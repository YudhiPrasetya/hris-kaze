<?php

namespace App\Http\Controllers;

use App\Http\ViewModels\AnnualLeaveViewModel;
use App\Http\ViewModels\AssignmentViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\AnnualLeave;
use App\Models\Employee;
use App\Repositories\Eloquent\AnnualLeaveRepository;
use App\Repositories\Eloquent\AssignmentRepository;
use Illuminate\Http\Request;


class AnnualLeaveController extends Controller {
	private AnnualLeaveViewModel $viewModel;

	public function __construct(AnnualLeaveRepository $repository, FormBuilder $builder) {
		$this->viewModel = new AnnualLeaveViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\AnnualLeave $annualLeave
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(AnnualLeave $annualLeave) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\AnnualLeave $annualLeave
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(AnnualLeave $annualLeave) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\AnnualLeave  $annualLeave
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, AnnualLeave $annualLeave) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\AnnualLeave $annualLeave
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(AnnualLeave $annualLeave) {
		//
	}

	public function selectByEmployee(Request $request, Employee $employee) {
		return $this->viewModel->selectByEmployee($request, $employee);
	}
}
