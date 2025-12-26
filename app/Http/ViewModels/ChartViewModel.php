<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ChartViewModel.php
 * @date   2020-10-30 5:40:29
 */

namespace App\Http\ViewModels;

use App\Casts\ArrayObject;
use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Models\Merchant;
use App\Models\ModelInterface;
use DateInterval;
use DateTime;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class ChartViewModel extends ViewModelBase {
	private const MONTHS = [
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December',
	];

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): self {
		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $audit): bool {
		return false;
	}

	public function delete(Request $request, ModelInterface $audit): Redirector|RedirectResponse {
		return false;
	}

	public function new(FormRequestInterface $request): mixed {
		return false;
	}

	public function getMonthlyPayment(Request $request, ?Merchant $merchant = null) {
		$results = new ArrayObject([]);

		$date = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P1D'));

		$results->set('data.credit_debit_sale_today', $this->getCreditDebitSale($date->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_void_sale_today', $this->getCreditDebitVoidSale($date->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_refund_today', $this->getCreditDebitRefund($date->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_reversal_today', $this->getCreditDebitReversal($date->format('Y-m-d'), $merchant));

		$results->set('data.credit_debit_sale_yesterday', $this->getCreditDebitSale($previous->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_void_sale_yesterday', $this->getCreditDebitVoidSale($previous->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_refund_yesterday', $this->getCreditDebitRefund($previous->format('Y-m-d'), $merchant));
		$results->set('data.credit_debit_reversal_yesterday', $this->getCreditDebitReversal($previous->format('Y-m-d'), $merchant));

		$results->set('data.credit_debit_sale', $this->appendResult($this->getCreditDebitSaleMonthly($merchant)));
		$results->set('data.credit_debit_void_sale', $this->appendResult($this->getCreditDebitVoidSaleMonthly($merchant)));
		$results->set('data.credit_debit_refund', $this->appendResult($this->getCreditDebitRefundMonthly($merchant)));
		$results->set('data.credit_debit_reversal', $this->appendResult($this->getCreditDebitReversalMonthly($merchant)));

		return $results->__toArray();
	}

	private function getCreditDebitSale($date, ?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
			//$query->where('trx_report.trx_type', '=', "SALE");//->unionAll($batch);
		});

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.batch', 'sale'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.batch', 'sale'))
		           ->fromRaw($tables)
		           ->selectRaw('sale.trx_type as trx_type')
		           ->selectRaw('COUNT(sale.trx_type) as total')
		           ->selectRaw('SUM(sale.base_amount) as amount')
		           ->selectRaw('DATE(sale.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = sale.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = sale.batch_group')
		           ->whereRaw('userDevice.username = sale.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = sale.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('sale.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$sql = $query->newQuery()
		             ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		             ->selectRaw('SUM(u.total) as total')
		             ->selectRaw('SUM(u.amount) as amount')
		             ->selectRaw('MONTH(u._date) as month')
		             ->where('u.trx_type', '=', 'SALE')
		             ->whereDate('u._date', '=', $date)
		             ->groupBy('u.trx_type', 'month')
		             ->toSql();

		$params = [
			null,
			null,
			'SALE',
			$date,
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function trxReport(callable $callback): QueryBuilder {
		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.settlement', 'settlement'),
				table('credit_debit.trx_report', 'trx_report'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$query = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.trx_report', 'trx_report'))
		           ->fromRaw($tables)
		           ->selectRaw('trx_report.trx_type as trx_type')
		           ->selectRaw('COUNT(trx_report.trx_type) as total')
		           ->selectRaw('SUM(trx_report.base_amount) as amount')
		           ->selectRaw('DATE(trx_report.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = trx_report.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = trx_report.batch_group')
		           ->whereRaw('userDevice.username = trx_report.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = trx_report.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->whereRaw('trx_report.batch_num = settlement.batch_num')
		           ->whereRaw('settlement.batch_group = trx_report.batch_group')
		           ->whereRaw('settlement.username = trx_report.username')
		           ->whereRaw('settlement.trx_channel = trx_report.trx_channel')
			//->where('trx_report.trx_type', '=', "$type")
			//->whereDate('trx_report.created_at', '>=', $previous->format('Y-m-d'))
			//->whereDate('trx_report.created_at', '<=', $now->format('Y-m-d'))
			       ->groupByRaw('trx_report.trx_type')
		           ->groupBy('_date');
		if ($callback) $callback($query);

		return $query;
	}

	private function appendResult(array $transactions): Collection {
		$data = [];

		for ($i = 0; $i <= 11; $i++) {
			$date = \DateTime::createFromFormat('j-M-Y', '1-' . date('M-Y'));
			$month = $date->sub(new \DateInterval("P" . $i . "M"));
			$n = $month->format('n');
			$m = [
				'name'       => self::MONTHS[$month->format('n') - 1] . $month->format(' Y'),
				'year'       => $month->format(' Y'),
				'month'      => $month->format('n'),
				'total'      => 0,
				'month_name' => self::MONTHS[$month->format('n') - 1],
			];
			$data[] = $m;
		}

		foreach ($transactions as $trx) {
			foreach ($data as &$d) {
				if ($trx->month == $d['month']) {
					$d['total'] = (int)$trx->total;
					$d['amount'] = $this->numberFormat($trx->amount);
					$d['month_name'] = self::MONTHS[$trx->month - 1];
				}
			}
		}

		return $this->fill($data);
	}

	private function fill($data): Collection {
		$total = count($data);

		if ($total < 12) {
			if (isset($data[$total - 1])) {
				$lastMonth = $data[$total - 1]['month'];
			}
			else $lastMonth = 13;

			for ($i = 0; $i < (12 - $total); $i++) {
				--$lastMonth;
				if ($lastMonth < 1) $lastMonth = 12;

				$data[count($data)] = array(
					'total'      => 0,
					'month'      => $lastMonth,
					'month_name' => self::MONTHS[$lastMonth - 1],
				);
			}
		}

		return collect($data)->reverse();
	}

	private function getCreditDebitSaleMonthly(?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
			//$query->where('trx_report.trx_type', '=', "SALE");//->unionAll($batch);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.batch', 'sale'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.batch', 'sale'))
		           ->fromRaw($tables)
		           ->selectRaw('sale.trx_type as trx_type')
		           ->selectRaw('COUNT(sale.trx_type) as total')
		           ->selectRaw('SUM(sale.base_amount) as amount')
		           ->selectRaw('DATE(sale.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = sale.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = sale.batch_group')
		           ->whereRaw('userDevice.username = sale.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = sale.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('sale.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$sql = $query->newQuery()
		             ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		             ->selectRaw('SUM(u.total) as total')
		             ->selectRaw('SUM(u.amount) as amount')
		             ->selectRaw('MONTH(u._date) as month')
		             ->where('u.trx_type', '=', 'SALE')
		             ->whereDate('u._date', '>=', $previous->format('Y-m-d'))
		             ->whereDate('u._date', '<=', $now->format('Y-m-d'))
		             ->groupBy('u.trx_type', 'month')
		             ->toSql();

		$params = [
			null,
			null,
			'SALE',
			$previous->format('Y-m-d'),
			$now->format('Y-m-d'),
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		//print_r($sql);

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitVoidSaleMonthly(?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.void', 'void'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.void', 'void'))
		           ->fromRaw($tables)
		           ->selectRaw('void.trx_type as trx_type')
		           ->selectRaw('COUNT(void.trx_type) as total')
		           ->selectRaw('SUM(void.base_amount) as amount')
		           ->selectRaw('DATE(void.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = void.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = void.batch_group')
		           ->whereRaw('userDevice.username = void.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = void.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('void.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', '=', 'VOID_SALE')
		               ->whereDate('u._date', '>=', $previous->format('Y-m-d'))
		               ->whereDate('u._date', '<=', $now->format('Y-m-d'))
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			null,
			null,
			'VOID_SALE',
			$previous->format('Y-m-d'),
			$now->format('Y-m-d'),
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitRefundMonthly(?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			//$query->where('trx_report.trx_type', '=', "REFUND");
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.batch', 'sale'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.batch', 'sale'))
		           ->fromRaw($tables)
		           ->selectRaw('sale.trx_type as trx_type')
		           ->selectRaw('COUNT(sale.trx_type) as total')
		           ->selectRaw('SUM(sale.base_amount) as amount')
		           ->selectRaw('DATE(sale.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = sale.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = sale.batch_group')
		           ->whereRaw('userDevice.username = sale.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = sale.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('sale.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', '=', 'REFUND')
		               ->whereDate('u._date', '>=', $previous->format('Y-m-d'))
		               ->whereDate('u._date', '<=', $now->format('Y-m-d'))
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			null,
			null,
			'REFUND',
			$previous->format('Y-m-d'),
			$now->format('Y-m-d'),
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitRefund($date, ?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			//$query->where('trx_report.trx_type', '=', "REFUND");
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.batch', 'sale'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.batch', 'sale'))
		           ->fromRaw($tables)
		           ->selectRaw('sale.trx_type as trx_type')
		           ->selectRaw('COUNT(sale.trx_type) as total')
		           ->selectRaw('SUM(sale.base_amount) as amount')
		           ->selectRaw('DATE(sale.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = sale.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = sale.batch_group')
		           ->whereRaw('userDevice.username = sale.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = sale.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('sale.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', '=', 'REFUND')
		               ->whereDate('u._date', '=', $date)
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			null,
			null,
			'REFUND',
			$date,
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitReversalMonthly(?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			$query->where('trx_report.trx_type', 'LIKE', "REVERSAL%");
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.success_reversal', 'success'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);
		$success = DB::connection(connection('credit_debit'))
		             ->table(table('credit_debit.success_reversal', 'success'))
		             ->fromRaw($tables)
		             ->selectRaw('"SUCCESS_REVERSAL" as trx_type')
		             ->selectRaw('COUNT(success.base_amount) as total')
		             ->selectRaw('SUM(success.base_amount) as amount')
		             ->selectRaw('DATE(success.db_timestamp) as _date')
		             ->whereRaw('m_mid.mid = success.mid')
		             ->whereRaw('m_mid.merchant_id = merchant.id')
		             ->whereRaw('m_mid.batch_group_id = success.batch_group')
		             ->whereRaw('userDevice.username = success.username')
		             ->whereRaw('userDevice.merchant_id = merchant.id')
		             ->whereRaw('batch_group.batch_group = success.batch_group')
		             ->whereRaw('batch_group.bank_id_acq = bank.id')
		             ->groupByRaw('trx_type')
		             ->groupByRaw('_date');

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.pending_reversal', 'pending'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);
		$pending = DB::connection(connection('credit_debit'))
		             ->table(table('credit_debit.pending_reversal', 'pending'))
		             ->fromRaw($tables)
		             ->selectRaw('"PENDING_REVERSAL" as trx_type')
		             ->selectRaw('COUNT(pending.base_amount) as total')
		             ->selectRaw('SUM(pending.base_amount) as amount')
		             ->selectRaw('DATE(pending.db_timestamp) as _date')
		             ->whereRaw('m_mid.mid = pending.mid')
		             ->whereRaw('m_mid.merchant_id = merchant.id')
		             ->whereRaw('m_mid.batch_group_id = pending.batch_group')
		             ->whereRaw('userDevice.username = pending.username')
		             ->whereRaw('userDevice.merchant_id = merchant.id')
		             ->whereRaw('batch_group.batch_group = pending.batch_group')
		             ->whereRaw('batch_group.bank_id_acq = bank.id')
		             ->groupByRaw('trx_type')
		             ->groupByRaw('_date');

		if ($merchant) {
			$success->where('merchant.id', '=', $merchant->id);
			$pending->where('merchant.id', '=', $merchant->id);
		}

		$query = $query->unionAll($success)->unionAll($pending);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', 'LIKE', '%REVERSAL%')
		               ->whereDate('u._date', '>=', $previous->format('Y-m-d'))
		               ->whereDate('u._date', '<=', $now->format('Y-m-d'))
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			'%REVERSAL%',
			null,
			null,
			null,
			'%REVERSAL%',
			$previous->format('Y-m-d'),
			$now->format('Y-m-d'),
		];

		if ($merchant) {
			$params[1] = $merchant->id;
			$params[2] = $merchant->id;
			$params[3] = $merchant->id;
		}
		else {
			unset($params[1]);
			unset($params[2]);
			unset($params[3]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitReversal($date, ?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			$query->where('trx_report.trx_type', 'LIKE', "REVERSAL%");
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.success_reversal', 'success'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);
		$success = DB::connection(connection('credit_debit'))
		             ->table(table('credit_debit.success_reversal', 'success'))
		             ->fromRaw($tables)
		             ->selectRaw('"SUCCESS_REVERSAL" as trx_type')
		             ->selectRaw('COUNT(success.base_amount) as total')
		             ->selectRaw('SUM(success.base_amount) as amount')
		             ->selectRaw('DATE(success.db_timestamp) as _date')
		             ->whereRaw('m_mid.mid = success.mid')
		             ->whereRaw('m_mid.merchant_id = merchant.id')
		             ->whereRaw('m_mid.batch_group_id = success.batch_group')
		             ->whereRaw('userDevice.username = success.username')
		             ->whereRaw('userDevice.merchant_id = merchant.id')
		             ->whereRaw('batch_group.batch_group = success.batch_group')
		             ->whereRaw('batch_group.bank_id_acq = bank.id')
		             ->groupByRaw('trx_type')
		             ->groupByRaw('_date');

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.pending_reversal', 'pending'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);
		$pending = DB::connection(connection('credit_debit'))
		             ->table(table('credit_debit.pending_reversal', 'pending'))
		             ->fromRaw($tables)
		             ->selectRaw('"PENDING_REVERSAL" as trx_type')
		             ->selectRaw('COUNT(pending.base_amount) as total')
		             ->selectRaw('SUM(pending.base_amount) as amount')
		             ->selectRaw('DATE(pending.db_timestamp) as _date')
		             ->whereRaw('m_mid.mid = pending.mid')
		             ->whereRaw('m_mid.merchant_id = merchant.id')
		             ->whereRaw('m_mid.batch_group_id = pending.batch_group')
		             ->whereRaw('userDevice.username = pending.username')
		             ->whereRaw('userDevice.merchant_id = merchant.id')
		             ->whereRaw('batch_group.batch_group = pending.batch_group')
		             ->whereRaw('batch_group.bank_id_acq = bank.id')
		             ->groupByRaw('trx_type')
		             ->groupByRaw('_date');

		if ($merchant) {
			$success->where('merchant.id', '=', $merchant->id);
			$pending->where('merchant.id', '=', $merchant->id);
		}

		$query = $query->unionAll($success)->unionAll($pending);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', 'LIKE', '%REVERSAL%')
		               ->whereDate('u._date', '=', $date)
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			'%REVERSAL%',
			null,
			null,
			null,
			'%REVERSAL%',
			$date,
		];

		if ($merchant) {
			$params[1] = $merchant->id;
			$params[2] = $merchant->id;
			$params[3] = $merchant->id;
		}
		else {
			unset($params[1]);
			unset($params[2]);
			unset($params[3]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	private function getCreditDebitVoidSale($date, ?Merchant $merchant = null) {
		$query = $this->trxReport(function (QueryBuilder $query) use ($merchant) {
			if ($merchant) $query->where('merchant.id', '=', $merchant->id);
		});

		$now = new DateTime();
		$previous = (new DateTime())->sub(new DateInterval('P11M'));
		$tables = implode(',',
			[
				table('credit_debit.merchant_mid', 'm_mid'),
				table('credit_debit.void', 'void'),
				table('credit_debit.batch_group_list', 'batch_group'),
				table('master.merchant', 'merchant'),
				table('master.bank', 'bank'),
				table('master.mobile_app_users', 'userDevice'),
			]);

		$batch = DB::connection(connection('credit_debit'))
		           ->table(table('credit_debit.void', 'void'))
		           ->fromRaw($tables)
		           ->selectRaw('void.trx_type as trx_type')
		           ->selectRaw('COUNT(void.trx_type) as total')
		           ->selectRaw('SUM(void.base_amount) as amount')
		           ->selectRaw('DATE(void.db_timestamp) as _date')
		           ->whereRaw('m_mid.mid = void.mid')
		           ->whereRaw('m_mid.merchant_id = merchant.id')
		           ->whereRaw('m_mid.batch_group_id = void.batch_group')
		           ->whereRaw('userDevice.username = void.username')
		           ->whereRaw('userDevice.merchant_id = merchant.id')
		           ->whereRaw('batch_group.batch_group = void.batch_group')
		           ->whereRaw('batch_group.bank_id_acq = bank.id')
		           ->groupByRaw('void.trx_type')
		           ->groupByRaw('_date');

		if ($merchant) {
			$batch->where('merchant.id', '=', $merchant->id);
		}
		$query = $query->unionAll($batch);
		$query = $query->newQuery()
		               ->fromRaw(sprintf("(%s) as u", $query->toSql()))
		               ->selectRaw('SUM(u.total) as total')
		               ->selectRaw('SUM(u.amount) as amount')
		               ->selectRaw('MONTH(u._date) as month')
		               ->where('u.trx_type', '=', 'VOID_SALE')
		               ->whereDate('u._date', '=', $date)
		               ->groupBy('u.trx_type', 'month');
		$sql = $query->toSql();

		$params = [
			null,
			null,
			'VOID_SALE',
			$date,
		];

		if ($merchant) {
			$params[0] = $merchant->id;
			$params[1] = $merchant->id;
		}
		else {
			unset($params[0]);
			unset($params[1]);
		}

		return DB::select($sql, collect($params)->values()->toArray());
	}

	/**
	 * @return Collection
	 * @throws \Exception
	 */
	private function months(): Collection {
		$months = [];

		for ($i = 0; $i <= 11; $i++) {
			$date = DateTime::createFromFormat('j-M-Y', '1-' . date('M-Y'));
			$month = $date->sub(new DateInterval("P" . $i . "M"));
			$m = [
				'name'  => self::MONTHS[$month->format('n') - 1] . $month->format(' Y'),
				'month' => $month->format('n'),
			];
			$months[] = $m;
		}

		return collect($months)->reverse();

	}
}
