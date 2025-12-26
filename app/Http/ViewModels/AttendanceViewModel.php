<?php

namespace App\Http\ViewModels;

use App\Http\Forms\AttendanceForm;
use App\Http\Forms\AttendanceReportForm;
use App\Http\Requests\AttendanceReportFormRequest;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\Attendance;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Repositories\Eloquent\SettingsRepository;
use App\Repositories\EloquentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class AttendanceViewModel extends ViewModelBase {
	public \DateTime $date;

	private ?Request $request;

	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'attendance';
		$this->routeKey = 'attendance';
		$this->modelPrimaryKey = 'id';
		$this->form = $this->formBuilder->create(AttendanceForm::class);
		$this->date = new \DateTime();
		$this->request = null;
	}

	public function setDate($date) {
		$this->date = new \DateTime($date);

		return $this;
	}

	public function setRequest(Request $request) {
		$this->request = $request;

		return $this;
	}

	public function request() {
		return $this->request;
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['attendance' => $model->id]));

		return $this;
	}

	public function createReportForm(string $method, string $route, array $options = []): ViewModelBase {
		$this->form = $this->formBuilder->create(AttendanceReportForm::class, $options);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$ret = $model->update($fields->toArray());

		return $ret;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$attendance = new Attendance($fields->toArray());
		$ret = $attendance->save();

		return $ret ? $attendance : false;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		list($offset, $limit, $sort, $order, $search, $date) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,used_at'])
		                 ->whereRaw('DATE(at) = DATE("' . $date . '")')
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['employee']['name'] =
						$self->createLink($result['employee']['name'], route('employee.show', ['employee' => $result['employee']['id']]));
					$result['at'] = $result['at']->format('Y-m-d');
					// $result['start'] = $result['start_at']->format('H:i');
					// $result['end'] = $result['end_at']->format('H:i');

					$result = $self->addDefaultListActions($result, 'show');
					$result['actions'] = array_merge([
						'permits' => [
							'icon'    => 'fad fa-running',
							'attr'    => [
								'class' => 'btn btn-sm btn-falcon-primary',
							],
							'type'    => 'button',
							'tooltip' => 'Permits',
						]
					], $result['actions']->toArray());

					return $result;
				});
			}

			return $item;
		});
	}

	public function byEmployee(Request $request, Employee $employee) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$results = $employee->attendance()
		                    ->with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,used_at'])
		                    ->orderBy($sort, $order)
		                    ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                    ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['employee']['name'] =
						$self->createLink($result['employee']['name'], route('employee.show', ['employee' => $result['employee']['id']]));

					$start = Carbon::parse(sprintf("%s %s", $result['at']->format('Y-m-d'), $result['start']));
					$end = Carbon::parse(sprintf("%s %s", $result['at']->format('Y-m-d'), $result['end']));
					$overtime = Carbon::parse(sprintf("%s %s", $result['updated_at']->format('Y-m-d'), $result['overtime']));

					$hours = $start->diff($end)->format("%H:%I");
					$overtimes = $end->diff($overtime)->format("%H:%I");

					$result['at'] = $result['at']->format('l, d F Y');
					$result['total_hours'] = $hours;
					$result['total_overtime'] = "$overtimes:00" == $result['end'] ? '00:00' : $overtimes;

					return $self->addDefaultListActions($result);
				});
			}

			return $item;
		});
	}

	public function reports(AttendanceReportFormRequest $request, SettingsRepository $settingsRepository) {
		$self = $this;
		$this->aliases = [
			'sick'          => 'sick',
			'present'       => 'present',
			'business_trip' => 'business_trip',
			'permit'        => 'permit',
			'absent'        => 'absent',
			'annual_leave'  => 'annual_leave',
			'employee_name' => 'employee_name',
		];
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request);
		$cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$current = clone($start);
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = self::getWorkingMonth($settingsRepository, $current);
		/*
		if ($cutoff->value !== 'end_of_month') {
			if ((int) date('d') < (int)$cutoff->value)
				$start = $start->sub(new \DateInterval('P1M'));
		}
		$year = $start->format('Y');
		$month = $start->format('m');
		$cutoffDateStart = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value + 1, 2, '0', STR_PAD_LEFT);
		$cutoffDateEnd = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value, 2, '0', STR_PAD_LEFT);
		$currentDate = new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart)));
		$next = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateEnd))))->add(new \DateInterval('P1M'));
		if ($cutoff->value === 'end_of_month') $next = $next->sub(new \DateInterval('P1D'));
		*/

		// @formatter:off
		$query = $query->groupBy('employee_id')
		               ->select(
			               DB::raw('employee_id'),
			               DB::raw('DATE("' . $prev->format('Y-m-d') . '") as start'),
			               DB::raw('DATE("' . $next->format('Y-m-d') . '") as end'),
			               DB::raw('(SELECT name FROM employees b WHERE b.id = attendances.employee_id) AS employee_name'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 1 GROUP BY attendance_reason_id) AS present'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 2 GROUP BY attendance_reason_id) AS sick'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 3 GROUP BY attendance_reason_id) AS business_trip'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 4 GROUP BY attendance_reason_id) AS permit'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 5 GROUP BY attendance_reason_id) AS absent'),
			               DB::raw('(SELECT COUNT(*) FROM attendances b WHERE DATE(b.at) >= DATE("' . $prev->format('Y-m-d') . '") AND DATE(b.at) <= DATE("' . $next->format('Y-m-d') . '") AND b.employee_id = attendances.employee_id AND b.attendance_reason_id = 6 GROUP BY attendance_reason_id) AS annual_leave'),
		               );
		// @formatter:on

		if ($request->get('employee')) $query->where('employee_id', '=', $request->get('employee'));
		$results = $query->with(['employee:id,name'/*, 'reason:id,name', 'annualLeave:id,no,year,used_at'*/])
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		$workingDays = count($this->workingDays($settingsRepository));

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self, $current, $workingDays) {
			if ($key === 'rows') {
				return collect($item)->map(function ($result, $i) use ($self, $current, $workingDays) {
					$result['employee_name'] = $self->createLink($result['employee_name'], route('employee.show', ['employee' => $result['employee']['id']]));
					$total = $result['present'] + $result['sick'] + $result['business_trip'] + $result['permit'] + $result['annual_leave'];
					$result['absent'] = $workingDays - $total;
					$result['actions'] = [
						'show'     => [
							'icon'    => 'fad fa-eye',
							'attr'    => [
								'class' => 'btn btn-sm btn-falcon-primary',
								'href'  => route('report.attendance.employee', ['employee' => $result['employee']['id'], 'start' => $current->format('Y-m-d')]),
							],
							'type'    => 'a',
							'tooltip' => 'View',
						],
						'download' => [
							'icon'    => 'fad fa-download',
							'attr'    => [
								'class' => 'btn btn-sm btn-falcon-success',
								'href'  => route('report.attendance.employee.download', ['employee' => $result['employee']['id']]),
							],
							'type'    => 'a',
							'tooltip' => 'Download',
						],
					];

					return $result;
				});
			}

			return $item;
		});
	}

	public static function getWorkingMonth(SettingsRepository $settingsRepository, \DateTime $start) {
		$cutoff = $settingsRepository->findOneBySectionAndKey('attendance', 'cutoff');
		$now = clone($start);

		if ($cutoff->value !== 'end_of_month') {
			//if ((int)date('d') < (int)$cutoff->value)
			$now = $now->sub(new \DateInterval('P1M'));
		}

		$year = $now->format('Y');
		$month = $now->format('m');
		$cutoffDateStart = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value + 1, 2, '0', STR_PAD_LEFT);
		$cutoffDateEnd = str_pad($cutoff->value === 'end_of_month' ? 1 : (int)$cutoff->value, 2, '0', STR_PAD_LEFT);
		$prev = new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart)));
		$next = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateEnd))))->add(new \DateInterval('P1M'));
		if ($cutoff->value === 'end_of_month') $next = $next->sub(new \DateInterval('P1D'));

		return [$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd];
	}

	public function workingDays(SettingsRepository $settingsRepository) {
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = AttendanceViewModel::getWorkingMonth($settingsRepository, new \DateTime());

		$d = clone($prev);
		$events = $this->nationalEvents(clone($d), clone($next));
		$dates = [];
		while ($d <= $next) {
			if ($d->format('N') < 6 && !in_array($d, $events)) $dates[] = clone($d);
			$d = $d->add(new \DateInterval('P1D'));
		}

		return $dates;
	}

	private function nationalEvents(\DateTime $start, \DateTime $end) {
		$months = [];
		$years = [];
		$s = (int)$start->format('n');
		$sy = (int)$start->format('Y');
		$e = (int)$end->format('n');

		while (1) {
			$months[] = $s;
			$years[] = $sy;
			$s++;
			if ($s > 12) {
				$s = 1;
				$sy++;
			}

			if ($s == $e) {
				$months[] = $s;
				$years[] = $sy;
				break;
			}
		}

		$map = function ($event) use ($start, $end, $years, $months) {
			// Fix the year on recurring events
			if ($event['recurring']) {
				$d = new \DateTime($event['start_date']);
				$m = $d->format('n');
				$index = array_search($m, $months);

				if ($index !== false) {
					$event['start_date'] = sprintf("%s-%s-%s", $years[$index], $d->format('m'), $d->format('d'));
				}
			}

			return new \DateTime($event['start_date']);
		};

		$event = CalendarEvent::whereDate('start_date', '>=', $start)
		                      ->whereDate('start_date', '<=', $end)
		                      ->where('recurring', '=', false)
		                      ->get()->map($map)->toArray();
		$recurring = CalendarEvent::whereRaw(sprintf('MONTH(start_date) IN (%s)', implode(',', $months)))
		                          ->where('recurring', '=', true)
		                          ->get()->map($map)->toArray();

		return array_merge($event, $recurring);
	}

	public function reportByEmployee(Request $request, Employee $employee) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request);
		$date = new \DateTime(date('Y-m-') . "01");
		$next = (new \DateTime(date('Y-m-') . "01"))->add(new \DateInterval('P1M'));

		$results = $query->with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,year,used_at'])
		                 ->where('employee_id', '=', $employee->id)
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key === 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					return $result;
				});
			}

			return $item;
		});
	}

	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 * @throws \Exception
	 */
	public function exportExcel(Request $request, Employee $employee, SettingsRepository $settingsRepository) {
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$days = $this->getReportDetailByEmployee($request, $employee, $settingsRepository);
		$border = [
			'allBorders' => [
				'borderStyle' => Border::BORDER_HAIR,
				'color'       => [
					'rgb' => '666666',
				],
			],
		];
		$background = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'color'    => [
					'rgb' => 'E0E0E0',
				],
			],
		];
		$titleFont = [
			'font' => [
				'bold' => true,
				'name' => 'Fira Sans',
			],
		];
		$titleAlignment = [
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical'   => Alignment::VERTICAL_TOP,
			],
		];

		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Attendance Report ' . $start->format('F Y'))
		      ->setCellValue('A3', 'NIK')->setCellValue('B3', $employee->nik)
		      ->setCellValue('A4', 'Name')->setCellValue('B4', $employee->name)
		      ->setCellValue('A5', 'Position')->setCellValue('B5', $employee->getPosition()->name);
		$sheet->mergeCells("A1:P1");
		$sheet->mergeCells("B3:P3");
		$sheet->mergeCells("B4:P4");
		$sheet->mergeCells("B5:P5");
		$sheet->getCell('A1')->getStyle()->applyFromArray($titleFont);
		$titleFont['font']['bold'] = false;
		$sheet->getCell('A2')->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('A3')->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('A4')->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('A5')->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('B3')->setDataType(DataType::TYPE_STRING)->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('B4')->getStyle()->applyFromArray($titleFont);
		$sheet->getCell('B5')->getStyle()->applyFromArray($titleFont);

		$titleFont['font']['bold'] = true;
		$titleFont['borders'] = $border;
		$headers = [
			'No.',
			'Date',
			'Working Hours',
			'Total Hours',
			'Present',
			'Sick',
			'Business Trip',
			'Permit',
			'Annual Leave',
			'Absent',
			'Remark',
		];
		$subheaders = [
			1 => [
				'Day of Week',
				'Day',
				'Month',
				'Year',
			],
			2 => [
				'Check-In',
				'Check-Out',
				'Overtime',
			],
		];
		$styles = [
			0  => array_merge($titleFont, $titleAlignment, $background),
			1  => array_merge($titleFont, $titleAlignment, $background),
			2  => array_merge($titleFont, $titleAlignment, $background),
			3  => array_merge($titleFont, $titleAlignment, $background),
			4  => array_merge($titleFont, $titleAlignment, $background),
			5  => array_merge($titleFont, $titleAlignment, $background),
			6  => array_merge($titleFont, $titleAlignment, $background),
			7  => array_merge($titleFont, $titleAlignment, $background),
			8  => array_merge($titleFont, $titleAlignment, $background),
			9  => array_merge($titleFont, $titleAlignment, $background),
			10 => array_merge($titleFont, ['alignment' => ['vertical' => Alignment::VERTICAL_TOP]], $background),
		];

		$firstColumnChr = 65; // => A

		foreach ($headers as $index => $title) {
			$sheet->setCellValue($this->getCell($firstColumnChr, 7), $title)
			      ->getStyle($this->getCell($firstColumnChr, 7))
			      ->applyFromArray($styles[$index] ?? []);

			// colspan
			if (isset($subheaders[$index])) {
				$startCell = $this->getCell($firstColumnChr, 7);
				$endCell = $this->getCell($firstColumnChr + count($subheaders[$index]) - 1, 7);
				$sheet->mergeCells("$startCell:$endCell");

				foreach ($subheaders[$index] as $subtitle) {
					$sheet->setCellValue($this->getCell($firstColumnChr, 8), $subtitle)
					      ->getStyle($this->getCell($firstColumnChr, 8))
					      ->applyFromArray($styles[$index] ?? []);
					++$firstColumnChr;
				}
			}
			// rowspan
			else {
				$startCell = $this->getCell($firstColumnChr, 7);
				$endCell = $this->getCell($firstColumnChr, 8);
				$sheet->mergeCells("$startCell:$endCell");
				++$firstColumnChr;
			}
		}

		$dataArray = [];
		$holiday = [];
		$no = 1;
		$sheet = $spreadsheet->getActiveSheet();

		foreach ($days as $data) {
			$date = \DateTime::createFromFormat('l, d F Y', $data['date']);

			$row = [
				$no++,
				$date->format('l'),
				$date->format('d'),
				$date->format('F'),
				$date->format('Y'),
				$data['start'] ?? '-',
				$data['end'] ?? '-',
				$data['overtime'] ?? '-',
				empty($data['total']) || $data['total'] === 0 ? '-' : $data['total'],
				!empty($data['present']) ? 'Y' : '-',
				!empty($data['sick']) ? 'Y' : '-',
				!empty($data['business_trip']) ? 'Y' : '-',
				!empty($data['permit']) ? 'Y' : '-',
				!empty($data['annual_leave']) ? 'Y' : '-',
				!empty($data['absent']) ? 'Y' : '-',
				$data['remark'],
			];

			$holiday[count($dataArray)] = [
				'event'   => $data['event'],
				'weekend' => in_array((int)$date->format('w'), [0, 6]),
			];
			$dataArray[] = $row;
		}

		$spreadsheet->getActiveSheet()->fromArray($dataArray, null, $this->getCell(65, 9));

		$firstColumnChr = 65;
		$centerHorizontal = [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
		$currentRow = 9;

		foreach ($dataArray as $x => $row) {
			if ($holiday[$x]['weekend']) {
				$color = '2C7BE5';
				$bgcolor = 'E8F1FC';
			}
			elseif ($holiday[$x]['event']) {
				$color = 'E63757';
				$bgcolor = 'FCE9EC';
			}
			else {
				$color = '000000';
				$bgcolor = null;
			}

			foreach ($row as $index => $data) {
				$col = $firstColumnChr + $index;
				$hAlignment = in_array($index, $centerHorizontal) ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT;

				$sheet->getCell($this->getCell($col, $currentRow))
				      ->setDataType(DataType::TYPE_STRING)
				      ->getStyle()
				      ->applyFromArray([
					      'font'      => [
						      'color' => [
							      'rgb' => $color,
						      ],
						      'name'  => 'Fira Sans',
					      ],
					      'fill'      => [
						      'fillType' => empty($bgcolor) ? Fill::FILL_NONE : Fill::FILL_SOLID,
						      'color'    => [
							      'rgb' => $bgcolor,
						      ],
					      ],
					      'alignment' => [
						      'horizontal' => $hAlignment,
					      ],
					      'borders'   => $border,
				      ]);
			}

			++$currentRow;
		}

		$sheet->calculateColumnWidths();

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		$writers = ['Xlsx', 'Xls'];
		$name = [
			'Attendance Report_',
			$employee->name . '_' . $employee->nik . '_',
			$start->format('F Y'),
		];
		$name = implode(' ', $name) . '.' . Str::lower($writers[0]);
		$filename = Str::snake($name);

		// Write documents
		$path = $this->getFilename($filename, mb_strtolower($writers[0]));
		$writer = IOFactory::createWriter($spreadsheet, $writers[0]);
		$writer->save($path);

		return $this->download($path, '');
	}

	/**
	 * @throws \Exception
	 */
	public function getReportDetailByEmployee(Request $request, Employee $employee, SettingsRepository $settingsRepository) {
		$self = $this;
		list($offset, $limit, $sort, $order, $search, $date, $start, $end) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request);

		$imonth = $start->format('n');
		[$prev, $now, $next, $cutoffDateStart, $cutoffDateEnd] = self::getWorkingMonth($settingsRepository, $start);
		$year = $now->format('Y');
		$month = $now->format('m');
		$imonthprev = $now->format('n');
		$start = 1;
		$days = [];
		$results = $query->with(['employee:id,name', 'reason:id,name', 'annualLeave:id,no,year,used_at'])
		                 ->where('employee_id', '=', $employee->id)
		                 ->whereDate('at', '>=', $prev)
		                 ->whereDate('at', '<=', $next)
		                 ->paginate($limit, self::ALL_FIELDS, 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();
		$reasons = ['present' => 1, 'sick' => 2, 'business_trip' => 3, 'permit' => 4, 'absent' => 5, 'annual_leave' => 6];

		///
		$events = CalendarEvent::whereMonth('start_date', '=', $imonthprev)
		                       ->where('recurring', '=', 1)
		                       ->get()
		                       ->map(function ($item) use ($year) {
			                       $item['start_date'] = new \DateTime($year . '-' . (new \DateTime($item['start_date']))->format('m-d'));

			                       return $item;
		                       });
		$events = $events->merge(CalendarEvent::whereMonth('start_date', '=', $imonth)
		                                      ->where('recurring', '=', 1)
		                                      ->get()
		                                      ->map(function ($item) use ($year) {
			                                      $item['start_date'] = new \DateTime($year . '-' . (new \DateTime($item['start_date']))->format('m-d'));

			                                      return $item;
		                                      }));
		$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $prev->format('Y'))
		                                      ->whereMonth('start_date', '=', $prev->format('n'))
		                                      ->whereDay('start_date', '=', $prev->format('j'))
		                                      ->where('recurring', '=', 0)
		                                      ->get());
		if ($next->format('n') > $prev->format('n')) {
			$events = $events->merge(CalendarEvent::whereYear('start_date', '=', $next->format('Y'))
			                                      ->whereMonth('start_date', '=', $next->format('n'))
			                                      ->whereDay('start_date', '=', $next->format('j'))
			                                      ->where('recurring', '=', 0)
			                                      ->get());
		}
		///

		for ($day = $start; $prev <= $next; $day++) {
			$isWeekend = in_array($prev->format('w'), [0, 6]);
			$event = $events->filter(function ($item) use ($prev) {
				return $item['start_date'] == $prev;
			})->first();

			$data = collect($results['data'])->filter(function ($item) use ($prev) {
				return $prev->format('Y-m-d') == (new \DateTime($item['at']))->format('Y-m-d');
			})->first();

			if (empty($data) && !($event || $isWeekend)) {
				$data = [];
				$data['start'] = null;
				$data['end'] = null;
				$data['overtime'] = null;
				$data['reason']['id'] = 5;
			}

			$att = collect($reasons)->map(function ($item, $key) use ($data) {
				if (empty($data)) return null;

				return $item == $data['reason']['id'] ? '<i class="fad fa-check"></i>' : null;
			})->toArray();

			$detail = null;
			if (!empty($data)) {
				$detail = $data['detail'] ?? null;
			}
			if ($event) $detail = $event['title'];

			$d = array_merge([
				'no'       => count($days) + 1,
				'date'     => $prev->format('l, d F Y'),
				'start'    => !empty($data) ? $data['start'] : null,
				'end'      => !empty($data) ? $data['end'] : null,
				'overtime' => !empty($data) ? $data['overtime'] : null,
				'total'    => 0,
				'remark'   => $detail,
				'event'    => $event ? true : false,
				'weekend'  => in_array($prev->format('w'), [0, 6]),
			], $att);

			if (!empty($d['start']) || !empty($d['end']) || !empty($d['overtime'])) {
				$day1hours = Carbon::parse(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['start']));

				if (empty($d['end'])) {
					$d['total'] = "00:00";
					// $day2hours = $day1hours->clone();
				}
				else {
					if (empty($d['start'])) {
						$d['total'] = '00:00';
					}
					else {
						// $day2hours = new \DateTime(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['end']));
						$end = Carbon::parse(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['end']));
						$diff = $day1hours->diffAsCarbonInterval($end);
						// $diff = Carbon::parse(sprintf("%s %s", (new \DateTime($data['at']))->format('Y-m-d'), $d['end']))->diffAsCarbonInterval($day1hours);
						$d['total'] = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s); // $day2hours->diff($day1hours)->format('%H:%I');
					}
				}

				if (!empty($d['overtime'])) {
					$ot = Carbon::parse(sprintf($prev->format('Y-m-d') . ' %s', $d['overtime']));
					$start = Carbon::parse(sprintf($prev->format('Y-m-d') . ' %s', $d['start']));
					$diff = $ot->diffAsCarbonInterval($start);
					//$diff = Carbon::parse(sprintf($prev->format('Y-m-d') . ' %s', $d['start']))
					//              ->diff(Carbon::parse(sprintf($prev->format('Y-m-d') . ' %s', $d['overtime'])));
					$d['total'] = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);

					//$overtime = (new \DateTime($d['overtime']))->diff($day2hours)->format('%H:%I');
					//$parts = explode(':', $d['total']);
					//$overtime = (new \DateTime($overtime))->add(new \DateInterval('PT' . intval($parts[0]) . 'H' . intval($parts[1]) . 'M'));
					// $d['total'] = $overtime->format('H:i');
				}

				if ($d['weekend'] && empty($d['remark'])) $d['remark'] = "Overtime";
			}

			$days[] = $d;

			$prev = (new \DateTime(date(sprintf("%s-%02d-%s", $year, $month, $cutoffDateStart))));
			$prev = $prev->add(new \DateInterval('P' . $day . 'D'));
		}

		return $days;
	}

	private function getCell(int $colChr, int $row) {
		return sprintf("%s%d", chr($colChr), $row);
	}

	private function getFilename($filename, $extension = 'xlsx') {
		$originalExtension = pathinfo($filename, PATHINFO_EXTENSION);

		return storage_path('app/public') . '/' . str_replace('.' . $originalExtension, '.' . $extension, basename($filename));
	}
}
