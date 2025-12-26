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
 * @file   TrackerViewModel.php
 * @date   2021-05-27 18:0:38
 */

namespace App\Http\ViewModels;

use App\Http\Requests\FormRequestInterface;
use App\Models\ModelInterface;
use App\Repositories\Eloquent\VehicleRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class TrackerViewModel extends ViewModelBase {
	public Collection $vehicles;

	public function __construct(VehicleRepository $repository) {
		parent::__construct($repository);
		$this->vehicles = $this->repository->all();
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		// TODO: Implement createForm() method.
	}

	public function update(FormRequestInterface $request, ModelInterface $model): bool {
		// TODO: Implement update() method.
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	public function new(FormRequestInterface $request): mixed {
		// TODO: Implement new() method.
	}
}
