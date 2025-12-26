<?php

namespace App\Http\Controllers;

use App\Http\Requests\FingerPrintDeviceFormRequest;
use App\Http\ViewModels\FingerPrintDeviceViewModel;
use App\Http\ViewModels\ViewModelBase;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\FingerPrintDevice;
use App\Models\Vehicle;
use App\Repositories\Eloquent\FingerPrintDeviceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class FingerPrintDeviceController extends Controller
{
    private FingerPrintDeviceViewModel $viewModel;

    public function __construct(FingerPrintDeviceRepository $repository, FormBuilder $builder)
    {
        $this->viewModel = new FingerPrintDeviceViewModel($repository, $builder);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response|FingerPrintDeviceViewModel
	 */
	public function index(): Response|FingerPrintDeviceViewModel {
		return $this->viewModel->view('pages.fp-devices.list');
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
        return $this->viewModel->createForm('POST', 'fingerprintdevice.store', new FingerPrintDevice())->view('pages.fp-devices.form');
    }

    /**
     * Store a newly created finger print device data in storage
     *
     * @param \Illuminate\Http\Request
     * @param \Illuminate\Http\Response
     */
    public function store(FingerPrintDeviceFormRequest $request): HttpViewModel|Response|Redirector|Application|FingerPrintDeviceViewModel|RedirectResponse{
        $model = $this->viewModel->new($request);
        if($model !== false){
            return redirect(route('fingerprintdevice.index'));
        }
        return $this->create();
    }

/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FingerPrintDevice  $fingerprintdevice
     *
     * @return HttpViewModel|ViewModelBase
     */
    public function edit(FingerPrintDevice $fingerprintdevice): HttpViewModel|ViewModelBase {
        // dd($fpd);
        // $myData = FingerPrintDevice::find(2);
        // dd($fpd::find(2));
	    return $this->viewModel->createForm('PUT', 'fingerprintdevice.update', $fingerprintdevice)->view('pages.fp-devices.form');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FingerPrintDeviceFormRequest $request
     * @param  \App\Models\FingerPrintDevice  $fingerprintdevice
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(FingerPrintDeviceFormRequest $request, FingerPrintDevice $fingerprintdevice): Redirector|Application|RedirectResponse {
	    if (!$this->viewModel->update($request, $fingerprintdevice)) {
		    return redirect(route('fingerprintdevice.edit', ['fingerprintdevice' => $fingerprintdevice->id]));
	    }
	    return redirect(route('fingerprintdevice.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FingerPrintDevice  $fingerprintdevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, FingerPrintDevice $fingerprintdevice)
    {
	    return $this->viewModel->delete($request, $fingerprintdevice);
    }

}
