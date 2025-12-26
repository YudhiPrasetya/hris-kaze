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
 * @file   CityController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers\World;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\World\CityViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\World\City;
use App\Models\World\State;
use App\Repositories\Eloquent\World\CityRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class CityController extends Controller {
	private CityViewModel $viewModel;

	public function __construct(CityRepository $repository, FormBuilder $builder) {
		$this->viewModel = new CityViewModel($repository, $builder);
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
	 * @param \App\Models\World\City $city
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(City $city) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\World\City $city
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(City $city) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request      $request
	 * @param \App\Models\World\City $city
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, City $city) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\World\City $city
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(City $city) {
		//
	}

	public function getByState(Request $request, State $state): Collection {
		return $this->viewModel->select2SearchByState($request, $state);
	}
}
