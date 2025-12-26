<?php

namespace App\Http\ViewModels;

use App\Http\Forms\FingerPrintDeviceDataForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\FingerPrintDevice;
use App\Models\FingerPrintDeviceData;
use App\Models\ModelInterface;
use App\Notifications\PullDataFromDeviceSuccess;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use Carbon\Carbon;

use Jmrashed\Zkteco\Lib;
use Jmrashed\Zkteco\Lib\ZKTeco;
class FingerPrintDeviceDataViewModel extends ViewModelBase{
    public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null)
    {
        parent::__construct($repository, $formBuilder);

        // $this->routeBasename = 'fingerprintdevicedata';
        $this->routeBasename = 'devicelog';
        // $this->routeKey = 'fingerprintdevicedatum';
        $this->routeKey = 'devicelog';
        $this->form = $this->formBuilder->create(FingerPrintDeviceDataForm::class);

    }

    public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase
    {
        $this->setModel($model);
        $this->form->setMethod($method);
        $this->form->setUrl(route($route, [$this->routeKey => $model->id]));

        return $this;
    }

    public function list(Request $request, ...$columns): Collection{
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query//->with(['country:iso,name', 'state:id,name', 'city:id,name', 'district:id,name', 'village:id,name', 'position:id,name'])
		->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		->toArray();
		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					return $self->addDefaultListActions($result, 'show');
				});
			}
			return $item;
		});

		// $self = $this;
		// $results = $this->getPaginatedList($request, $this->repository, ...$columns);
		// $rows = $results->get('rows')->map(function ($result, $key) use ($self) {
		// 	return $self->addDefaultListActions($result, 'show');
		// });
		// $results->offsetSet('rows', $rows);

		// return $results;
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
        $this->form->setRequest($request);
        $this->form->redirectIfNotValid();

        if(!FingerPrintDeviceData::find($model->uid)->forceDelete()){
            $request->session()->flash('message', "Failed to delete <strong>{$model->plat_number}</strong>");
            $request->session()->flash('alert', "danger");
        }else{
            $request->session()->flash('message', "Successfully delete <strong>Data finger print device data!</strong>.");
            $request->session()->flash('alert', "success");
        }
        return redirect(route('fingerprintdevicedata.index'));
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
        $arrFields = $fields->toArray();

        $deviceID = $arrFields['finger_print_device_id'];
        $from = $arrFields['from'];
        $to = $arrFields['to'];

        $dataDevice = FingerPrintDevice::find($deviceID);

        $zk = new ZKTeco($dataDevice->ip_address);
        $connected = $zk->connect();
        if($connected){
            $logs = $zk->getAttendance();
            // $todayDate = date('Y-m-d');
            $attendanceRecords = [];
            foreach($logs as $record){
                $recordDate = substr($record['timestamp'], 0, 10);
                // if($recordDate == $todayDate){
                //     $todayRecords[] = $record;
                // }
                if($recordDate >= $from && $recordDate <= $to){
                    $attendanceRecords[] = $record;
                }
            }
            dd($attendanceRecords);
            $zk->disconnect();

            // foreach($attendanceRecords as $r){
            //     // var_dump($r['id']);
            //     $nik = $r['id'];
            //     $dateTime = Carbon::parse($r['timestamp']);
            //     $employee = Employee::where('nik', '=', $nik)->first();
            //     $att = $employee->attendance()->whereDate('at', '=', $dateTime->format('Y-m-d'))->first();

            //     $time = Carbon::parse($dateTime);
            //     $shift = $employee->getWorkingShift()->first();
            //     $start = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->start));
            //     $end = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end));
            //     $end2 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end))->addHour();
            //     $end3 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end))->addHour();
            //     $end4 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), '23:59:59'))->addHour();
            //     $key = 'start';

            //     if($time->lessThan($end) || $time->between($start, $end)) $key = 'start';
            //     else if ($time->between($end, $end2)) $key = 'end';
            //     else if($time->between($end3->addSecond(), $end4)) $key = 'overtime';

            //     if($att === null){
            //         $att = new Attendance([
            //             'employee_id' => $employee->id,
            //             'attendance_reason_id' => 1,
            //             'at' => $dateTime->format('Y-m-d'),
            //             $key => $dateTime->format('H:i:s')
            //         ]);
            //     }else{
            //         $att->{$key} = $dateTime->format('H:i:s');
            //         if ($key === 'overtime' && $att->end === null) {
            //             $att->end = $shift?->end;
            //         }
            //     }
            //     $att->save();

            //     $arrFpDeviceData = [
            //         'finger_print_device_id' => 1,
            //         'nik' => $nik,
            //         'timestamps' => $dateTime
            //     ];

            //     $fpDeviceData = new FingerPrintDeviceData($arrFpDeviceData);
            //     $fpDeviceData->save();
            // }
            // $this->sendNotification();

            // return redirect(route('devicelog.create'));
            // return true;
        }

        // $attendanceRecords = [
        //     ["id" => "2025021", "timestamp" => "2025-11-03 07:32:25"],
        //     ["id" => "2025021", "timestamp" => "2025-11-03 18:46:29"],

        //     ["id" => "2025091", "timestamp" => "2025-11-03 07:38:29"],
        //     ["id" => "2025091", "timestamp" => "2025-11-03 17:33:21"],

        //     ["id" => "2024071", "timestamp" => "2025-11-03 07:40:21"],
        //     ["id" => "2024071", "timestamp" => "2025-11-03 17:33:21"],

        //     ["id" => "2024072", "timestamp" => "2025-11-03 07:40:21"],
        //     ["id" => "2024072", "timestamp" => "2025-11-03 17:33:21"],
        // ];

        // foreach($attendanceRecords as $r){
        //     // var_dump($r['id']);
        //     $nik = $r['id'];
        //     $dateTime = Carbon::parse($r['timestamp']);
        //     $employee = Employee::where('nik', '=', $nik)->first();
        //     $att = $employee->attendance()->whereDate('at', '=', $dateTime->format('Y-m-d'))->first();

        //     $time = Carbon::parse($dateTime);
        //     $shift = $employee->getWorkingShift()->first();
        //     $start = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->start));
        //     $end = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end));
        //     $end2 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end))->addHour();
        //     $end3 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), $shift?->end))->addHour();
        //     $end4 = Carbon::parse(sprintf("%s %s", $dateTime->format('Y-m-d'), '23:59:59'))->addHour();
        //     $key = 'start';

        //     if($time->lessThan($end) || $time->between($start, $end)) $key = 'start';
        //     else if ($time->between($end, $end2)) $key = 'end';
        //     else if($time->between($end3->addSecond(), $end4)) $key = 'overtime';

        //     if($att === null){
        //         $att = new Attendance([
        //             'employee_id' => $employee->id,
        //             'attendance_reason_id' => 1,
        //             'at' => $dateTime->format('Y-m-d'),
        //             $key => $dateTime->format('H:i:s')
        //         ]);
        //     }else{
        //         $att->{$key} = $dateTime->format('H:i:s');
        //         if ($key === 'overtime' && $att->end === null) {
        //             $att->end = $shift?->end;
        //         }
        //     }
        //     $att->save();

        //     $arrFpDeviceData = [
        //         'finger_print_device_id' => 1,
        //         'nik' => $nik,
        //         'timestamps' => $dateTime
        //     ];

        //     $fpDeviceData = new FingerPrintDeviceData($arrFpDeviceData);
        //     $fpDeviceData->save();
        // }

        // $groupedData=[];
        // foreach($todayRecords as $item){
        //     $id = $item['id'];
        //     if(!isset($groupedData[$id])){
        //         $groupedData[$id] = [];
        //     }
        //     $groupedData[$id][] = $item;
        // }
        // dd($groupedData);

        // dd($dataDevice->ip_address);

		// $fpDeviceData = new FingerPrintDeviceData($fields->toArray());


		// $ret = $fpDevice->save();

		// return $ret ? $fpDevice : false;
        // dd($fpDeviceData);
        // var_dump($fpDeviceData);

        // $request->session()->flash('message', "Pull data from device success. Please check attendece for the clarification.");

        // $this->sendNotification();

        // return redirect(route('devicelog.create'));
        return false;
	}

    private function sendNotification(){
        $user = auth()->user();
        $user->notify(new PullDataFromDeviceSuccess());
    }

}
