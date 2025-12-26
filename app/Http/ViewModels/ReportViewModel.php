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
 * @file   ReportViewModel.php
 * @date   2020-10-30 5:40:29
 */

namespace App\Http\ViewModels;

use App\Libraries\XLS;
use App\Http\Requests\FormRequestInterface;
use App\Http\Requests\ReportFormRequest;
use App\Http\ViewModels\ViewModelBase;
use App\Models\CreditDebit\TransactionReport;
use App\Models\ModelInterface;
use App\Models\QR\TransactionHistory;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ReportViewModel extends ViewModelBase {
	public ?string $url = null;

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): self {
		$this->form = $this->formBuilder->create($formClass, $options);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route));

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

	public function unsettledUrl() {
		$fields = $this->getFormFields();

		return route('api.report.unsettled', $fields->toArray());
	}

	public function settlementSummaryUrl() {
		$fields = $this->getFormFields();

		return route('api.report.settlement.summary', $fields->toArray());
	}

	public function settlementDetailUrl() {
		$fields = $this->getFormFields();

		return route('api.report.settlement.detail', $fields->toArray());
	}

	public function paymentType() {
		$fields = $this->getFormFields();

		return $fields->get('payment_type', 'cdcp');
	}

	public function searchUnsettled(ReportFormRequest $request) {
		$this->form->setRequest($request);
		$fields = collect($request->all());
		$columns = [];

		if ($fields->get('payment_type') == 'cdcp') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'db_timestamp', 'DESC');
			list($columns, $query) = $this->unsettledCreditDebit($request, $fields);
		}

		if ($fields->get('payment_type') == 'qr') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'create_at', 'DESC');
			list($columns, $query) = $this->unsettledQR($request, $fields);

		}

		$results = $query->paginate($limit, $columns, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		$self = $this;
		return $this->prepareForResponse($results, $offset)->map(function ($values, $key) use ($self) {
			if ($key == 'rows') {
				return collect($values)->map(function ($row) use ($self) {
					return collect($row)->map(function ($value, $field) use ($self, $row) {
						if ($field == 'base_amount') $value = $self->moneyFormat($value);
						if ($field == 'tip_amount') $value = $self->moneyFormat($value);
						if ($field == 'is_debit_flag') $value = $value ? 'Yes' : 'No';
						if ($field == 'is_on_us_flag') $value = $value ? 'Yes' : 'No';
						if ($field == 'trx_type') $value = Str::of(strtolower($value))->replace('_', ' ')->title()->__toString();
						if ($field == 'merchant_name') $value = $self->createLink($value, route('merchant.show', ['merchant' => $row['merchant_id']]));
						if ($field == 'username') $value = $self->createLink($value, route('user_device.show', ['user_device' => $value]));

						return $value;
					});
				});
			}

			return $values;
		});
	}

	private function unsettledCreditDebit(ReportFormRequest $request, Collection $fields) {
		$trxType = $fields->get('cdcp_trx_type');
		$cardNumber = $fields->get('card_number');
		$amount = $fields->get('amount');
		$tid = $fields->get('tid');
		$mid = $fields->get('mid');
		$from = $fields->get('from_date');
		$to = $fields->get('to_date');
		$merchant = $fields->get('merchant');
		$invoice = $fields->get('invoice');
		$columns = [
			'username',
			'trx_type',
			'base_amount',
			'tip_amount',
			'db_timestamp',
			'mid',
			'tid',
			'bin_result',
			'invoice_num',
			'rrn',
			'batch_group',
			'track_2_data',
			'stan',
		];

		$sale = DB::connection(connection('credit_debit'))
		          ->table(table('credit_debit.batch'))
		          ->select(array_merge($columns, ['is_on_us_flag', 'is_debit_flag', 'approval_code']))
		          ->selectRaw("((base_amount * bank_mdr_value ) / 100 ) AS DISC_AMOUNT")
		          ->selectRaw("(base_amount - ((base_amount * bank_mdr_value) / 100)) AS NET_AMOUNT")
		          ->selectRaw("CONCAT(substr(track_2_data, 1, 6), '******', substr(track_2_data, 13, 4)) AS CARD_NO")
		          ->selectRaw("(base_amount - ((base_amount * merchant_mdr_value) / 100)) AS TRF_AMOUNT")
		          ->selectRaw("(base_amount * (format((merchant_mdr_value - bank_mdr_value), 2 ) / 100 ) ) AS FEE")
		          ->selectRaw("bank_mdr_value")
		          ->selectRaw("merchant_mdr_value")
		          ->selectRaw("trx_host_date AS host_date")
		          ->selectRaw("trx_host_time AS host_time");
		$void = DB::connection(connection('credit_debit'))
		          ->table(table('credit_debit.void'))
		          ->select(array_merge($columns, ['is_on_us_flag', 'is_debit_flag', 'approval_code']))
		          ->selectRaw('"0" AS DISC_AMOUNT')
		          ->selectRaw('"0" AS NET_AMOUNT')
		          ->selectRaw("CONCAT(substr(track_2_data, 1, 6), '******', substr(track_2_data, 13, 4)) AS CARD_NO")
		          ->selectRaw('"0" AS TRF_AMOUNT')
		          ->selectRaw('"0" AS FEE')
		          ->selectRaw('bank_mdr_value')
		          ->selectRaw('merchant_mdr_value')
		          ->selectRaw('void_host_date AS host_date')
		          ->selectRaw('void_host_time AS host_time');
		$pendingReversal = DB::connection(connection('credit_debit'))
		                     ->table(table('credit_debit.pending_reversal'))
		                     ->select($columns)
		                     ->selectRaw('"0" as approval_code')
		                     ->selectRaw('"0" as is_on_us_flag')
		                     ->selectRaw('"0" as is_debit_flag')
		                     ->selectRaw('"0" AS DISC_AMOUNT')
		                     ->selectRaw('"0" AS NET_AMOUNT')
		                     ->selectRaw("CONCAT(substr(track_2_data, 1, 6), '******', substr(track_2_data, 13, 4)) AS CARD_NO")
		                     ->selectRaw('"0" AS TRF_AMOUNT')
		                     ->selectRaw('"0" AS FEE')
		                     ->selectRaw('"0" AS bank_mdr_value')
		                     ->selectRaw('"0" AS merchant_mdr_value')
		                     ->selectRaw('DATE_FORMAT(db_timestamp, "%m%d") AS host_date')
		                     ->selectRaw('DATE_FORMAT(db_timestamp, "%H%i%s") AS host_time');
		$successReversal = DB::connection(connection('credit_debit'))
		                     ->table(table('credit_debit.success_reversal'))
		                     ->select($columns)
		                     ->selectRaw('"0" as approval_code')
		                     ->selectRaw('"0" as is_on_us_flag')
		                     ->selectRaw('"0" as is_debit_flag')
		                     ->selectRaw('"0" AS DISC_AMOUNT')
		                     ->selectRaw('"0" AS NET_AMOUNT')
		                     ->selectRaw("CONCAT(substr(track_2_data, 1, 6), '******', substr(track_2_data, 13, 4)) AS CARD_NO")
		                     ->selectRaw('"0" AS TRF_AMOUNT')
		                     ->selectRaw('"0" AS FEE')
		                     ->selectRaw('"0" AS bank_mdr_value')
		                     ->selectRaw('"0" AS merchant_mdr_value')
		                     ->selectRaw('DATE_FORMAT(db_timestamp, "%m%d") AS host_date')
		                     ->selectRaw('DATE_FORMAT(db_timestamp, "%H%i%s") AS host_time');
		$query = DB::connection(connection('credit_debit'))
		           ->table($sale)
		           ->unionAll($void)
		           ->unionAll($pendingReversal)
		           ->unionAll($successReversal);

		$tables = implode(',',
			[
				table('credit_debit.merchant_mid') . ' as m_mid',
				//table('credit_debit.merchant_mdr') . ' as m_mdr',
				//table('credit_debit.bank_mdr') . ' as b_mdr',
				table('credit_debit.batch_group_list') . ' as b_batch_group',
				table('master.bank') . ' as bank',
				table('master.merchant') . ' as m_merchant',
				'(' . $query->toSql() . ') as x',
			]);

		$query = DB::connection(connection('credit_debit'))->table($query->toSql(), 'x')
		           ->fromRaw($tables)
		           ->whereRaw('m_mid.mid = x.mid')
		           //->whereRaw('x.is_on_us_flag = b_mdr.is_on_us')
		           //->whereRaw('x.is_debit_flag = b_mdr.is_debit')
		           //->whereRaw('x.is_on_us_flag = m_mdr.is_on_us')
		           //->whereRaw('x.is_debit_flag = m_mdr.is_debit')
		           ->whereRaw('b_batch_group.batch_group = x.batch_group')
		           ->whereRaw('m_mid.batch_group_id = x.batch_group')
		           //->whereRaw('m_merchant.id = m_mdr.merchant_id')
		           //->whereRaw('bank.id = b_mdr.bank_id')
		           ->whereRaw('b_batch_group.bank_id_acq = bank.id')
		           ->whereRaw('m_mid.merchant_id = m_merchant.id');
		if ($trxType) $query->where('x.trx_type', 'LIKE', "$trxType%");
		if ($amount) $query->where('x.base_amount', '=', $amount);
		if ($from) $query->whereDate('x.db_timestamp', '>=', $from);
		if ($to) $query->whereDate('x.db_timestamp', '<=', $to);
		if ($cardNumber) $query->where('x.track_2_data', 'LIKE', "%$cardNumber%D%");
		if ($mid) $query->where('x.mid', '=', "$mid");
		if ($tid) $query->where('x.tid', '=', "$tid");
		if ($merchant) $query->where('m_merchant.id', '=', "$merchant");
		if ($invoice) $query->where('x.invoice_num', '=', "$invoice");

		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'db_timestamp', 'DESC');
		$query->orderBy($sort, $order);
		//$query->groupBy('x.invoice_num');
		//print_r($query->toSql());

		$columns = [
			"username",
			"trx_type",
			"base_amount",
			"db_timestamp",
			"mid",
			"tid",
			"bin_result",
			"invoice_num",
			"rrn",
			"batch_group",
			"is_on_us_flag",
			"is_debit_flag",
			"approval_code",
			"merchant_id",
			"merchant_name",
			"bank_id",
			"bank_name",
			"bank_mdr",
			"merchant_mdr",
		];

		$query->select([
			'x.*',
			'm_merchant.id as merchant_id',
			'm_merchant.name as merchant_name',
			'bank.id as bank_id',
			'bank.name as bank_name',
			//'b_mdr.value as bank_mdr',
			//'m_mdr.value as merchant_mdr',
			'x.bank_mdr_value as bank_mdr',
			'x.merchant_mdr_value as merchant_mdr',
		]);

		return [$columns, $query];
	}

	private function unsettledQR(ReportFormRequest $request, Collection $fields) {
		$trxType = $fields->get('qr_trx_type');
		$amount = $fields->get('amount');
		$from = $fields->get('from_date');
		$to = $fields->get('to_date');
		$merchant = $fields->get('merchant');
		$invoice = $fields->get('invoice');
		$columns = [
			'username',
			'qr_pay_app_id',
			'batch_group_id',
			'trx_channel_id',
			'base_amount',
			'stan',
			'invoice_num',
			'create_at',
		];
		$mdr = [
			'issuer_name',
			'rrn',
			'approval_code',
			'batch_group_mdr_value',
			'merchant_mdr_value',
			'sales_mdr_value',
			'batch_group_admin_fee',
			'merchant_admin_fee',
			'sales_admin_fee',
		];

		$tables = function (string $aliasPrefix, $appendTable) {
			return [
				table('qr.qr_pay_app') . " as {$aliasPrefix}1",
				table('qr.trx_channel') . " as {$aliasPrefix}2",
				table('qr.batch_group') . " as {$aliasPrefix}3",
				table('master.mobile_app_users') . " as {$aliasPrefix}4",
				table('master.merchant') . " as {$aliasPrefix}5",
				table('qr.merchant_mdr') . " as {$aliasPrefix}6",
				"$appendTable as {$aliasPrefix}",
			];
		};

		$successPayment = get_table('qr.success_payment')
			->fromRaw(implode(',', $tables('a', table('qr.success_payment'))))
			->select(collect(array_merge($columns, $mdr))->map(fn($v) => "a.$v")->toArray())
			->selectRaw('a1.name as qr_pay_app')
			->selectRaw('a5.name as merchant_name')
			->selectRaw('"SUCCESS_PAYMENT" as trx_type')
			->selectRaw('a3.name as batch_group')
			->selectRaw('a2.name as trx_channel')
			->selectRaw('(a.merchant_mdr_value / 100) * a.base_amount as merchant_mdr')
			->selectRaw('(a.batch_group_mdr_value / 100) * a.base_amount as batch_group_mdr')
			->selectRaw('(a.sales_mdr_value / 100) * a.base_amount as sales_mdr')
			->selectRaw('(a.batch_group_admin_fee + a.merchant_admin_fee + a.sales_admin_fee) * a.base_amount as total_fee')
			->whereRaw('a1.id = a.qr_pay_app_id')
			->whereRaw('a.trx_channel_id = a2.id')
			->whereRaw('a.batch_group_id = a2.batch_group_id')
			->whereRaw('a.batch_group_id = a3.id')
			->whereRaw('a.username = a4.username')
			->whereRaw('a4.merchant_id = a5.id')
			->whereRaw('a6.batch_group_id = a3.id')
			->whereRaw('a6.merchant_id = a5.id');

		$pendingPayment = get_table('qr.pending_payment')
			->fromRaw(implode(',', $tables('b', table('qr.pending_payment'))))
			->select(collect($columns)->map(fn($v) => "b.$v")->toArray())
			->selectRaw('"" as issuer_name')
			->selectRaw('"0" as rrn')
			->selectRaw('"0" as approval_code')
			->selectRaw('"0" as batch_group_mdr_value')
			->selectRaw('"0" as merchant_mdr_value')
			->selectRaw('"0" as sales_mdr_value')
			->selectRaw('"0" as batch_group_admin_fee')
			->selectRaw('"0" as merchant_admin_fee')
			->selectRaw('"0" as sales_admin_fee')
			->selectRaw('b1.name as qr_pay_app')
			->selectRaw('b5.name as merchant_name')
			->selectRaw('IF(expired_flag=1, "EXPIRED_PENDING_PAYMENT", "PENDING_PAYMENT") as trx_type')
			->selectRaw('b3.name as batch_group')
			->selectRaw('b2.name as trx_channel')
			->selectRaw('"0" as merchant_mdr')
			->selectRaw('"0" as batch_group_mdr')
			->selectRaw('"0" as sales_mdr')
			->selectRaw('"0" as total_fee')
			->whereRaw('b1.id = b.qr_pay_app_id')
			->whereRaw('b.trx_channel_id = b2.id')
			->whereRaw('b.batch_group_id = b2.batch_group_id')
			->whereRaw('b.batch_group_id = b3.id')
			->whereRaw('b.username = b4.username')
			->whereRaw('b4.merchant_id = b5.id')
			->whereRaw('b6.batch_group_id = b3.id')
			->whereRaw('b6.merchant_id = b5.id');

		$refund = get_table('qr.refund')
			->fromRaw(implode(',', $tables('c', table('qr.refund'))))
			->select(collect(array_merge($columns, $mdr))->map(fn($v) => "c.$v")->toArray())
			->selectRaw('c1.name as qr_pay_app')
			->selectRaw('c5.name as merchant_name')
			->selectRaw('"REFUND" as trx_type')
			->selectRaw('c3.name as batch_group')
			->selectRaw('c2.name as trx_channel')
			->selectRaw('(c.merchant_mdr_value / 100) * c.base_amount as merchant_mdr')
			->selectRaw('(c.batch_group_mdr_value / 100) * c.base_amount as batch_group_mdr')
			->selectRaw('(c.sales_mdr_value / 100) * c.base_amount as sales_mdr')
			->selectRaw('(c.batch_group_admin_fee + c.merchant_admin_fee + c.sales_admin_fee) * c.base_amount as total_fee')
			->whereRaw('c1.id = c.qr_pay_app_id')
			->whereRaw('c.trx_channel_id = c2.id')
			->whereRaw('c.batch_group_id = c2.batch_group_id')
			->whereRaw('c.batch_group_id = c3.id')
			->whereRaw('c.username = c4.username')
			->whereRaw('c4.merchant_id = c5.id')
			->whereRaw('c6.batch_group_id = c3.id')
			->whereRaw('c6.merchant_id = c5.id');

		$query = QR()->table($successPayment)
		             ->unionAll($pendingPayment)
		             ->unionAll($refund);

		$sql = $query->toSql();
		$query = DB::query()->fromRaw("({$sql}) as u");

		if ($trxType) $query->where('u.trx_type', 'LIKE', "$trxType%");
		if ($amount) $query->where('u.base_amount', '=', $amount);
		if ($from) $query->whereDate('u.create_at', '>=', $from);
		if ($to) $query->whereDate('u.create_at', '<=', $to);
		if ($merchant) $query->where('m_merchant.id', '=', "$merchant");
		if ($invoice) $query->where('u.invoice_num', '=', "$invoice");
		//print_r($query->toSql());
		$columns = array_merge($columns,
			$mdr,
			['qr_pay_app', 'merchant_name', 'trx_type', 'batch_group', 'trx_channel', 'merchant_mdr', 'batch_group_mdr', 'sales_mdr', 'total_fee']);

		return [$columns, $query];
	}

	public function searchSettlementSummary(ReportFormRequest $request) {
		$this->form->setRequest($request);
		$fields = collect($request->all());
		$columns = [];
		$results = [];
		$self = $this;
		$offset = 0;
		$limit = 0;

		if ($fields->get('payment_type') == 'cdcp') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'db_timestamp', 'DESC');
			list($columns, $query) = $this->settlementCreditDebit($request, $fields);
			$results = $query->paginate($limit, $columns, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
			                 ->toArray();
			$results = collect($results)->map(function ($result, $key) use ($self) {
				if ($key == 'data') {
					return collect($result)->map(function ($row) use ($self) {
						if (isset($row->merchant_name)) $row->merchant_name = $self->createLink($row->merchant_name, route('merchant.show', ['merchant' => $row->merchant_id]));
						if (isset($row->bank_name)) $row->bank_name = $self->createLink($row->bank_name, route('bank.show', ['bank' => $row->bank_id]));
						if (isset($row->total_amount)) $row->total_amount = $self->moneyFormat($row->total_amount);
						if (isset($row->incoming_amount)) $row->incoming_amount = $self->moneyFormat($row->incoming_amount);
						if (isset($row->transfer_amount)) $row->transfer_amount = $self->moneyFormat($row->transfer_amount);
						if (isset($row->transfer_fee)) $row->transfer_fee = $self->moneyFormat($row->transfer_fee);
						if (isset($row->total_transactions)) $row->total_transactions = $self->numberFormat($row->total_transactions);
						if (isset($row->fee)) $row->fee = $self->moneyFormat($row->fee);

						return $row;
					})->toArray();
				}

				return $result;
			})->toArray();
		}

		if ($fields->get('payment_type') == 'qr') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'create_at', 'DESC');
			list($columns, $query) = $this->settlementQR($request, $fields);
			$results = $query->paginate($limit, $columns, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
			                 ->toArray();
			$results = collect($results)->map(function ($result, $key) use ($self) {
				if ($key == 'data') {
					return collect($result)->map(function ($row) use ($self) {
						$row->merchant_name = $this->createLink($row->merchant_name, route('merchant.show', ['merchant' => $row->merchant_id]));
						$row->batch_group = $this->createLink($row->batch_group, route('qr.acquirer.show', ['acquirer' => $row->batch_group_id]));
						$row->total_transactions = $this->numberFormat($row->total_transactions);
						$row->total_amount = $this->moneyFormat($row->total_amount);
						$row->incoming_amount = $this->moneyFormat($row->incoming_amount);
						$row->transfer_amount = $this->moneyFormat($row->transfer_amount);
						$row->transfer_fee = $this->moneyFormat($row->transfer_fee);
						$row->fee = $this->moneyFormat($row->fee);
						$row->bank_name = $this->createLink($row->bank_name, route('bank.show', ['bank' => $row->bank_id]));

						return $row;
					})->toArray();
				}

				return $result;
			})->toArray();
		}

		return $this->prepareEachResultForResponse($results, $offset, false);
	}

	private function settlementCreditDebit(Request $request, Collection $fields) {
		$merchant = $fields->get('merchant');
		$from = DateTime::createFromFormat('Y-m-d', $fields->get('from_date'));
		$to = DateTime::createFromFormat('Y-m-d', $fields->get('to_date'));

		$t_settlement = get_table('credit_debit.settlement', 'a');
		$columns = [
			'merchant_name',
			'merchant_id',
			'proc_date',
			'settlement_date',
			'total_transactions',
			'total_amount',
			'incoming_amount',
			'transfer_amount',
			'transfer_fee',
			'clearing_code',
			'bank_name',
			'account_number',
			'fee',
			'mid',
		];

		$query = $t_settlement
			->selectRaw('d.`name` AS merchant_name')
			->selectRaw('d.id AS merchant_id')
			->selectRaw('a.mid')
			->selectRaw('a.batch_num AS batch_num')
			->selectRaw("DATE_FORMAT(a.`db_timestamp`, '%Y-%m-%d') AS proc_date")
			->selectRaw("DATE(CONCAT(DATE_FORMAT(a.`db_timestamp`, '%Y'),'-',SUBSTRING(a.`settlement_host_date`,1,2),'-',SUBSTRING(a.`settlement_host_date`,3,2))) as settlement_date")
			->selectRaw("COUNT(a.db_timestamp) AS total_transactions")
			->selectRaw("SUM(b.base_amount) AS total_amount")
			->selectRaw("ROUND(SUM((`b`.`base_amount` - ((`b`.`base_amount` * `b`.`bank_mdr_value`) / 100)))) AS incoming_amount")
			->selectRaw("ROUND(SUM((`b`.`base_amount` - ((`b`.`base_amount` * `b`.`merchant_mdr_value`) / 100)))) AS transfer_amount")
			->selectRaw("IF(d.transaction_bank_id='2',0,5000) AS transfer_fee")
			//->selectRaw("d.transaction_bank_clearing_code AS clearing_code")
			->selectRaw("(SELECT `name` FROM mw_master_db.bank WHERE id = d.transaction_bank_id ) as bank_name")
			->selectRaw("(SELECT `id` FROM mw_master_db.bank WHERE id = d.transaction_bank_id ) as bank_id")
			->selectRaw("d.transaction_bank_account_no AS account_number")
			->selectRaw("d.transaction_bank_on_behalf AS account_name")
			->selectRaw("ROUND(SUM((`b`.`base_amount` * ((`b`.`merchant_mdr_value` - `b`.`bank_mdr_value`) / 100)))) AS fee")
			->join(table('credit_debit.trx_report') . ' as b',
				function () {
				},
				null,
				null,
				'')
			->join(table('credit_debit.merchant_mid') . ' as c',
				function () {
				},
				null,
				null,
				'')
			->join(table('master.merchant') . ' as d',
				function (JoinClause $join) {
					$join->on('c.merchant_id', '=', 'd.id')
					     ->on("a.mid", '=', 'b.mid')
					     ->on("a.tid", '=', 'b.tid')
					     ->on("a.batch_group", '=', 'b.batch_group')
					     ->on("a.batch_num", '=', 'b.batch_num')
					     ->on('b.mid', '=', 'c.mid')
					     ->on('b.batch_group', '=', 'c.batch_group_id');
				},
				null,
				null,
				'')
			->where('b.trx_type', '=', 'SALE')
			->whereRaw("(DATE(CONCAT(DATE_FORMAT(a.`db_timestamp`, '%Y'),'-',SUBSTRING(a.`settlement_host_date`,1,2),'-',SUBSTRING(a.`settlement_host_date`,3,2))) >= DATE('{$from->format('Y-m-d')}') AND DATE(CONCAT(DATE_FORMAT(a.`db_timestamp`, '%Y'),'-',SUBSTRING(a.`settlement_host_date`,1,2),'-',SUBSTRING(a.`settlement_host_date`,3,2))) <= DATE('{$to->format('Y-m-d')}'))")
			->groupByRaw('d.id')
			->groupByRaw('a.mid')
			->groupByRaw('proc_date')
			->groupByRaw('settlement_date')
			//->groupByRaw('clearing_code')
			->groupByRaw('bank_name')
			->groupByRaw('a.batch_num')
			->groupByRaw('account_number');

		$user = Auth::user();
		if ($merchant) $query->whereRaw("d.id = '$merchant'");
		if ($user->hasRole('sales')) $query->whereRaw("AND d.sales_id = '{$user->id}'");

		return [$columns, $query];
	}

	private function settlementQR(Request $request, Collection $fields) {
		$merchant = $fields->get('merchant');
		$from = DateTime::createFromFormat('Y-m-d', $fields->get('from_date'));
		$to = DateTime::createFromFormat('Y-m-d', $fields->get('to_date'));

		$columns = ['username'];
		$empty = fn() => null;
		$query = get_table('qr.settlement', 'a')
			->selectRaw("`d`.name AS merchant_name")
			->selectRaw("`d`.id AS merchant_id")
			->selectRaw("`e`.name AS batch_group")
			->selectRaw("`a`.batch_group_id")
			->selectRaw("`a`.batch_num AS batch_num")
			->selectRaw("DATE_FORMAT(`a`.create_at, '%Y-%m-%d') AS proc_date")
			->selectRaw("`a`.create_at")
			->selectRaw("COUNT(`a`.create_at) AS total_transactions")
			->selectRaw("SUM(`b`.base_amount) AS total_amount")
			->selectRaw("(SUM(`b`.base_amount) - (`e`.admin_fee + ((`e`.mdr / 100) * SUM(`b`.base_amount)))) * SUM(`a`.num_of_success_payment) as incoming_amount")
			->selectRaw("(SUM(`b`.base_amount) - ((`f`.merchant_admin_fee + `e`.admin_fee + `f`.sales_admin_fee) * SUM(`a`.num_of_success_payment)) - (((`f`.merchant_mdr / 100) * SUM(`b`.base_amount)) + ((`e`.mdr / 100) * SUM(`b`.base_amount)) + ((`f`.sales_mdr / 100) * SUM(`b`.base_amount)))) as transfer_amount")
			->selectRaw("IF(`d`.transaction_bank_id = '4', 0, 5000) AS transfer_fee")
			->selectRaw("(SELECT name FROM " . table('master.bank') . " WHERE id = `d`.transaction_bank_id) AS bank_name")
			->selectRaw("(SELECT id FROM " . table('master.bank') . " WHERE id = `d`.transaction_bank_id) AS bank_id")
			->selectRaw("`d`.transaction_bank_account_no AS account_number")
			->selectRaw("`d`.transaction_bank_on_behalf AS account_name")
			->selectRaw("ROUND((SUM(`b`.base_amount) * ((`f`.merchant_mdr - `e`.mdr) / 100))) AS fee")
			->join(table('qr.trx_history', 'b'), $empty, null, null, '')
			->join(table('master.mobile_app_users', 'c'), $empty, null, null, '')
			->join(table('master.merchant', 'd'), $empty, null, null, '')
			->join(table('qr.batch_group', 'e'), $empty, null, null, '')
			->join(table('qr.merchant_mdr', 'f'),
				function (JoinClause $clause) {
					$clause->on("c.merchant_id", "=", "d.id")
					       ->on("f.batch_group_id", "=", "b.batch_group_id")
					       ->on("f.merchant_id", "=", "d.id")
					       ->on("a.batch_group_id", "=", "b.batch_group_id")
					       ->on("a.batch_num", "=", "b.batch_num")
					       ->on("b.batch_group_id", "=", "e.id")
					       ->on("c.username", "=", "a.username")
					       ->on("b.username", "=", "a.username")
					       ->on("b.username", "=", "a.username");
				},
				null,
				null,
				'')
			->where('b.trx_type', '=', 'SUCCESS_PAYMENT')
			->whereDate('a.create_at', '>=', $from->format('Y-m-d'))
			->whereDate('a.create_at', '<=', $to->format('Y-m-d'))
			->groupByRaw("`a`.batch_num")
			->groupByRaw("`f`.merchant_mdr")
			->groupByRaw("`f`.merchant_admin_fee")
			->groupByRaw("`f`.sales_mdr")
			->groupByRaw("`f`.sales_admin_fee")
			->groupByRaw("`d`.id")
			->groupByRaw("`a`.create_at")
			->groupByRaw("`e`.id");

		return [['*'], $query];
	}

	public function searchSettlementDetail(ReportFormRequest $request) {
		$fields = collect($request->all());
		$columns = [];
		$self = $this;

		if ($fields->get('payment_type') == 'cdcp') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'db_timestamp', 'DESC');
			list($columns, $query) = $this->settlementDetailCreditDebit($request, $fields);
			$results = $query->paginate($limit, $columns, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
			                 ->toArray();
			$results = collect($results)->map(function ($result, $key) use ($self) {
				if ($key == 'data') {
					$result = collect($result)->map(function ($row) use ($self) {
						$row['trx_channel'] = $self->createLink($row['trx_channel'], route('credit_debit.cp.channel.show', ['channel' => $row['trx_channel']]));
						$row['batch_group'] = $self->createLink($row['batch_group'], route('credit_debit.cp.acquirer.show', ['acquirer' => $row['batch_group']]));
						$row['mid']['merchant']['name'] = $self->createLink($row['mid']['merchant']['name'], route('merchant.show', ['merchant' => $row['mid']['merchant']['id']]));
						$row['username'] = $self->createLink($row['username'], route('user_device.show', ['user_device' => $row['username']]));
						$row['base_amount'] = $self->moneyFormat($row['base_amount']);
						$row['tip_amount'] = $self->moneyFormat($row['tip_amount']);
						$row['is_debit_flag'] = $row['is_debit_flag'] ? 'Yes' : 'No';
						$row['is_on_us_flag'] = $row['is_on_us_flag'] ? 'Yes' : 'No';
						$row['trx_type'] = Str::of(strtolower($row['trx_type']))->replace('_', ' ')->title()->__toString();
						$row['db_timestamp'] = (new DateTime($row['db_timestamp']))->format('Y-m-d H:i:s');

						return $row;
					});
				}

				return $result;
			})->toArray();
		}

		if ($fields->get('payment_type') == 'qr') {
			list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'create_at', 'DESC');
			list($columns, $query) = $this->settlementDetailQR($request, $fields);
			$results = $query->paginate($limit, $columns, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
			                 ->toArray();
			$results = collect($results)->map(function ($result, $key) use ($self) {
				if ($key == 'data') {
					$result = collect($result)->map(function ($row) use ($self) {
						//$row['trx_channel'] = $self->createLink($row['trx_channel'], route('credit_debit.cp.channel.show', ['channel' => $row['trx_channel']]));
						$row['batch_group'] = $self->createLink($row['batch_group']['name'], route('qr.acquirer.show', ['acquirer' => $row['batch_group_id']]));
						$row['user_device']['merchant']['name'] = $self->createLink($row['user_device']['merchant']['name'], route('merchant.show', ['merchant' => $row['user_device']['merchant']['id']]));
						$row['username'] = $self->createLink($row['username'], route('user_device.show', ['user_device' => $row['username']]));
						$row['base_amount'] = $self->moneyFormat($row['base_amount']);

						return $row;
					});
				}

				return $result;
			})->toArray();
		}

		return $this->prepareEachResultForResponse($results, $offset, false);
	}

	private function settlementDetailCreditDebit(Request $request, $fields) {
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request, 0, 25, 'db_timestamp', 'DESC');
		$detailColumns = [
			'trx_channel',
			'batch_group',
			'mid',
			'tid',
			'trx_type',
			'username',
			'bin_result',
			'base_amount',
			'tip_amount',
			'stan',
			'trx_host_date',
			'trx_host_time',
			'void_host_date',
			'void_host_time',
			'entry_mode',
			'masked_pan',
			'card_holder_name',
			'invoice_num',
			'batch_num',
			'approval_code',
			'rrn',
			'bank_id_acq',
			'is_on_us_flag',
			'is_debit_flag',
			'bank_mdr_value',
			'merchant_mdr_value',
			'card_expiry_date',
			'db_timestamp',
		];

		$query = TransactionReport::with(["settlement:batch_num,settlement_host_date", 'mid:merchant_id,mid'])
		                          ->whereHas('settlement',
			                          function (Builder $query) use ($fields) {
				                          if ($fields->get('from_date') && $fields->get('to_date')) {
					                          $from = DateTime::createFromFormat('Y-m-d', $fields->get('from_date'));
					                          $to = DateTime::createFromFormat('Y-m-d', $fields->get('to_date'));

					                          $date = sprintf(
						                          'CONCAT(DATE_FORMAT(%s, "%s"),"-",SUBSTR(%s,1,2),"-",SUBSTR(%s,3,2))',
						                          table('credit_debit.settlement') . '.db_timestamp',
						                          "%Y",
						                          table('credit_debit.settlement') . '.settlement_host_date',
						                          table('credit_debit.settlement') . '.settlement_host_date',
					                          );
					                          $query->whereRaw("$date >= DATE('{$from->format('Y-m-d')}')");
					                          $query->whereRaw("$date <= DATE('{$to->format('Y-m-d')}')");
				                          }
			                          });

		if (($merchant = $fields->get('merchant', null))) {
			$query->whereHas('mid',
				function (Builder $query) use ($merchant) {
					$query->with(['merchant:id,name'])
					      ->whereHas('merchant',
						      function (Builder $query) use ($merchant) {
							      $query->where('id', '=', $merchant);
						      });
				});
		}

		$query->orderBy($sort, $order);

		return [$detailColumns, $query];
	}

	private function settlementDetailQR(Request $request, $fields) {
		$detailColumns = [
			'trx_channel',
			'batch_group',
			'mid',
			'tid',
			'trx_type',
			'username',
			'bin_result',
			'base_amount',
			'tip_amount',
			'stan',
			'trx_host_date',
			'trx_host_time',
			'void_host_date',
			'void_host_time',
			'entry_mode',
			'masked_pan',
			'card_holder_name',
			'invoice_num',
			'batch_num',
			'approval_code',
			'rrn',
			'bank_id_acq',
			'is_on_us_flag',
			'is_debit_flag',
			'bank_mdr_value',
			'merchant_mdr_value',
			'card_expiry_date',
			'create_at',
		];

		$query = TransactionHistory::with(['settlement:batch_num', 'batchGroup:id,name', 'qpApp:id,name', 'userDevice:username,merchant_id', 'channel:id,name'])
		                           ->whereHas('settlement',
			                           function (Builder $query) use ($fields) {
				                           if ($fields->get('from_date') && $fields->get('to_date')) {
					                           $from = DateTime::createFromFormat('Y-m-d', $fields->get('from_date'));
					                           $to = DateTime::createFromFormat('Y-m-d', $fields->get('to_date'));

					                           $query->whereDate('create_at', ">=", $from->format('Y-m-d'));
					                           $query->whereDate('create_at', "<=", $to->format('Y-m-d'));
				                           }
			                           })
		                           ->whereHas('userDevice',
			                           function (Builder $query) use ($fields) {
				                           if (($merchant = $fields->get('merchant'))) {
					                           $query->where('merchant_id', "=", $merchant);
				                           }
			                           })
		                           ->whereHas('channel')
		                           ->whereHas('batchGroup')
		                           ->whereHas('qpApp');

		//print_r($query->toSql());

		return [['*'], $query];
	}

	public function setRequest(ReportFormRequest $request) {
		$this->form->setRequest($request);
	}

	public function downloadUnsettledTransactions(ReportFormRequest $request) {
		$fields = $this->fields($request);

		if ($fields->get('payment_type') == 'cdcp') {
			return $this->downloadUnsettledCreditDebit($request);
		}

		if ($fields->get('payment_type') == 'qr') {
			return $this->downloadUnsettledQR($request);
		}
	}

	public function fields(Request $request) {
		return collect($request->all());
	}

	/**
	 * @param \App\Http\Requests\ReportFormRequest $request
	 *
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	private function downloadUnsettledCreditDebit(ReportFormRequest $request) {
		list($columns, $query) = $this->unsettledCreditDebit($request, collect($request->all()));
		$headers = [
			'NO.'               => null,
			'MERCHANT'          => 'merchant_name',
			'MID'               => 'mid',
			'TID'               => 'tid',
			'USERNAME'          => 'username',
			'BATCH GROUP'       => 'batch_group',
			'TRANSACTION TYPE'  => 'trx_type',
			'CARD NO'           => 'CARD_NO',
			'MIDWARE DATE TIME' => null,
			'HOST DATE'         => 'host_date',
			'HOST TIME'         => 'host_time',
			'APPROVAL CODE'     => 'approval_code',
			'AMOUNT'            => 'base_amount',
			'BANK MDR'          => 'bank_mdr',
			'DISC AMOUNT'       => 'DISC_AMOUNT',
			'NET AMOUNT'        => 'NET_AMOUNT',
			'MERCHANT MDR'      => 'merchant_mdr',
			'MDR TYPE'          => null,
			'ACQUIRER BANK'     => 'bank_name',
			'FEE'               => 'FEE',
			'TRF AMOUNT'        => 'TRF_AMOUNT',
		];

		$self = $this;
		$amounts = ['base_amount', 'tip_amount', 'DISC_AMOUNT', 'NET_AMOUNT', 'TRF_AMOUNT', 'FEE'];
		$xls = XLS::getInstance();
		$xls->setTitle("CDCP Unsettled Transactions")
		    ->setSubject("CDCP Unsettled Transactions Reporting")
		    ->setDescription("Unsettled transactions reporting for credit debit card present");
		$xls->mergeCells('A1:U1')->setValue("Credit Debit Card Present Unsettled Transactions Reporting");
		$xls->setAlignments('A1', ['horizontal' => Alignment::HORIZONTAL_CENTER]);
		$xls->setFont('A1', ['bold' => true]);
		$xls->setColHeaders('A', 3, array_keys($headers))->each(function (Cell $cell, string $coord) use ($xls) {
			$xls->setFont($coord, ['bold' => true]);

		});

		$query->get()->each(function ($result, $i) use ($self, $amounts, $xls, $headers) {
			$xls->addRow($headers,
				null,
				null,
				function ($value, $key) use ($i, $self, $result, $xls, $amounts) {
					if ($key == 'NO.') return $xls->prepareRow($i + 1);
					if ($key == 'MIDWARE DATE TIME') return $xls->prepareRow(parse_date_from_format('Y-m-d H:i:s', $result->db_timestamp));
					if ($key == 'MDR TYPE') return $xls->prepareRow($self->mdrType($result->is_debit_flag, $result->is_on_us_flag));

					return $xls->prepareRow(
						$result->$value,
						in_array($value, $amounts) ? XLS::FORMAT_CURRENCY_IDR_SIMPLE : NumberFormat::FORMAT_TEXT
					);
				});
		});

		return $xls->download();
	}

	private function mdrType(bool $isDebit, bool $isOnUs) {
		$isDebit = $isDebit == "0" ? "Credit" : "Debit";
		$isOnUs = $isOnUs == "0" ? "offUs" : "onUs";

		return strtoupper(Str::snake($isOnUs . $isDebit, ' '));
	}

	/**
	 * @param \App\Http\Requests\ReportFormRequest $request
	 *
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	private function downloadUnsettledQR(ReportFormRequest $request) {
		list($columns, $query) = $this->unsettledQR($request, collect($request->all()));
		$headers = [
			'No.'                   => null,
			'Merchant'              => 'merchant_name',
			//'Merchant Code',
			'Transaction Type'      => 'trx_type',
			'QR Pay App'            => 'qr_pay_app',
			'Issuer'                => 'issuer_name',
			'Batch Group'           => 'batch_group',
			'Approval Code'         => 'approval_code',
			'RRN'                   => 'rrn',
			'Invoice No.'           => 'invoice_num',
			'Stan'                  => 'stan',
			'Amount'                => 'base_amount',
			'Batch Group MDR (%)'   => 'batch_group_mdr_value',
			'Batch Group Admin Fee' => 'batch_group_admin_fee',
			'Merchant MDR (%)'      => 'merchant_mdr_value',
			'Merchant Admin Fee'    => 'merchant_admin_fee',
			'Sales MDR (%)'         => 'sales_mdr_value',
			'Sales Admin Fee'       => 'sales_admin_fee',
			'Incoming Amount'       => 'incoming_amount',
			'Transfer Amount'       => 'transfer_amount',
			'Date Time'             => 'create_at',
		];

		$self = $this;
		$amounts = ['base_amount', 'merchant_mdr', 'batch_group_mdr', 'sales_mdr', 'transfer_amount', 'incoming_amount'];
		$xls = XLS::getInstance();
		$xls->setTitle("QR Unsettled Transaction")
		    ->setSubject("QR Unsettled Transaction Reporting")
		    ->setDescription("QR Unsettled Transaction");
		$xls->mergeCells('A1:T1')
		    ->setValue("QR Unsettled Transaction");
		$xls->setAlignments('A1', ['horizontal' => Alignment::HORIZONTAL_CENTER]);
		$xls->setFont('A1', ['bold' => true]);
		$xls->setColHeaders('A', 3, array_keys($headers))->each(function (Cell $cell, string $coord) use ($xls) {
			$xls->setFont($coord, ['bold' => true]);
		});

		//$transferAmount = $amount - $totalFee - ($mdrMerchant + $mdrBatchGroup + $mdrSales);
		//$incomingAmount = $amount - ($batchGroupFee + $mdrBatchGroup);
		$query->get()->each(function ($result, $i) use ($self, $amounts, $xls, $headers) {
			$result->transfer_amount = $result->base_amount - $result->total_fee - ($result->merchant_mdr + $result->batch_group_mdr + $result->sales_mdr);
			$result->incoming_amount = $result->base_amount - ($result->batch_group_admin_fee + $result->batch_group_mdr);

			$xls->addRow($headers,
				null,
				null,
				function ($value, $key) use ($i, $self, $result, $xls, $amounts) {
					if ($key == 'No.') return $xls->prepareRow($i + 1);
					if ($key == 'Date Time') return $xls->prepareRow(parse_date_from_format('Y-m-d H:i:s', $result->create_at));

					return $xls->prepareRow(
						$result->$value,
						in_array($value, $amounts) ? XLS::FORMAT_CURRENCY_IDR_SIMPLE : NumberFormat::FORMAT_TEXT
					);
				});
		});

		return $xls->download();
	}

	public function downloadSettlementTransactions(ReportFormRequest $request) {
		$fields = $this->fields($request);

		if ($fields->get('payment_type') == 'cdcp') {
			return $this->downloadSettlementCreditDebit($request);
		}

		if ($fields->get('payment_type') == 'qr') {
			return $this->downloadSettlementQR($request);
		}
	}

	private function downloadSettlementCreditDebit(ReportFormRequest $request) {
		list($columns, $query) = $this->settlementCreditDebit($request, collect($request->all()));
		$headers = [
			'NO.'                => null,
			'MERCHANT'           => 'merchant_name',
			'MID'                => 'mid',
			'PROCESSING DATE'    => 'settlement_date',
			'TOTAL TRANSACTIONS' => 'total_transactions',
			'TOTAL AMOUNT'       => 'total_amount',
			'INCOMING AMOUNT'    => 'incoming_amount',
			'TRANSFER AMOUNT'    => 'transfer_amount',
			'CLEARING CODE'      => null,
			'BANK'               => 'bank_name',
			'ACCOUNT NO.'        => 'account_number',
			'ACCOUNT NAME'       => 'account_name',
			'FEE'                => 'fee',
		];

		$self = $this;
		$amounts = ['total_amount', 'incoming_amount', 'transfer_amount', 'fee'];
		$xls = XLS::getInstance();
		$xls->setTitle("CDCP Settlement Transactions")
		    ->setSubject("CDCP Settlement Transactions Reporting")
		    ->setDescription("Settlement transactions reporting for credit debit card present");
		$xls->mergeCells('A1:M1')->setValue("Credit Debit Card Present Settlement Transactions Reporting");
		$xls->setAlignments('A1', ['horizontal' => Alignment::HORIZONTAL_CENTER]);
		$xls->setFont('A1', ['bold' => true]);
		$xls->setColHeaders('A', 3, array_keys($headers))->each(function (Cell $cell, string $coord) use ($xls) {
			$xls->setFont($coord, ['bold' => true]);

		});

		$query->get()->each(function ($result, $i) use ($self, $amounts, $xls, $headers) {
			$xls->addRow($headers,
				null,
				null,
				function ($value, $key) use ($i, $self, $result, $xls, $amounts) {
					if ($key == 'NO.') return $xls->prepareRow($i + 1);
					if ($key == 'CLEARING CODE') return $xls->prepareRow('');

					return $xls->prepareRow(
						$result->$value,
						in_array($value, $amounts) ? XLS::FORMAT_CURRENCY_IDR_SIMPLE : NumberFormat::FORMAT_TEXT
					);
				});
		});

		return $xls->download();
	}

	private function downloadSettlementQR(ReportFormRequest $request) {
		list($columns, $query) = $this->settlementQR($request, collect($request->all()));
		$headers = [
			'No.'                => null,
			'Merchant'           => 'merchant_name',
			'Date'               => 'create_at',
			'Batch No.'          => 'batch_num',
			'Batch Group'        => 'batch_group',
			'Total Transactions' => 'total_transactions',
			'Total Amount'       => 'total_amount',
			'Incoming Amount'    => 'incoming_amount',
			'Transfer Amount'    => 'transfer_amount',
			'Transfer Fee'       => 'transfer_fee',
			'Fee'                => 'fee',
			'Bank'               => 'bank_name',
			'Account No.'        => 'account_number',
			'Account Name'       => 'account_name',
		];

		$self = $this;
		$amounts = ['total_amount', 'incoming_amount', 'transfer_amount', 'transfer_fee', 'fee'];
		$xls = XLS::getInstance();
		$xls->setTitle("QR Settlement Reporting")
		    ->setSubject("QR Settlement Reporting")
		    ->setDescription("Settlement transactions reporting for QR payment");
		$xls->mergeCells('A1:N1')->setValue("QR Settlement Reporting");
		$xls->setAlignments('A1', ['horizontal' => Alignment::HORIZONTAL_CENTER]);
		$xls->setFont('A1', ['bold' => true]);
		$xls->setColHeaders('A', 3, array_keys($headers))->each(function (Cell $cell, string $coord) use ($xls) {
			$xls->setFont($coord, ['bold' => true]);

		});

		$query->get()->each(function ($result, $i) use ($self, $amounts, $xls, $headers) {
			$xls->addRow($headers,
				null,
				null,
				function ($value, $key) use ($i, $self, $result, $xls, $amounts) {
					if ($key == 'No.') return $xls->prepareRow($i + 1);

					return $xls->prepareRow(
						$result->$value,
						in_array($value, $amounts) ? XLS::FORMAT_CURRENCY_IDR_SIMPLE : NumberFormat::FORMAT_TEXT
					);
				});
		});

		return $xls->download();
	}
}
