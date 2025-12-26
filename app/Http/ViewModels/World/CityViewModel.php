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
 * @file   CityViewModel.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\ViewModels\World;

use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Models\World\City;
use App\Models\World\State;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class CityViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'city';
		$this->routeKey = 'city';
	}

	public function select2SearchByState(Request $request, State $state): Collection {
		$search = $request->get('search', null);
		$results = collect([]);
		$items = null;

		if (!empty($search)) {
			$items = City::search($search)->where('state_id', $state->id);
		}
		else {
			$items = $state->cities();
		}

		$results->offsetSet('results', $items->orderBy('name')->get());

		return $results;
	}

	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []
	): ViewModelBase {
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
