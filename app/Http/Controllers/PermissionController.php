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
 * @file   PermissionController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers;

use App\Http\ViewModels\PermissionViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Role;
use App\Repositories\Eloquent\PermissionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class PermissionController extends Controller {
	private PermissionViewModel $viewModel;

	public function __construct(PermissionRepository $repository, FormBuilder $builder) {
		$this->viewModel = new PermissionViewModel($repository, $builder);
	}

	public function apiPermissionByRole(Request $request, Role $role): Collection {
		return $this->viewModel->getByRole($request, $role);
	}
}
