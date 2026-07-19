<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DomesticAssignmentFormRequest;
use App\Http\ViewModels\DomesticAssigmentViewModel;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\DomesticAssignment;
use App\Repositories\Eloquent\DomesticAssignmentRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class DomesticAssignmentController extends Controller
{
    private DomesticAssigmentViewModel $viewModel;

    public function __construct(DomesticAssignmentRepository $repository, FormBuilder $builder){
        $this->viewModel = new DomesticAssigmentViewModel($repository, $builder);
    }
    public function index(){
        return $this->viewModel->view('pages.assignment.domestic.list');
    }

    public function list(Request $request): Collection{
        return $this->viewModel->list($request);
    }


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param DomesticAssignmentFormRequest $request
	 *
	 * @return Application|RedirectResponse|Response|Redirector|ViewModelBase
	 */
	public function store(DomesticAssignmentFormRequest $request): Response|Redirector|RedirectResponse|Application|ViewModelBase {
		// $model = $this->viewModel->new($request);
        $model = $this->viewModel->addNew('POST', $request);

		if ($model !== false) {
			return redirect(route('assignment-domestic.show', ['assignment_domestic' => $model->id]));
		}

		return $this->create();
	}

    public function create(){
        return $this->viewModel->createForm('POST', 'assignment-domestic.store', new DomesticAssignment())
                    ->view('pages.assignment.domestic.form');
    }

    	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\DomesticAssignment $domesticAssignment
	 *
	 * @return ViewModel|ViewModelBase
	 */
	public function edit(Request $request) {
        // dd($request->all());
        // $domesticAssignment = DomesticAssignment::where('id', $request->get('id'))->first();
        $domesticAssignment = DomesticAssignment::find($request->assignment_domestic);
        // dd($domesticAssignment);
		return $this->viewModel->createForm('PUT', 'assignment-domestic.store', $domesticAssignment)
		                       ->view('pages.assignment.domestic.edit');
	}

    	/**
	 * Update the specified resource in storage.
	 *
	 * @param DomesticAssignmentFormRequest $request
	 * @param \App\Models\DomesticAssignment $domesticAssignment
	 *
	 * @return Application|RedirectResponse|Redirector
	 */
	public function update(DomesticAssignmentFormRequest $request, DomesticAssignment $domesticAssignment):Application|RedirectResponse|Redirector {
		if (!$this->viewModel->update($request, $domesticAssignment)) {
			return redirect(route('assignment-domestic.edit', ['assignment_domestic' => $domesticAssignment->id]));
		}

		// return redirect(route('assignment-domestic.show', ['assignment_domestic' => $domesticAssignment->id]));
		return redirect(route('assignment-domestic.index'));
	}

    public function addNew(Request $request, ?DomesticAssignment $model = null){
        // dd($request);
        return $this->viewModel->addNew('POST', $request);

    }

    public function domesticAssignmentUpdate(Request $request){
        return $this->viewModel->domesticAssignmentUpdate('POST', $request);
    }

    public function preServiceGetById($id){
        // $id = $request->get('id');
        return $this->viewModel->preServiceGetById($id);
    }

    public function duringServiceGetById($id){
        // $id = $request->get('id');
        return $this->viewModel->duringServiceGetById($id);
    }
}
