<?php

namespace App\Http\Controllers;

use App\Http\Requests\FingerPrintDeviceDataFormRequest;
use App\Http\ViewModels\FingerPrintDeviceDataViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\FingerPrintDeviceData;
use App\Models\Vehicle;
use App\Repositories\Eloquent\FingerPrintDeviceDataRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class FingerPrintDeviceDataController extends Controller
{
    private FingerPrintDeviceDataViewModel $viewModel;

    public function __construct(FingerPrintDeviceDataRepository $repository, FormBuilder $builder)
    {
        $this->viewModel = new FingerPrintDeviceDataViewModel($repository, $builder);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response|FingerPrintDeviceDataViewModel
	 */
	public function index() {
		// return $this->viewModel->view('pages.fpdevicesdata.list');
		// return $this->viewModel->view('pages.fpdevicesdata.form');
        return redirect(route('devicelog.create'));
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}

    /**
     * Show the form creating a new source
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create(): HttpViewModel|ViewModelBase{
        return $this->viewModel->createForm('POST', 'devicelog.store', new FingerPrintDeviceData())->view('pages.fpdevicesdata.form');
    }

    /**
     * Store a newly created finger print device data in storage
     *
     * @param \Illuminate\Http\Request
     * @param \Illuminate\Http\Response
     */
    public function store(FingerPrintDeviceDataFormRequest $request): HttpViewModel|Response|Redirector|Application|FingerPrintDeviceDataViewModel|RedirectResponse{
        $model = $this->viewModel->new($request);
        if($model !== false){
            return redirect(route('devicelog.index'));
        }
        return $this->create();
    }

/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FingerPrintDeviceData  $fingerPrintDeviceData
     *
     * @return HttpViewModel|ViewModelBase|Response
     */
    public function edit($id): HttpViewModel|Response|ViewModelBase {
        // dd($id);
        // dd($fingerPrintDeviceData);
        $fingerPrintDeviceData = FingerPrintDeviceData::find($id);
        // dd($fingerPrintDeviceData);

	    return $this->viewModel->createForm('PUT', 'devicelog.update', $fingerPrintDeviceData)->view('pages.fpdevicesdata.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FingerPrintDeviceDataFormRequest $request
     * @param  \App\Models\FingerPrintDeviceData  $fingerPrintDeviceData
     *
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function update(FingerPrintDeviceDataFormRequest $request, FingerPrintDeviceData $fingerPrintDeviceData): Response|Redirector|Application|RedirectResponse {
	    if (!$this->viewModel->update($request, $fingerPrintDeviceData)) {
		    return redirect(route('devicelog.edit', ['devicelog' => $fingerPrintDeviceData->id]));
	    }
	    return redirect(route('devicelog.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FingerPrintDeviceData  $fingerprintdevicedata
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, FingerPrintDeviceData $fingerprintdevicedata)
    {
	    return $this->viewModel->delete($request, $fingerprintdevicedata);
    }

}
