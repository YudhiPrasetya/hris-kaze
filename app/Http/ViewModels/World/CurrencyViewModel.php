<?php

namespace App\Http\ViewModels\World;

use App\Http\Requests\FormRequestInterface;
use App\Http\ViewModels\ViewModelBase;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Models\World\Country;
use App\Models\World\State;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class CurrencyViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'currency';
		$this->routeKey = 'currency';
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

	public function select2SearchByCountry(Request $request, Country $country): Collection {
		$search = $request->get('search', null);
		$results = collect([]);
		$items = null;

		if (!empty($search)) {
			$items = State::search($search)->where('country_id', $country->iso);
		}
		else {
			$items = $country->currencies();
		}

		$results->offsetSet('results', $items->orderBy('code')->get());

		return $results;
	}
}
