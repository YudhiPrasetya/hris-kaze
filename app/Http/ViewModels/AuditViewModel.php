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
 * @file   AuditViewModel.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\ViewModels;

use App\Http\Requests\FormRequestInterface;
use App\Libraries\PrettyDateTime;
use App\Managers\Form\FormBuilder;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;



class AuditViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'audit';
		$this->routeKey = 'audit';
	}

	public function setModel(ModelInterface $model): static {
		$this->model = $model;
		return $this;
	}

	public function createForm(string $method, string $route, ?ModelInterface $audit = null, ?string $formClass = null, array $options = []): self {
		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $audit): bool {
		return false;
	}

	public function delete(Request $request, ModelInterface $audit): Redirector|RedirectResponse {
		return redirect(route('audit.show', ['audit' => $audit->id]));
	}

	public function new(FormRequestInterface $request): mixed {
		return false;
	}

	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		$events = [
			'updated' => 'Update',
			'created' => 'Create',
			'deleted' => 'Delete',
		];
		$actions = [
			'updated' => 'Update',
			'created' => 'Add',
			'deleted' => 'Delete',
		];
		$this->aliases = ['at' => 'created_at', 'user' => 'username'];

		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);
		$query = $this->getBaseQuery($request, ...$columns);
		$columns = $this->getDefaultColumns(...$columns);
		$results = $query->with(['user:id,username,name,email'])
		                 ->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self, $events, $actions) {
			if ($key == 'rows') {
				return collect($item)->map(function ($result, $i) use ($self, $events, $actions) {
					$result['user'] = empty($result['user']) ? '<span class="text-success">Generate by system</span>' :
						$this->createLink($result['user']['username'], route('user.show', $result['user']['id']));
					$type = new ReflectionClass($result['auditable_type']);
					//$array = preg_split('/(?=[A-Z])/', $type->getShortName());
					$result['auditable_type'] = $type->getShortName();
					$keyEvent = $result['event'];
					$result['event'] = Str::title($events[$keyEvent]);

					if ($events[$keyEvent] === "Update" && $result['auditable_type'] === "User" && $result['old_values']->has('last_login')) {
						$result['event'] = "Login";
					}

					$result['created_at'] = PrettyDateTime::parseString($result['created_at']->format('Y-m-d H:i:s'));
					$result['updated_at'] = PrettyDateTime::parseString($result['updated_at']->format('Y-m-d H:i:s'));
					$result['at'] = $result['event'] === 'created' ? $result['created_at'] : $result['updated_at'];
					//$result['action'] = Str::ucfirst($actions[$keyEvent] . ' ' . Str::lower($type->getShortName()));
					$result = $self->addDefaultListActions($result, 'edit', 'destroy');

					return $result;
				});
			}

			return $item;
		});
	}
}
