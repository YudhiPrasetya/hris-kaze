<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ChartViewModel.php
 * @date   2020-10-30 5:40:29
 */

namespace App\Http\ViewModels;

use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ModelInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;


class PermissionViewModel extends ViewModelBase {
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): self {
		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $audit): bool {
		return false;
	}

	public function delete(Request $request, ModelInterface $audit): Redirector|RedirectResponse {
		return false;
	}

	public function new(FormRequestInterface $request): mixed {
		return false;
	}

	public function getByRole(Request $request, Role $role) {
		return $role->permissions()->get();
	}
}
