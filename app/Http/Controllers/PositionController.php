<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionFormRequest;
use App\Http\ViewModels\PositionViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Position;
use App\Repositories\Eloquent\PositionRepository;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Http\ViewModels\ViewModelBase;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

class PositionController extends Controller
{
    private PositionViewModel $viewModel;

	public function __construct(PositionRepository $repository, FormBuilder $builder) {
		$this->viewModel = new PositionViewModel($repository, $builder);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return $this->viewModel->view('pages.position.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \App\Http\ViewModels\ViewModel|\App\Http\ViewModels\ViewModelBase
     */
    public function create()
    {
		return $this->viewModel->createForm('POST', 'position.store', new Position())
		                       ->view('pages.position.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\PositionFormRequest $request
     *
     */
    public function store(PositionFormRequest $request): HttpViewModel|PositionViewModel|Redirector|RedirectResponse|Application
    {
		$model = $this->viewModel->new($request);

		if ($model !== false) {
			return redirect(route('position.index'));
		}

		return $this->create();
    }

    /**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Position $position
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function show(Position $position)
    {
        return redirect(route('position.index'));
    }

	/**
	 * Show the form for editing the specified resource.
	 * @param \App\Models\Position $jobtitle
	 * @return HttpViewModel|ViewModelBase|Response
	 */
    public function edit(Position $position): HttpViewModel|Response|ViewModelBase
    {
		return $this->viewModel->createForm('PUT', 'position.update', $position)
		                       ->view('pages.position.form');
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param PositionFormRequest $request
	 * @param \App\Models\Position  $position
	 *
     * @return Application|RedirectResponse|Response|Redirector
	 */
    public function update(PositionFormRequest $request, Position $postion, Position $position): Response|Redirector|Application|RedirectResponse
    {
		if (!$this->viewModel->update($request, $postion)) {
			return redirect(route('position.edit', ['position' => $position->id]));
		}

		return redirect(route('position.index'));
    }

/**
	 * Remove the specified resource from storage.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function destroy(Request $request,Position $position)
    {
		$this->viewModel->delete($request, $position);

		return redirect(route('position.index'));
    }

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}
}
