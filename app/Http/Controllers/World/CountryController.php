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
 * @file   CountryController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers\World;

use App\Http\Controllers\Controller;
use App\Models\World\Country;
use Illuminate\Http\Request;


class CountryController extends Controller {
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
	 * @param \App\Models\World\Country $country
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Country $country) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\World\Country $country
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Country $country) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request         $request
	 * @param \App\Models\World\Country $country
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Country $country) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\World\Country $country
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Country $country) {
		//
	}

	public function countryFlag(Country $country) {
		$flag = \File::get(resource_path('img/flags/' . strtolower($country->iso) . '.svg'));

		return response($flag)->withHeaders(
			[
				"Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0",
				"Pragma"        => "no-cache",
				'Content-Type'  => 'image/svg+xml',
			]
		);
	}
}
