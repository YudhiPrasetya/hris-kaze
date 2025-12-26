<?php
/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   DistrictController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers\World;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\World\DistrictViewModel;
use App\Http\ViewModels\World\StateViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\World\City;
use App\Models\World\Country;
use App\Models\World\District;
use App\Repositories\Eloquent\World\DistrictRepository;
use App\Repositories\Eloquent\World\StateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class DistrictController extends Controller {
	private DistrictViewModel $viewModel;

	public function __construct(DistrictRepository $repository, FormBuilder $builder) {
		$this->viewModel = new DistrictViewModel($repository, $builder);
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
	 * @param \App\Models\World\District $district
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(District $district) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\World\District $district
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(District $district) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request          $request
	 * @param \App\Models\World\District $district
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, District $district) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\World\District $district
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(District $district) {
		//
	}

	public function getByCity(Request $request, City $city): Collection {
		return $this->viewModel->select2SearchByCity($request, $city);
	}
}
