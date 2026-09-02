<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentReportFormRequest;
use App\Http\ViewModels\DomesticAssignmentDuringServiceViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\DomesticAssignmentDuringService;
use App\Repositories\Eloquent\SettingsRepository;
use App\Repositories\Eloquent\DomesticAssignmentDuringServiceRepository;
use Illuminate\Http\Request;

class DomesticAssignmentDuringServiceController extends Controller
{
    private DomesticAssignmentDuringServiceViewModel $duringServiceViewModel;

    private SettingsRepository $settingsRepository;

    public function __construct(DomesticAssignmentDuringServiceRepository $repository, SettingsRepository $settingsRepository, FormBuilder $builder){
        $this->duringServiceViewModel = new DomesticAssignmentDuringServiceViewModel($repository, $builder);
        $this->settingsRepository = $settingsRepository;
    }

    public function report(Request $request){
        return $this->duringServiceViewModel->setRequest($request)
            ->createForm('POST', 'report.assignment', new DomesticAssignmentDuringService())
            ->view('pages.report.assignment.summary');
    }

    public function reports(AssignmentReportFormRequest $request){
        return $this->duringServiceViewModel->reports($request);
            // ->view('pages.report.assignment.summary');
    }

    public function exportToExcel(Request $request){
        return $this->duringServiceViewModel->setRequest($request)
            ->exportToExcel($request);
    }

    function checkAvaibilityEmp(Request $request){
        // dump($request);
        return $this->duringServiceViewModel->checkAvaibilityEmp($request);
    }
}
