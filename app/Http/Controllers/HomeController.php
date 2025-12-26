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
 * @file   HomeController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;


/**
 * Class HomeController
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller {
	public function __construct() {
		// $this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @param Request $request
	 *
	 * @return Application|Factory|View
	 */
	public function index(Request $request): Factory|View|Application {
		return view(
			'home',
			[
			]
		);
	}
}
