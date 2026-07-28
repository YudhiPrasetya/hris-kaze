<?php

namespace App\Http\ViewModels;

use App\Http\Forms\DomesticAssignmentForm;
use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\ArrivalFromOverseasAssignment;
use App\Models\Attendance;
use App\Models\Currency;
use App\Models\DomesticAssignment;
use App\Models\DomesticAssignmentDuringService;
use App\Models\DomesticAssignmentMeal;
use App\Models\DomesticAssignmentPreService;
use App\Models\Employee;
use App\Models\ModelInterface;
use App\Models\OverseasAssignmentMeal;
use App\Repositories\EloquentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class DomesticAssigmentViewModel extends ViewModelBase{
    public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null)
    {
        parent::__construct($repository, $formBuilder);

        $this->routeBasename = 'assignments';
        $this->routeKey = 'assignment';
        $this->modelPrimaryKey = 'id';
        $this->form = $this->formBuilder->create(DomesticAssignmentForm::class);

    }

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['assignments' => $model->id]));

		return $this;
	}

    public function new(FormRequestInterface $request): mixed {
        $this->form->setModel(new DomesticAssignment());
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		// $fields = $this->getFormFields();
        // $preService = $fields['preService'];
        return true;
	}

    public function overseasTo(){
        return Currency::all();
    }

    public function addNew($method, Request $request, ?ModelInterface $model = null)    {
        // $this->form->setMethod($method);
        $data = $request->get('data');
        $exchange_rate = $data['exchange_rate'];
        $exchange_rate_history = $data['exchange_rate_history'];
        $assignment_type = $data['assignment_type'];
        $overseas_to = ($assignment_type == "Overseas" ? $data['overseas_to'] : '-');
        $assignment_no = $data['assignment_no'];
        $letter_date = $data['letter_date'];
        $sr_no = $data['sr_no'];
        $is_chargeable = $data['is_chargeable'];
        $charge_price = $data['charge_price'];
        $customer_id = $data['customer_id'];
        $machine_id = $data['machine_id'];
        $assignment_date_from = $data['assignment_date_from'];
        $assignment_date_to = $data['assignment_date_to'];
        // dd($data);

        $newAssignment = DomesticAssignment::create([
            'exchange_rate' => $exchange_rate,
            'exchange_rate_history' => $exchange_rate_history,
            'assignment_type'=> $assignment_type,
            'overseas_to'=> $overseas_to,
            'assignment_no' => $assignment_no,
            'letter_date' => $letter_date,
            'sr_no' => $sr_no,
            'is_chargeable' => $is_chargeable,
            'charge_price' => $charge_price,
            'customer_id' => $customer_id,
            'machine_id' => $machine_id,
            'assignment_date_from' => $assignment_date_from,
            'assignment_date_to' => $assignment_date_to
        ]);
        $insertedId = $newAssignment->id;

        $dateNow = Carbon::now();
        $domesticAssignmentPreServices = $data['pre_service'];

        foreach($domesticAssignmentPreServices as $preService){
            // Process pre-service data
            $dataEmployee = Employee::find($preService['employee_id']);
            if($dataEmployee->position_id == 4){
                $empEffectiveDate = Carbon::parse($dataEmployee->effective_since);
                $empYears = $empEffectiveDate->diffInYears($dateNow);
                $domesticAssignmentMeal = DomesticAssignmentMeal::where('position_id', '=', $dataEmployee->position_id)
                                                                    ->where('year_employee', '=', $empYears)->first();
            }else{
                $domesticAssignmentMeal = DomesticAssignmentMeal::where('position_id', '=', $dataEmployee->position_id)->first();
            }

            $checkInDate = Carbon::parse($preService['check_in_date']);

            if($preService['pre_service_lunch'] == 1 && $checkInDate->isWeekend()){
                $preServiceLunch = $domesticAssignmentMeal->lunch_weekend;
            }else if($preService['pre_service_lunch'] == 1 && $checkInDate->isWeekday()){
                $preServiceLunch = $domesticAssignmentMeal->lunch_weekday;
            }elseif($preService['pre_service_lunch'] == 0){
                $preServiceLunch = 0;
            }

            if($preService['pre_service_dinner'] == 1 && $checkInDate->isWeekend()){
                $preServiceDinner = $domesticAssignmentMeal->dinner_weekend;
            }else if($preService['pre_service_dinner'] == 1 && $checkInDate->isWeekday()){
                $preServiceDinner = $domesticAssignmentMeal->dinner_weekday;
            }elseif($preService['pre_service_dinner'] == 0){
                $preServiceDinner = 0;
            }

            if($preService['pre_service_supper'] == 1 && $checkInDate->isWeekend()){
                $preServiceSupper = $domesticAssignmentMeal->supper_weekend;
            }else if($preService['pre_service_supper'] == 1 && $checkInDate->isWeekday()){
                $preServiceSupper = $domesticAssignmentMeal->supper_weekday;
            }elseif($preService['pre_service_supper'] == 0){
                $preServiceSupper = 0;
            }

            DomesticAssignmentPreService::create([
                'assignment_id' => $insertedId,
                'employee_id' => $preService['employee_id'],
                'check_in_date' => $preService['check_in_date'],
                'check_in_at' => $preService['check_in_at'],
                'pre_service_breakfast' => 0,
                'pre_service_lunch' => $preServiceLunch,
                'pre_service_dinner' => $preServiceDinner,
                'pre_service_supper' => $preServiceSupper
            ]);
        }

        $domesticAssignmentDuringServices = $data['during_service'];
        $duringServiceLunch = 0;
        $duringServiceDinner = 0;
        $defaultFinishJob = strtotime('17:00');
        foreach($domesticAssignmentDuringServices as $duringService){
            // Process during-service data
            $dataEmployee1 = Employee::find($duringService['employee_id']);

            if($assignment_type != 'Overseas'){
                if($dataEmployee1->position_id == 4){
                    $empEffectiveDate1 = Carbon::parse($dataEmployee1->effective_since);
                    $empYears1 = $empEffectiveDate1->diffInYears($dateNow);
                    $assignmentMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee1->position_id)
                                                                      ->where('year_employee', '=', $empYears1)->first();
                }else{
                    $assignmentMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee1->position_id)
                                                                    ->first();
                }

                $assignmentDate = Carbon::parse($duringService['assignment_date']);

                if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekend()){
                    $duringServiceLunch = $assignmentMeal->lunch_weekend;
                }else if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekday()){
                    $duringServiceLunch = $assignmentMeal->lunch_weekday;
                }elseif($duringService['during_service_lunch'] == 0){
                    $duringServiceLunch = 0;
                }

                if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekend()){
                    $duringServiceDinner = $assignmentMeal->dinner_weekend;
                }else if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekday()){
                    $duringServiceDinner = $assignmentMeal->dinner_weekday;
                }elseif($duringService['during_service_dinner'] == 0){
                    $duringServiceDinner = 0;
                }

                $overseas_meal = 0;
            }else{
                $duringServiceLunch = 0;
                $duringServiceDinner = 0;
                if($duringService['start_job'] != ''){
                    $assignmentMeal = OverseasAssignmentMeal::where('position_id', '=', $dataEmployee1->position_id)->first();
                    $overseas_meal = (float)$assignmentMeal->amountJPY * (float)$exchange_rate;
                }else{
                    $overseas_meal = 0;
                }
            }
            DomesticAssignmentDuringService::create([
                'assignment_id' => $insertedId,
                'employee_id' => $duringService['employee_id'],
                'check_out_date' => $duringService['check_out_date'],
                'assignment_date' => $duringService['assignment_date'],
                'during_service_breakfast' => 0,
                'during_service_lunch' => $duringServiceLunch,
                'during_service_dinner' => $duringServiceDinner,
                'start_job' => $duringService['start_job'],
                'finish_job' => $duringService['finish_job'],
                'overseas_meal' => $overseas_meal
            ]);

            $finishJob = strtotime($duringService['finish_job']);
            if($duringService['finish_job'] != ''){
                $end = $finishJob > $defaultFinishJob ? '17:00' : $duringService['finish_job'];
                $overtime = $finishJob > $defaultFinishJob ? $duringService['finish_job'] : '00:00:00';
            }else{
                $end = NULL;
                $overtime = NULL;
            }
            Attendance::create([
                'employee_id' => $duringService['employee_id'],
                'attendance_reason_id' => 3,
                'at' => $duringService['assignment_date'],
                'start' => ($duringService['start_job'] != '' ? $duringService['start_job'] : NULL),
                'end' => $end,
                'overtime' => $overtime
            ]);
        }

        // Arrival
        if($assignment_type == 'Overseas'){
            $arrival = $data['arrival'];
            foreach($arrival as $arr){
                $dataEmployee = Employee::find($arr['employee_id']);
                $empEffectiveDate = Carbon::parse($dataEmployee->effective_since);
                $empYears = $empEffectiveDate->diffInYears($dateNow);
                $arrivalMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee->position_id)
                                                                  ->where('year_employee', '=', $empYears)->first();

                $arrivalDate = Carbon::parse($arr['eta_flight_date']);
                if($arr['lunch'] == 1 && $arrivalDate->isWeekend()){
                    $arrivalLunch = $arrivalMeal->lunch_weekend;
                }else if($arr['lunch'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalLunch = $arrivalMeal->lunch_weekday;
                }elseif($arr['lunch'] == 0){
                    $arrivalLunch = 0;
                }

                if($arr['dinner'] == 1 && $arrivalDate->isWeekend()){
                    $arrivalDinner = $arrivalMeal->dinner_weekend;
                }else if($arr['dinner'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalDinner = $arrivalMeal->dinner_weekday;
                }elseif($arr['dinner'] == 0){
                    $arrivalDinner = 0;
                }

                if($arr['supper'] == 1 && $arrivalDate->isWeekend()){
                    $arr = $arrivalMeal->night_weekend;
                }else if($arr['supper'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalSupper = $arrivalMeal->night_weekday;
                }elseif($arr['supper'] == 0){
                    $arrivalSupper = 0;
                }

                ArrivalFromOverseasAssignment::create([
                    'assignment_id' => $insertedId,
                    'eta_flight_date' => $arr['eta_flight_date'],
                    'eta_flight_time' => $arr['eta_flight_time'],
                    'employee_id' => $arr['employee_id'],
                    'breakfast' => 0,
                    'lunch' => $arrivalLunch,
                    'dinner' => $arrivalDinner,
                    'supper' => $arrivalSupper
                ]);
            }
        }
        return $newAssignment;
    }

    public function domesticAssignmentUpdate(string $method, Request $request){
        $this->form->setMethod($method);
        $data = $request->get('data');
        // dd($data);
        $assignmentId = $data['assignment_id'];
        $exchangeRate = DomesticAssignment::where('id', $assignmentId)->value('exchange_rate');
        $assignmentType = $data['assignment_type'];

        // Remove pre service based on assignmentId
        $deletedPreService = DomesticAssignmentPreService::where('assignment_id', $assignmentId);
        $deletedPreService->delete();

        // Remove during service based on assignmentId
        $deletedDuringService = DomesticAssignmentDuringService::where('assignment_id', $assignmentId);
        $deletedDuringService->delete();

        // Remove arrival
        if($assignmentType == "Overseas"){
            $deletedArrival = ArrivalFromOverseasAssignment::where('assignment_id', $assignmentId);
            $deletedArrival->delete();
        }

        $dateNow = Carbon::now();

        // Process pre service
        $domesticAssignmentPreServices = $data['pre_service'];
        foreach($domesticAssignmentPreServices as $preService){
            // Process pre-service data
            $dataEmployee = Employee::find($preService['employee_id']);
            if($dataEmployee->position_id == 4){
                $empEffectiveDate = Carbon::parse($dataEmployee->effective_since);
                $empYears = $empEffectiveDate->diffInYears($dateNow);
                $domesticAssignmentMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee->position_id)
                                                                    ->where('year_employee', '=', $empYears)->first();
            }else{
                $domesticAssignmentMeal = DomesticAssignmentMeal::where('position_id', '=', $dataEmployee->position_id)->first();
            }

            $checkInDate = Carbon::parse($preService['check_in_date']);

            if($preService['pre_service_lunch'] == 1 && $checkInDate->isWeekend()){
                $preServiceLunch = $domesticAssignmentMeal->lunch_weekend;
            }else if($preService['pre_service_lunch'] == 1 && $checkInDate->isWeekday()){
                $preServiceLunch = $domesticAssignmentMeal->lunch_weekday;
            }elseif($preService['pre_service_lunch'] == 0){
                $preServiceLunch = 0;
            }

            if($preService['pre_service_dinner'] == 1 && $checkInDate->isWeekend()){
                $preServiceDinner = $domesticAssignmentMeal->dinner_weekend;
            }else if($preService['pre_service_dinner'] == 1 && $checkInDate->isWeekday()){
                $preServiceDinner = $domesticAssignmentMeal->dinner_weekday;
            }elseif($preService['pre_service_dinner'] == 0){
                $preServiceDinner = 0;
            }

            if($preService['pre_service_supper'] == 1 && $checkInDate->isWeekend()){
                $preServiceSupper = $domesticAssignmentMeal->supper_weekend;
            }else if($preService['pre_service_supper'] == 1 && $checkInDate->isWeekday()){
                $preServiceSupper = $domesticAssignmentMeal->supper_weekday;
            }elseif($preService['pre_service_supper'] == 0){
                $preServiceSupper = 0;
            }

            DomesticAssignmentPreService::create([
                'assignment_id' => $assignmentId,
                'employee_id' => $preService['employee_id'],
                'check_in_date' => $preService['check_in_date'],
                'check_in_at' => $preService['check_in_at'],
                'pre_service_breakfast' => 0,
                'pre_service_lunch' => $preServiceLunch,
                'pre_service_dinner' => $preServiceDinner,
                'pre_service_supper' => $preServiceSupper
            ]);

            // DomesticAssignmentPreService::create($preService);
        }

        // Proses during servics
        $domesticAssignmentDuringServices = $data['during_service'];
        $defaultFinishJob = strtotime('17:00');
        foreach($domesticAssignmentDuringServices as $duringService){
            // Process during-service data
            $dataEmployee1 = Employee::find($duringService['employee_id']);
            $assignmentDate = Carbon::parse($duringService['assignment_date']);
            if($assignmentType != "Overseas"){
                if($dataEmployee1->position_id == 4){
                    $empEffectiveDate1 = Carbon::parse($dataEmployee1->effective_since);
                    $empYears1 = $empEffectiveDate1->diffInYears($dateNow);
                    $domesticAssignmentMeal1 = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee1->position_id)
                                                                      ->where('year_employee', '=', $empYears1)->first();
                }else{
                    $assignmentMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee1->position_id)->first();
                }

                if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekend()){
                    $duringServiceLunch = $domesticAssignmentMeal1->lunch_weekend;
                }else if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekday()){
                    $duringServiceLunch = $domesticAssignmentMeal1->lunch_weekday;
                }elseif($duringService['during_service_lunch'] == 0){
                    $duringServiceLunch = 0;
                }

                if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekend()){
                    $duringServiceDinner = $domesticAssignmentMeal1->dinner_weekend;
                }else if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekday()){
                    $duringServiceDinner = $domesticAssignmentMeal1->dinner_weekday;
                }elseif($duringService['during_service_dinner'] == 0){
                    $duringServiceDinner = 0;
                }
                $overseas_meal = 0;

            }else{
                $duringServiceLunch = 0;
                $duringServiceDinner = 0;
                if($duringService['start_job'] != ''){
                    $assignmentMeal = OverseasAssignmentMeal::where('position_id', '=', $dataEmployee1->position_id)->first();
                    $overseas_meal = (float)$assignmentMeal->amountJPY * (float)$exchangeRate;
                }else{
                    $overseas_meal = 0;
                }
            }
            DomesticAssignmentDuringService::create([
                'assignment_id' => $assignmentId,
                'employee_id' => $duringService['employee_id'],
                'check_out_date' => $duringService['check_out_date'],
                'assignment_date' => $duringService['assignment_date'],
                'during_service_breakfast' => 0,
                'during_service_lunch' => $duringServiceLunch,
                'during_service_dinner' => $duringServiceDinner,
                'start_job' => $duringService['start_job'],
                'finish_job' => $duringService['finish_job'],
                'overtime' => '00:00:00',
                'overseas_meal' => $overseas_meal
            ]);

            $finishJob = strtotime($duringService['finish_job']);
            // Update attendace
            $att = Attendance::where('employee_id', $duringService['employee_id'])
                            ->where('at', $duringService['assignment_date'])->first();
            $att->update([
                'start' => $duringService['start_job'],
                'end' => ($finishJob > $defaultFinishJob ? '17:00' : $duringService['finish_job']),
                'overtime' => ($finishJob > $defaultFinishJob ? $duringService['finish_job'] : '00:00:00')
            ]);
        }

        if($assignmentType == "Overseas"){
            $arrival = $data['arrival'];
            foreach($arrival as $arr){
                $dataEmployee = Employee::find($arr['employee_id']);
                if($dataEmployee->position_id == 4){
                    $empEffectiveDate = Carbon::parse($dataEmployee->effective_since);
                    $empYears = $empEffectiveDate->diffInYears($dateNow);
                    $arrivalMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee->position_id)
                                                           ->where('year_employee', '=', $empYears)->first();
                }else{
                    $arrivalMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee->position_id)->first();
                }

                $arrivalDate = Carbon::parse($arr['eta_flight_date']);
                if($arr['lunch'] == 1 && $arrivalDate->isWeekend()){
                    $arrivalLunch = $arrivalMeal->lunch_weekend;
                }else if($arr['lunch'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalLunch = $arrivalMeal->lunch_weekday;
                }elseif($arr['lunch'] == 0){
                    $arrivalLunch = 0;
                }

                if($arr['dinner'] == 1 && $arrivalDate->isWeekend()){
                    $arrivalDinner = $arrivalMeal->dinner_weekend;
                }else if($arr['dinner'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalDinner = $arrivalMeal->dinner_weekday;
                }elseif($arr['dinner'] == 0){
                    $arrivalDinner = 0;
                }

                if($arr['supper'] == 1 && $arrivalDate->isWeekend()){
                    $arr = $arrivalMeal->night_weekend;
                }else if($arr['supper'] == 1 && $arrivalDate->isWeekday()){
                    $arrivalSupper = $arrivalMeal->night_weekday;
                }elseif($arr['supper'] == 0){
                    $arrivalSupper = 0;
                }

                ArrivalFromOverseasAssignment::create([
                    'assignment_id' => $assignmentId,
                    'eta_flight_date' => $arr['eta_flight_date'],
                    'eta_flight_time' => $arr['eta_flight_time'],
                    'employee_id' => $arr['employee_id'],
                    'breakfast' => 0,
                    'lunch' => $arrivalLunch,
                    'dinner' => $arrivalDinner,
                    'supper' => $arrivalSupper
                ]);
            }
        }

        return true;
    }

	public function update(FormRequestInterface $request, ModelInterface $model): bool {

		return true;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

    public function list(Request $request, ...$columns): Collection{
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		// $results = $query->with(['customer:id,name', 'destination:id,name', 'technicians:assignment_id', 'currentStatus:id,name,reason,model_id'])
		$results = $query->with(['customer:id,name', 'machine:id,name'])->orderBy('id', 'DESC')
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self) {
					$result['letter_date'] = $result['letter_date']?->format('l, d F Y');
					// $result['assignment_date'] = $result['assignment_date']?->format('l, d F Y');
					// $result['total_worker'] = count($result['technicians']);
					$result['is_chargeable'] = '<span class="badge badge-pill badge-' . ($result['is_chargeable'] ? 'success' : 'danger') . '">' .
					                           ($result['is_chargeable'] ? 'Yes' : 'No') . '</span>';

					$action = [
						'assignment_edit' => [
							'icon'    => 'fad fa-business-time',
							'attr'    => [
								'class' => 'btn btn-md btn-falcon-success',
								'href'  => route('assignments.edit', ['assignment' => $result['id']]),
								// 'href' => '#',
							],
							'type'    => 'a',
							'tooltip' => 'Edit assignment',
						],

					];
                    $result['actions'] = $action;

					// return $self->addDefaultListActions($result, 'show');
                    return $result;
				});
			}

			return $item;
		});
    }

    public function preServiceGetById($id){
        $preServices = DomesticAssignmentPreService::with('employee')->where('assignment_id', $id)->get();

        $dataPreServices = [];

        foreach($preServices as $ps){
            $data = [
                'check_in_date' => $ps->check_in_date,
                'check_in_at' => $ps->check_in_at,
                'employee_id' => $ps->employee->id,
                'employee_name' => $ps->employee->name,
                'ps_breakfast' => $ps->pre_service_breakfast > 0 ? 1 : 0,
                'ps_lunch' => $ps->pre_service_lunch > 0 ? 1 : 0,
                'ps_dinner' => $ps->pre_service_dinner > 0 ? 1 : 0,
                'ps_supper' => $ps->pre_service_supper > 0 ? 1 : 0,
            ];
            array_push($dataPreServices, $data);
        }

        return $dataPreServices;
    }

    public function duringServiceGetById($id){
        $duringServices = DomesticAssignmentDuringService::with('employee')->where('assignment_id', $id)->get();

        $dataDuringServices = [];

        foreach($duringServices as $ds){
            $data = [
                'check_out_date' => $ds->check_out_date,
                'assignment_date' => $ds->assignment_date,
                'employee_id' => $ds->employee->id,
                'employee_name' => $ds->employee->name,
                'ds_breakfast' => $ds->during_service_breakfast > 0 ? 1 : 0,
                'start_job' => $ds->start_job,
                'ds_lunch' => $ds->during_service_lunch > 0 ? 1 : 0,
                'finish_job' => $ds->finish_job,
                'ds_dinner' => $ds->during_service_dinner > 0 ? 1 : 0,
                'overtime' => $ds->overtime,
            ];
            array_push($dataDuringServices, $data);
        }
        return $dataDuringServices;
    }

    public function getArrival($id){
        $arrival = ArrivalFromOverseasAssignment::with('employee')->where('assignment_id', $id)->get();

        $dataArrival = [];

        foreach($arrival as $arr){
            $data = [
                'eta_flight_date' => $arr->eta_flight_date,
                'eta_flight_time' => $arr->eta_flight_time,
                'employee_id' => $arr->employee->id,
                'employee_name' => $arr->employee->name,
                'breakfast' => $arr->breakfast > 0 ? 1 : 0,
                'lunch' => $arr->lunch > 0 ? 1 : 0,
                'dinner' => $arr->dinner > 0 ? 1 : 0,
                'supper' => $arr->supper > 0 ? 1 : 0,
            ];
            array_push($dataArrival, $data);
        }
        // dd($arrival, $dataArrival);
        return $dataArrival;
    }
}
