<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendancePermit;
use App\Models\Fingerprint;
use App\Models\OperationLog;
use App\Models\UserInfo;
use App\Repositories\Eloquent\AttendanceLogRepository;
use App\Repositories\Eloquent\FingerprintRepository;
use App\Repositories\Eloquent\OperationLogRepository;
use App\Repositories\Eloquent\UserInfoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;


class iClockController extends BaseController {
	private const LOG_FP = 'fingerprint';

	private const LOG_USER = 'user';

	private const LOG_OPLOG = 'operation_log';

	private array $desc = [
		"0"  => "Startup",
		"1"  => "Shutdown",
		"2"  => "Authentication fails",
		"3"  => "Alarm",
		"4"  => "Access menu",
		"5"  => "Change settings",
		"6"  => "Enroll fingerprint",
		"7"  => "Enroll password",
		"8"  => "Enroll HID card",
		"9"  => "Delete user",
		"10" => "Delete fingerprint",
		"11" => "Delete password",
		"12" => "Delete RF card",
		"13" => "Clear data",
		"14" => "Create MF card",
		"15" => "Enroll MF card",
		"16" => "Register MF card",
		"17" => "Delete MF card registration",
		"18" => "Clear MF card content",
		"19" => "Move enrolled data into the card",
		"20" => "Copy data in the card to the machine",
		"21" => "Set time",
		"22" => "Delivery configuration",
		"23" => "Delete entry and exit records",
		"24" => "Clear administrator privilege",
		"82" => "Change Device IP",
		"83" => "Change Device PORT",
		"70" => "",
		"30" => "",
	];

	/**
	 * @var \App\Repositories\Eloquent\UserInfoRepository
	 */
	private UserInfoRepository $userInfoRepository;

	/**
	 * @var \App\Repositories\Eloquent\FingerprintRepository
	 */
	private FingerprintRepository $fingerprintRepository;

	/**
	 * @var \App\Repositories\Eloquent\AttendanceLogRepository
	 */
	private AttendanceLogRepository $attendanceLogRepository;

	/**
	 * @var \App\Repositories\Eloquent\OperationLogRepository
	 */
	private OperationLogRepository $operationLogRepository;

