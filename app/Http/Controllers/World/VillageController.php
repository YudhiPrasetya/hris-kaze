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
 * @file   VillageController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers\World;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\World\VillageViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\World\Country;
use App\Models\World\District;
use App\Models\World\Village;
use App\Repositories\Eloquent\World\StateRepository;
use App\Repositories\Eloquent\World\VillageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class VillageController extends Controller {
	private VillageViewModel $viewModel;

	public function __construct(VillageRepository $repository, FormBuilder $builder) {
		$this->viewModel = new VillageViewModel($repository, $builder);
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
	 * @param \App\Models\World\Village $village
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Village $village) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\World\Village $village
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Village $village) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request         $request
	 * @param \App\Models\World\Village $village
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Village $village) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\World\Village $village
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Village $village) {
		//
	}

	public function getByDistrict(Request $request, District $district): Collection {
		return $this->viewModel->select2SearchByDistrict($request, $district);
	}
}
