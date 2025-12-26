<?php

namespace App\Http\ViewModels;

use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class SettingsViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'task';
		$this->routeKey = 'task';
	}

	/**
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): ViewModelBase {
		$this->form = $this->formBuilder->create($formClass);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $model = null): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$fields = $this->getFormFields();
		$repository = $this->getRepository();

		$fields->each(function($item, $key) use($repository, $fields) {
			$result = $repository->findOneBySectionAndKey('attendance', $key);
			if ($key === 'cutoff' && $item === 'user_defined') {
				$item = $fields->get('cutoff_date');
			}
			/**
			 * @see \App\Models\ModelBase::__set
			 */
			if ($result) $result->value = $item;
		});

		return true;
	}

	public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse {
		// TODO: Implement delete() method.
	}

	/**
	 * @inheritDoc
	 */
	public function new(FormRequestInterface $request): mixed {
		return false;
	}

	public function list(Request $request, ...$columns): Collection {
		return collect([]);
	}

	public function getConfig() {
		$repository = $this->getRepository();
		$result = [];
		foreach ($repository->findBySection('attendance') as $value) {
			$result[$value['key']] = $value['value'];
		}

		return $result;
	}
}
