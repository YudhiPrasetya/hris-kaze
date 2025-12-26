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
 * @file   ChartController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\ChartViewModel;
use Illuminate\Http\Request;


class ChartController extends Controller {
	private ChartViewModel $viewModel;
}
