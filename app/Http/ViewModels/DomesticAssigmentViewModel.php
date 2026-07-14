<?php

namespace App\Http\ViewModels;

use App\Http\Forms\DomesticAssignmentForm;
use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\DomesticAssignment;
use App\Models\DomesticAssignmentDuringService;
use App\Models\DomesticAssignmentEmployee;
use App\Models\DomesticAssignmentMeal;
use App\Models\DomesticAssignmentPreService;
use App\Models\Employee;
use App\Models\ModelInterface;
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

        $this->routeBasename = 'assignment-domestic';
        $this->routeKey = 'assignment_domestic';
        $this->modelPrimaryKey = 'id';
        $this->form = $this->formBuilder->create(DomesticAssignmentForm::class);

    }

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['assignment-domestic' => $model->id]));
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['assignment_domestic' => $model->id]));

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

    public function addNew($method, Request $request, ?ModelInterface $model = null)    {
        // var_dump($request);
        // $this->setModel($model);
        $this->form->setMethod($method);
        $data = $request->get('data');
        // dd($data);

        $assignment_no = $data['assignment_no'];
        $letter_date = $data['letter_date'];
        $sr_no = $data['sr_no'];
        $is_chargeable = $data['is_chargeable'];
        $charge_price = $data['charge_price'];
        $customer_id = $data['customer_id'];
        $machine_id = $data['machine_id'];


        $newAssignment = DomesticAssignment::create([
            'assignment_no' => $assignment_no,
            'letter_date' => $letter_date,
            'sr_no' => $sr_no,
            'is_chargeable' => $is_chargeable,
            'charge_price' => $charge_price,
            'customer_id' => $customer_id,
            'machine_id' => $machine_id
        ]);
        $insertedId = $newAssignment->id;

        $dateNow = Carbon::now();
        $domesticAssignmentPreServices = $data['pre_service'];
        foreach($domesticAssignmentPreServices as $preService){
            // Process pre-service data
            $dataEmployee = Employee::find($preService['employee_id']);
            $empEffectiveDate = Carbon::parse($dataEmployee->effective_since);
            $empYears = $empEffectiveDate->diffInYears($dateNow);

            $domesticAssignmentMeal = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee->position_id)
                                                              ->where('year_employee', '=', $empYears)->first()->get();
            // dd($domesticAssignmentMeal);

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

            // DomesticAssignmentPreService::create($preService);
        }
        // $ret = DomesticAssignmentEmployee::insert($domesticAssignmentEmployees);

        $domesticAssignmentDuringServices = $data['during_service'];
        foreach($domesticAssignmentDuringServices as $duringService){
            // Process during-service data
            $dataEmployee1 = Employee::find($duringService['employee_id']);
            $empEffectiveDate1 = Carbon::parse($dataEmployee1->effective_since);
            $empYears1 = $empEffectiveDate1->diffInYears($dateNow);

            $domesticAssignmentMeal1 = DomesticAssignmentMeal::where('position_id', '=',$dataEmployee1->position_id)
                                                              ->where('year_employee', '=', $empYears1)->firstOrFail();

            $assignmentDate = Carbon::parse($duringService['assignment_date']);

            if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekend()){
                $duringServiceLunch = $domesticAssignmentMeal1->lunch_weekend;
            }else if($duringService['during_service_lunch'] == 1 && $assignmentDate->isWeekday()){
                $duringServiceLunch = $domesticAssignmentMeal1->lunch_weekday;
            }elseif($preService['pre_service_lunch'] == 0){
                $duringServiceLunch = 0;
            }

            if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekend()){
                $duringServiceDinner = $domesticAssignmentMeal1->dinner_weekend;
            }else if($duringService['during_service_dinner'] == 1 && $assignmentDate->isWeekday()){
                $duringServiceDinner = $domesticAssignmentMeal1->dinner_weekday;
            }elseif($duringService['during_service_dinner'] == 0){
                $duringServiceDinner = 0;
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
            ]);

        }

        return $newAssignment;

    }

	public function update(FormRequestInterface $request, ModelInterface $model): bool {

		return true;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	/**
	 * @inheritDoc
	 */


    public function list(Request $request, ...$columns): Collection{
		$self = $this;
		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		// $results = $query->with(['customer:id,name', 'destination:id,name', 'technicians:assignment_id', 'currentStatus:id,name,reason,model_id'])
		$results = $query->with(['customer:id,name'])
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
						'domestic_assignment_edit' => [
							'icon'    => 'fad fa-business-time',
							'attr'    => [
								'class' => 'btn btn-md btn-falcon-success',
								'href'  => route('assignment-domestic.edit', ['assignment_domestic' => $result['id']]),
								// 'href' => '#',
							],
							'type'    => 'a',
							'tooltip' => 'Edit domestic assignment',
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
}