	/**
	 * @param \App\Repositories\Eloquent\UserInfoRepository $userInfoRepository
	 * @param \App\Repositories\Eloquent\FingerprintRepository $fingerprintRepository
	 * @param \App\Repositories\Eloquent\AttendanceLogRepository $attendanceLogRepository
	 */
	public function __construct(UserInfoRepository $userInfoRepository, FingerprintRepository $fingerprintRepository,
		AttendanceLogRepository $attendanceLogRepository, OperationLogRepository $operationLogRepository
	) {
		$this->middleware(['web', 'auth'], ['except' => ['cdata', 'getRequest']]);

		$this->userInfoRepository = $userInfoRepository;
		$this->fingerprintRepository = $fingerprintRepository;
		$this->attendanceLogRepository = $attendanceLogRepository;
		$this->operationLogRepository = $operationLogRepository;
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function cdata(Request $request): Response {
		// ?SN={sn}&options={options}&language={language}&pushver={pushver}
		// ?SN={sn}&table=OPERLOG&OpStamp={opstamp}
		// ?SN=BWXP211360117&table=ATTLOG&Stamp=9999

		try {
			if ($request->has('pushver') && $request->has('options')) {
				// $this->operationLogRepository->fin
				$latest = OperationLog::latest('op_time')->first();
				$time = $latest?->op_time->format('%H%i') ?? '9999';
				$sn = $request->get('SN');
				$content = <<<CONTENT
GET OPTION FROM: $sn
ATTLOGStamp=$time
OPERLOGStamp=$time
ATTPHOTOStamp=None
ErrorDelay=30
Delay=60
TransTimes=07:00;09:00;11:00;13:00;15:00;17:00;19:00
Transinterval=60
TransFlag=1111111100
TimeZone=7
Realtime=30
Encrypt=None
CONTENT;

				$res = new Response();
				$res->setContent($content);

				return $res;
			}

			if ($request->has('table') && $request->get('table') === "OPERLOG")
				return $this->operationLog($request, $request->get('SN'), $request->get('OpStamp'));

			if ($request->has('table') && $request->get('table') === "ATTLOG")
				return $this->attendanceLog($request, $request->get('SN'), $request->get('OpStamp'));

			$res = new Response();
			$res->setContent("OK");

			return $res;
		}
		catch (\Exception $e) {
			clock($e, $request->all());
			$res = new Response();
			$res->setContent("OK " . $e->getMessage());

			return $res;
		}
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param string $sn
	 * @param $opstamp
	 *
	 * @return \Illuminate\Http\Response
	 */
	private function operationLog(Request $request, string $sn, $opstamp): Response {
		$self = $this;
		$type = null;
		$content = explode("\n", $request->getContent());
		$total = count($content);

		$logs = collect($content)->map(function ($item, $key) use (&$type, $self) {
			$t = substr($item, 0, strpos($item, ' '));
			$trimmed = substr($item, strpos($item, ' ') + 1);
			$line = explode("\t", $trimmed);
			$log = [];

			$type = match (true) {
				$t === "FP" => iClockController::LOG_FP,
				$t === "USER" => iClockController::LOG_USER,
				$t === "OPLOG" => iClockController::LOG_OPLOG,
				default => throw new \Exception('Unexpected match value'),
			};

			foreach ($line as $key => $item) {
				$k = Str::before($item, "=");
				$v = Str::after($item, "=");
				$k = match (true) {
					$k === 'PIN' => 'pin',
					$k === 'FID' => 'finger_id',
					$k === 'Passwd' => 'password',
					$k === 'Grp' => 'group',
					$k === 'TZ' => 'timezone',
					$k === 'TMP' => 'template',
					$k === 'Pri' => 'privilege',
					default => Str::snake($k)
				};

				if ($t === "OPLOG") {
					$k = match (true) {
						$key === 0 => "op_type",
						$key === 1 => "op_who",
						$key === 2 => "op_time",
						$key === 3 => "value_1",
						$key === 4 => "value_2",
						$key === 5 => "value_3",
						$key === 6 => "reserved_op_type",
					};

					// if ($k === "op_time") $v = new \DateTime($v);
					if ($k === "op_time") $v = Carbon::parse($v)->toDateTime();
				}

				$log[$k] = $v;
			}

			$safe = false;

			if ($type === iClockController::LOG_USER) $this->procUser($log);
			if ($type === iClockController::LOG_FP) $this->procFinger($log);
			if ($type === iClockController::LOG_OPLOG) $this->procOperationLog($log);

			return collect($log);
		});

		$res = new Response();
		$res->setContent("OK $total");

		return $res;
	}

	/**
	 * @param array $data
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	private function procUser(array $data): void {
		$user = $this->userInfoRepository->findOneBy(['pin' => $data['pin']]);
		if (!$user) {
			$user = new UserInfo($data);
			$user->save();
		}
		// update
		else $user->save($data);
	}

	/**
	 * @param $data
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	private function procFinger($data): void {
		$finger = $this->fingerprintRepository->findOneBy(['pin' => $data['pin']]);
		if (!$finger) {
			$finger = new Fingerprint($data);
			$finger->save();
		}
		// update
		else $finger->save($data);
	}

	/**
	 * @param $data
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	private function procOperationLog($data): void {
		$op = new OperationLog($data);
		$op->save();
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param string $sn
	 * @param $opstamp
	 *
	 * @return \Illuminate\Http\Response
	 */
	private function attendanceLog(Request $request, string $sn, $opstamp): Response {
		$content = explode("\n", $request->getContent());
		$total = count($content);
		$logs = collect($content)->map(function ($item, $key) {
			$line = explode("\t", $item);
			$log = [];

			$class = AttendanceLog::class;

			foreach ($line as $key => $value) {
				// @formatter:off
				switch ($key) {
					case 0: $k = "pin"; break;
					case 1: $k = "time"; break;
					case 2: $k = "status"; break;
					case 3: $k = "verify"; break;
					case 4: $k = "workcode"; break;
					case 5: $k = "reserved_1"; break;
					case 6: $k = "reserved_2"; break;
				}
				// @formatter:on

				if (empty($value)) $value = 0;
				else {
					if ($k === "verify") $value = $value === '1';
					//if ($k === "time") $value = new \DateTime($value);
					if ($k === "time") $value = Carbon::parse($value)->toDateTime();
				}

				$log[$k] = $value;
			}

			/**
			 * @var $attLog AttendanceLog|null
			 */
			$attLog = AttendanceLog::with(['userInfo:pin'])
			                       ->where('pin', '=', $log['pin'])
			                       ->where('status', '=', $log['status'])
			                       ->whereDate('time', '=', $log['time'])
			                       ->first();

			$user = UserInfo::where('pin', '=', $log['pin'])->first();
			if ($user?->privilege == 14) return collect($log);

			if ($attLog === null) {
				/**
				 * @var $m AttendanceLog
				 */
				$m = new AttendanceLog($log);
				$m->save();
				$this->saveAttendance($m, $log);
			}
			else {
				$attLog->save($log);
				$this->saveAttendance($attLog, $log);
			}

			return collect($log);
		});

		$res = new Response();
		// $res->setContent($logs->toJson());
		$res->setContent("OK $total");

		return $res;
	}

	/**
	 * @param \App\Models\AttendanceLog $attendanceLog
	 * @param array $log
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	private function saveAttendance(AttendanceLog $attendanceLog, array &$log) {
		/*
		switch ($log['status']) {
			case '0': $key = 'start'; break;
			case '1': $key = 'end'; break;
			case '4':
			case '5': $key = 'overtime'; break;
		}
		*/

		/**
		 * @var $employee \App\Models\Employee
		 */
		$employee = $attendanceLog?->userInfo()?->first()?->employee()?->first();

		/**
		 * @var $att \App\Models\Attendance
		 */
		$att = $employee->attendance()
		                ->whereDate('at', '=', $log['time']->format('Y-m-d'))
		                ->first();

		/**
		 * @var $shift \App\Models\WorkingShift
		 */
		$time = Carbon::parse($log['time']);
		$shift = $employee->getWorkingShift()->first();
		$start = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), $shift?->start));
		$end = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), $shift?->end));

		// $end1 = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), $shift?->end));
		$end2 = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), $shift?->end))->addHour();

		$end3 = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), $shift?->end))->addHour();
		$end4 = Carbon::parse(sprintf("%s %s", $log['time']->format('Y-m-d'), '23:59:59'))->addHour();

		$key = 'start';
		// if ($time->between($start, $end1->subSecond())) $key = 'start';
		if ($time->lessThan($end) || $time->between($start, $end)) $key = 'start';
		else if ($time->between($end, $end2)) $key = 'end';
		else if ($time->between($end3->addSecond(), $end4)) $key = 'overtime';

		if ($att !== null) {
			if (in_array($key, ['end', 'overtime'])) {
				/**
				 * @see \App\Models\ModelBase::__set
				 */
				$att->{$key} = $log['time'];
				if ($key === 'overtime' && $att->end === null) {
					$att->end = $shift?->end;
				}
			}
			else if ($key === 'start') {
				$permit = AttendancePermit::where('attendance_id', '=', $att->id)
				                          ->orderByDesc('created_at')
				                          ->first();

				if ($permit && empty($permit?->end)) {
					$permit->end = $log['time'];
				}
				else {
					$permit = new AttendancePermit([
						'attendance_id' => $att->id,
						'start'         => $log['time'],
						'reason'        => '',
					]);
				}

				$permit->save();
			}
		}
		else {
			$att = new Attendance([
				'employee_id'          => $employee->id,
				'attendance_reason_id' => 1,
				'at'                   => $attendanceLog->time->format('Y-m-d'),
				$key                   => $attendanceLog->time->format('H:i:s'),
			]);
			$att->save();
		}
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getRequest(Request $request): Response {
		$res = new Response();
		$res->setStatusCode(200);
		$res->setContent("OK");

		return $res;
	}
}
