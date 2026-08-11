<?php
namespace App\Http\ViewModels;

use App\Http\Forms\AssignmentReportForm;
use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\DomesticAssignmentDuringService;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DomesticAssignmentDuringServiceViewModel extends ViewModelBase
{
    private ?Request $request;

    public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null){
        parent::__construct($repository, $formBuilder);
        $this->routeBasename = 'assignments';
        $this->routeKey = 'assignment';
        $this->modelPrimaryKey = 'id';
        $this->form = $this->formBuilder->create(AssignmentReportForm::class);

        $this->request = null;
    }

    public function new(FormRequestInterface $request): mixed{
        $this->form->setModel(new DomesticAssignmentDuringService());
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

        return true;
    }
    public function update(FormRequestInterface $request, ModelInterface $model):bool{
        return true;
    }

    public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase{
		$this->setModel($model);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['assignments' => $model->id]));

		return $this;
    }

    public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse{
        // TODO: Implement delete() method.
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function request()
    {
        return $this->request;
    }

    public function reports(Request $request){
        $self = $this;
        list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);

        // $assignmentType = $request->get('assignment_type');
        $startDate = new DateTime($request->get('start_date'));
        $endDate = new DateTime($request->get('end_date'));

        $query = $this->getBaseQuery($request);

        $results = $query->where('assignment_date', '>=', $startDate)
                            ->where('assignment_date', '<=', $endDate)
                            ->with(['domesticAssignment' => function($query){
                            return $query->with('customer:id,name', 'machine:id,name')->with(['domesticAssigmentPreServices']);
                            }])
                            ->with('employee:id,name')
                            ->orderBy('id', 'DESC')
                            ->paginate($limit, ['*'], 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
                            ->toArray();

        // if($assignmentType == 'All'){
        //     $results = $query->where('assignment_date', '>=', $startDate)
        //                      ->where('assignment_date', '<=', $endDate)
        //                      ->with(['domesticAssignment' => function($query){
        //                         return $query->with('customer:id,name', 'machine:id,name')->with(['domesticAssigmentPreServices']);
        //                      }])
        //                      ->with('employee:id,name')
        //                      ->orderBy('id', 'DESC')
        //                      ->paginate($limit, ['*'], 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
        //                      ->toArray();

        // }else{
        //     $results = $query->where('assignment_date', '>=', $startDate)
        //                      ->where('assignment_date', '<=', $endDate)
        //                      ->with(['domesticAssignment' => function($query) use ($assignmentType){
        //                         return $query->where('assignment_type', $assignmentType)->with('customer:id,name', 'machine:id,name')
        //                                      ->with(['domesticAssigmentPreServices']);
        //                      }])
        //                      ->with('employee:id,name')
        //                      ->orderBy('id', 'DESC')
        //                      ->paginate($limit, ['*'], 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
        //                      ->toArray();
        // }

        return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self) {
            if ($key == 'rows') {
                return collect($item)->map(function ($result, $i) use ($self) {
                    // $startDt = $self->request->get('start_date');
                    // dd($self->getFormFields());
                    $arrFormFields = $self->getFormFields();
                    $dateStart = new DateTimeImmutable($arrFormFields['start_date']);
                    $dateEnd = new DateTimeImmutable($arrFormFields['end_date']);

                    $result['startDate'] = $dateStart->format("Y/m/d");
                    $result['endDate'] = $dateEnd->format("Y/m/d");

                    // $result['endDate'] = $self->data('endDt')->format('Y-m-d');
                    $result['assignment_date'] = Carbon::parse($result['assignment_date'])->format('l, d F Y');
                    foreach($result['domestic_assignment']['domestic_assigment_pre_services'] as $ds){
                        $result['check_in_at'] = $ds['check_in_at'];
                    }
                    $result['total_during_service'] = $result['during_service_breakfast'] + $result['during_service_lunch'] + $result['during_service_dinner'] + $result['during_service_supper'];
                    // $result = $self->addDefaultListActions($result, 'show');
                    // dd($result);
                    return $result;
                });
            }
            return $item;
        });
    }

    public function exportToExcel(Request $request){
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $strStartDate = new DateTimeImmutable($startDate);
        $startDt = $strStartDate->format('Y/m/d');

        $strEndDate = new DateTimeImmutable($endDate);
        $endDt = $strEndDate->format('Y/m/d');

        $query = $this->getBaseQuery($request);

        $results = $query->where('assignment_date', '>=', $startDate)
                            ->where('assignment_date', '<=', $endDate)
                            ->with(['domesticAssignment' => function($query){
                            return $query->with('customer:id,name', 'machine:id,name')->with(['domesticAssigmentPreServices']);
                            }])
                            ->with('employee:id,name')
                            ->orderBy('id', 'DESC')->get()
                            ->toArray();
        // dd($results);

        $spreadSheet = new Spreadsheet();
        $spreadSheet->setActiveSheetIndex(0);
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Assignment Report')
              ->setCellValue('A2', 'Periode: ' . $startDt . ' - ' . $endDt);

        // $headers = [
        //     'No', 'Assignment Date', 'SR NO', 'Customer', 'User', 'Members',
        //     'Start', 'Finish', 'Hotel', 'BF', 'LC', 'NT', 'Total/Member', 'Overseas Meal (IDR)',
        //     'Foreign Currency', 'Overseas Meal In Foreign Currency'
        // ];

        // Headers
        $sheet->setCellValue('A4', 'No.'); 
        $sheet->setCellValue('B4', 'Assignment Date'); 
        $sheet->setCellValue('C4', 'SR NO'); 
        $sheet->setCellValue('D4', 'Customer'); 
        $sheet->setCellValue('E4', 'User'); 
        $sheet->setCellValue('F4', 'Members'); 
        $sheet->setCellValue('G4', 'Start'); 
        $sheet->setCellValue('H4', 'Finish'); 
        $sheet->setCellValue('I4', 'Hotel'); 
        $sheet->setCellValue('J4', 'BF'); 
        $sheet->setCellValue('K4', 'LC'); 
        $sheet->setCellValue('L4', 'DN'); 
        $sheet->setCellValue('M4', 'NT'); 
        $sheet->setCellValue('N4', 'Total/Member'); 
        $sheet->setCellValue('O4', 'Overseas Meal (IDR)'); 
        $sheet->setCellValue('P4', 'Foreign Currency'); 
        $sheet->setCellValue('Q4', 'Overseas Meal In Foreign Currency');
        
        // Content
        $no = 1;
        $row = 5;
        foreach($results as $result){
            $totalDuringService = (float)$result['during_service_lunch'] + (float)$result['during_service_dinner'] + (float)$result['during_service_supper'];
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, Carbon::parse($result['assignment_date'])->format('l, d F Y'));
            $sheet->setCellValue('C' . $row, $result['domestic_assignment']['sr_no']);
            $sheet->setCellValue('D' . $row, $result['domestic_assignment']['customer']['name']);
            $sheet->setCellValue('E' . $row, $result['domestic_assignment']['customer']['name']);
            $sheet->setCellValue('F' . $row, $result['employee']['name']);
            $sheet->setCellValue('G' . $row, $result['start_job'] ?? "-");
            $sheet->setCellValue('H' . $row, $result['finish_job'] ?? "-");
            foreach($result['domestic_assignment']['domestic_assigment_pre_services'] as $ds){
                $sheet->setCellValue('I' . $row, $ds['check_in_at']);
            }
            $sheet->setCellValue('J' . $row, "-");
            $sheet->setCellValue('K' . $row, $result['during_service_lunch']);
            $sheet->setCellValue('L' . $row, $result['during_service_dinner']);
            $sheet->setCellValue('M' . $row, $result['during_service_supper']);
            $sheet->setCellValue('N' . $row, $totalDuringService);
            $sheet->setCellValue('O' . $row, $result['overseas_meal']);
            $sheet->setCellValue('P' . $row, $result['foreign_currency'] ?? "-");
            $sheet->setCellValue('Q' . $row, $result['overseas_meal_in_foreign_currency']);
            ++$no;
            ++$row;
        }
        $sheet->calculateColumnWidths();
        $spreadSheet->setActiveSheetIndex(0);

        $writers = ['Xlsx', 'Xls'];
        // $name = [
        //     'Assignment Report',
        //     '(' . $startDt . ' - ' . $endDt . ')'
        // ];
        // $name = implode(' ', $name) . '.' . Str::lower($writers[0]);
        $fileName = 'Assignment_Report_(' . $strStartDate->format('l, d F Y') . " - " . $strEndDate->format('l, d F Y') . ')' . '.' . Str::lower($writers[0]);
        // $fileName = Str::snake($name);

        // Write Document
        $path = $this->getFileName($fileName, mb_strtolower($writers[0]));
        $writer = IOFactory::createWriter($spreadSheet, $writers[0]);
        $writer->save($path);

        return $this->download($path, '');
    }

    private function getFilename($filename, $extension = 'xlsx')
    {
        $originalExtension = pathinfo($filename, PATHINFO_EXTENSION);

        return storage_path('app/public') . '/' . str_replace('.' . $originalExtension, '.' . $extension, basename($filename));
    }    

}
