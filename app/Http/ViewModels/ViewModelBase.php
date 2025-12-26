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
 * @file   ViewModelBase.php
 * @date   2020-10-30 5:40:29
 */

namespace App\Http\ViewModels;

use App\Http\Requests\FormRequestInterface;
use App\Managers\Form\Form;
use App\Managers\Form\FormBuilder;
use App\Models\ModelBase;
use App\Models\ModelInterface;
use App\Repositories\EloquentRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function _\get;


abstract class ViewModelBase extends ViewModel {
	protected const ALL_FIELDS = ['*'];

	public ?Form $form;

	public ?FormBuilder $formBuilder;

	public array $data = [];

	protected ?ModelInterface $model;

	protected ?ModelInterface $parentModel;

	protected ?string $modelPrimaryKey;

	protected ?EloquentRepositoryInterface $repository;

	/**
	 * @var string
	 */
	protected string $routeBasename = '';

	protected ?string $routeKey;

	protected string $uri;

	protected array $aliases = [];

	public function __construct(?EloquentRepositoryInterface $repository = null, ?FormBuilder $formBuilder = null) {
		$this->setRepository($repository);
		$this->setFormBuilder($formBuilder);
		$this->model = null;
		$this->parentModel = null;
		$this->routeKey = null;
		$this->form = null;
		$this->modelPrimaryKey = null;
		$this->uri = \Illuminate\Support\Facades\Request::url();
	}

	public function setFormBuilder(?FormBuilder $formBuilder = null): void {
		$this->formBuilder = $formBuilder;
	}

	public function getRepository(): ?EloquentRepositoryInterface {
		return $this->repository;
	}

	public function setRepository(?EloquentRepositoryInterface $repository = null): void {
		$this->repository = $repository;
	}

	/**
	 * @param string $method
	 * @param ModelInterface $model
	 * @param string $route
	 * @param string|null $formClass
	 * @param array $options
	 *
	 * @return $this
	 */
	abstract public function createForm(string $method, string $route, ?ModelInterface $model = null, ?string $formClass = null, array $options = []): self;

	/**
	 * @param FormRequest $request
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
	abstract public function update(FormRequestInterface $request, ModelInterface $model): bool;

	/**
	 * @param Request $request
	 * @param ModelInterface $model
	 *
	 * @return Redirector|RedirectResponse
	 */
	abstract public function delete(Request $request, ModelInterface $model): Redirector|RedirectResponse;

	/**
	 * @param FormRequest $request
	 *
	 * @return mixed
	 */
	abstract public function new(FormRequestInterface $request): mixed;

	public function model(): ?ModelInterface {
		return $this->model;
	}

	public function parentModel(): ?ModelInterface {
		return $this->parentModel;
	}

	public function isCreate(): bool {
		return Str::afterLast($this->uri, '/') === 'create';
	}

	public function isEdit(): bool {
		return Str::afterLast($this->uri, '/') === 'edit';
	}

	/**
	 * @param Request $request
	 * @param mixed ...$columns
	 *
	 * @return Collection
	 */
	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		$results = $this->getPaginatedList($request, $this->repository, ...$columns);
		$rows = $results->get('rows')->map(function ($result, $key) use ($self) {
			return $self->addDefaultListActions($result);
		});
		$results->offsetSet('rows', $rows);

		return $results;
	}

	/**
	 * @param Request $request
	 * @param EloquentRepositoryInterface $repository
	 * @param mixed ...$columns
	 *
	 * @return Collection
	 */
	protected function getPaginatedList(Request $request, EloquentRepositoryInterface $repository, ...$columns): Collection {
		$columns = $this->getDefaultColumns(...$columns);
		$self = $this;

		list($offset, $limit, $sort, $order, $search) = $this->getDefaultRequestParam($request);

		$query = $repository->getQueryBuilder();
		if ($search) $query->whereLike(['*'], $search);
		if ($sort) $query->orderBy($sort, $order);
		$results = $query->paginate($limit, $columns->toArray(), 'offset', $offset == 0 ? $offset + 1 : ($offset / $limit) + 1)
		                 ->toArray();

		return $this->prepareForResponse($results, $offset);
	}

	protected function getDefaultColumns(...$columns): Collection {
		$columns = count($columns) == 0 ? ['*'] : $columns;

		$this->modelPrimaryKey = $this->repository->getModel()->getPrimaryKey();
		if (in_array('*', $columns)) {
			$columns = $this->repository->getModel()->getFields()->toArray();
		}
		else {
			if (!in_array($this->modelPrimaryKey, $columns)) $columns[] = $this->modelPrimaryKey;
			if (!in_array($this->repository->getModel()->CREATED_AT, $columns)) $columns[] = $this->repository->getModel()->CREATED_AT;
			if (!in_array($this->repository->getModel()->UPDATED_AT, $columns)) $columns[] = $this->repository->getModel()->UPDATED_AT;
		}

		// if (!in_array('deleted_at', $columns)) $columns[] = 'deleted_at';
		return collect($columns)->filter(fn($value) => !empty($value));
	}

	protected function getDefaultRequestParam(Request $request, int $offset = 0, int $limit = 25, string $sort = null, string $order = 'ASC'): array {
		$offset = $request->get('offset', $offset);
		// $offset == 0 ? $offset + 1 : ($offset / $limit) + 1;
		$limit = $request->get('limit', $limit);
		$sort = $request->get('sort', $sort);
		$order = $request->get('order', $order);
		$search = $request->get('search', null);
		$date = $request->get('date', date('Y-m-d'));
		$start = new \DateTime($request->get('start', date('Y-m-d')));
		$end = new \DateTime($request->get('end', date('Y-m-d')));

		return [$offset, $limit, $sort, $order, $search, $date, $start, $end];
	}

	protected function prepareForResponse(array $results, $offset, ?callable $callback = null): Collection {
		$self = $this;
		$results = collect($results);
		$results->offsetSet('rows',
			collect($results->get('data'))->map(function ($category, $key) use ($self, $offset, $callback) {
				$category = collect($category)->map(function ($value, $k) use ($callback) {
					//					if (Str::contains($value, '-') || Str::contains($value, ':')) {
					//						if ((Str::endsWith($k, '_at') || $k == 'last_login' || date_parse($value) !== false) && (!empty($value) || strlen($value) > 1)) {
					//							return (new DateTime($value))->format('Y-m-d H:i:s');
					//						}
					//					}

					return is_date($value) ? parse_date($value) : $value;
				});

				if (!$category->has('id')) $category->put('id', $category->get($self->modelPrimaryKey));
				$category = is_callable($callback) ? $callback($category, $key) : $category;

				return $category->put('no', $key + $offset + 1)->toArray();
			}));

		$results->offsetUnset('data');
		$results->offsetUnset('first_page_url');
		$results->offsetUnset('last_page_url');
		$results->offsetUnset('links');
		$results->offsetUnset('next_page_url');
		$results->offsetUnset('path');
		$results->offsetUnset('prev_page_url');

		return $results;
	}

	protected function addDefaultListActions(array $result, ...$exceptions): Collection {
		$actions = [
			'show'    => [
				'icon'    => 'fad fa-eye',
				'attr'    => [
					'class' => 'btn btn-sm btn-falcon-primary',
				],
				'type'    => 'a',
				'tooltip' => 'View',
			],
			'edit'    => [
				'icon'    => 'fad fa-pencil',
				'attr'    => [
					'class' => 'btn btn-sm btn-falcon-success',
				],
				'type'    => 'a',
				'tooltip' => 'Edit',
			],
			'destroy' => [
				'icon'    => 'fad fa-trash',
				'attr'    => [
					'class' => 'btn btn-sm btn-falcon-danger',
				],
				'type'    => 'button',
				'tooltip' => 'Delete',
			],
		];

		$currentRoute = $this->routeBasename; // Str::beforeLast(Route::getCurrentRoute()->getName(), '.');
		$user = Auth::user();
		//$class = Str::of(class_basename(get_class($this)))->replace('QR', 'Qr')->replace('ViewModel', '');
		$self = $this;

		$actions = collect($actions)->map(function ($action, $key) use ($self, $user, $currentRoute, &$perms) {
			$act = Str::of($currentRoute)->append(".", $key);
			if (!$user->hasDirectPermission("{$self->routeBasename}.$key")) return null;

			return array_merge($action, ['route' => "{$self->routeBasename}.$key"]);
		});

		return collect([
			'actions' => $actions
				->map(function ($item, $key) use ($self, &$result, $user, $exceptions) {
					if (in_array($key, $exceptions)) return null;
					if ($item) {
						$id = $result[$self->modelPrimaryKey];
						if (Route::has("{$self->routeBasename}.$key")) {
							$params = ["{$self->routeKey}" => $id];
							if (isset($result['_params'])) {
								$params = array_merge($params, $result['_params']);
							}

							$route = route("{$self->routeBasename}.$key", $params);

							if ($key == 'destroy') {
								$token = csrf_token();
								$item['content'] = <<<FORM
<form id="delete-form-{$id}" action="{$route}" method="POST" style="display: none;">
	<input type="hidden" name="_method" value="DELETE">
	<input type="hidden" name="_token" value="{$token}">
</form>
FORM;
								$item['attr'] = array_merge($item['attr'],
									[
										'onclick' => 'event.preventDefault(); document.getElementById("delete-form-' . $id . '").submit();',
									]);
							}
							else {
								$item['attr'] = array_merge($item['attr'], ['href' => $route]);
							}
						}
					}

					return $item;
				})
				// Non-null item
				->filter(function ($item) {
					return !empty($item);
				}),
		])->merge(collect($result)->filter(function ($item, $k) {
			return !in_array($k, ['_params']);
		}));
	}

	public function setModel(ModelInterface $model): static {
		$this->model = $model;
		$this->form->setModel($this->model);
		$this->form->setFormOption('model', $model);

		return $this;
	}

	public function setParentModel(ModelInterface $model): void {
		$this->parentModel = $model;
	}

	public function createLink(string $label, string $url): string {
		return '<a href="' . $url . '" target="' . (parse_url($url, PHP_URL_HOST) == env('APP_DOMAIN') ? '_self' : '_blank') . '">' . $label . '</a>';
	}

	public function moneyFormat($amount, $currency, ?string $title = null) {
		$cell = '<div class="row row-equal flex-nowrap" data-toggle="tooltip" title="'.$title.'"><div class="col-4 text-left">%s</div><div class="col-8 text-right">%s</div></div>';
		$amount = number_format($amount, 2, ',', '.');
		//$amt = explode(',', $amount);
		//if ($amt[1] === '00') unset($amt[1]);
		//$amount = $amt[0];

		return sprintf($cell, $currency, $amount);
		// return sprintf($cell, number_format($amount, 2, ',', '.'));
		// return sprintf($cell, number_format(round(floatval($amount)), 0, ',', '.'));
	}

	public function numberFormat($number): string {
		return number_format($number, 0, ',', '.');
	}

	final public function maskedPan(string $pan, int $start = 6, int $end = 4, string $maskedChar = '*'): string {
		$masked = substr($pan, 0, strpos($pan, 'D'));
		$maskedLen = strlen($masked) - ($start + $end);

		return substr($masked, 0, $start) . str_repeat($maskedChar, $maskedLen) . substr($masked, $end * -1);
	}

	final public function percentage($value): string {
		return floatval($value) . '%';
	}

	public function refererUrl(Request $request): string {
		return request()->headers->get('referer');
	}

	public function setData(mixed $data) {
		$this->data = $data;
	}

	public function addData(string $key, mixed $data) {
		$this->data[$key] = $data;
	}

	protected function getColumns(ModelBase $model, ...$columns): Collection {
		$columns = count($columns) === 0 ? ['*'] : $columns;

		$modelPrimaryKey = $model->getPrimaryKey();
		if (in_array('*', $columns)) {
			$columns = $model->getFields()->toArray();
		}
		else {
			if (!in_array($modelPrimaryKey, $columns)) $columns[] = $modelPrimaryKey;
			if (!in_array($model->CREATED_AT, $columns)) $columns[] = $model->CREATED_AT;
			if (!in_array($model->UPDATED_AT, $columns)) $columns[] = $model->UPDATED_AT;
		}

		// if (!in_array('deleted_at', $columns)) $columns[] = 'deleted_at';
		return collect($columns)->filter(fn($value) => !empty($value));
	}

	protected function getBaseQuery(Request $request, ...$columns): Builder {
		$self = $this;

		[$offset, $limit, $sort, $order, $search] = $this->getDefaultRequestParam($request);

		$columns = $this->getDefaultColumns(...$columns);
		$query = $this->repository->getQueryBuilder();

		if ($search) {
			$query->whereLike($columns->toArray(), $search);
		}

		if ($columns->search($sort) || array_key_exists($sort, $this->aliases)) {
			if (array_key_exists($sort, $this->aliases)) {
				$sort = $this->aliases[$sort];
			}

			return $query->orderBy($sort, $order);
		}

		return $query/*->orderBy($sort, $order)*/ ;
	}

	protected function convertImage(Request $request, string $key): ?string {
		if ($request->hasFile($key)) {
			$image = $request->file($key);

			return "data:{$image->getMimeType()};base64," . base64_encode($image->get());
		}

		return $this->model?->$key;
	}

	protected function getFormFields(bool $with_nulls = false): Collection {
		return collect($this->form->getFieldValues($with_nulls));
	}

	protected function toBool($value): bool {
		return !(empty($value) || $value === '0' || $value === 0 || $value === false);
	}

	protected function prepareEachResultForResponse(array $results, $offset, bool $hasAction = true, callable $callback = null): Collection {
		$self = $this;

		return $this->prepareForResponse($results, $offset)->map(function ($item, $key) use ($self, $hasAction, $callback) {
			if ($key === 'rows') {
				return collect($item)->map(function ($result, $i) use ($self, $hasAction, $callback) {
					$table = '';
					if ($this->repository) $table = Str::ucfirst(Str::camel($this->repository->getModel()->getTableName()));
					$method = "prepare{$table}Result";

					if (method_exists($self, $method)) {
						$result = $self->$method($result);
					}
					else {
						if (is_callable($callback)) {
							$result = $callback($result, $i);
						}
						else {
							$result = $self->prepareResult($result);
						}
					}

					return $hasAction ? $self->addDefaultListActions($result) : $result;
				});
			}

			return $item;
		});
	}

	protected function prepareResult($result) {
		return $result;
	}

	public function download(string $file, string $suggestionFilename, bool $deleteAfterSend = true): BinaryFileResponse {
		// $contentType = 'application/vnd.ms-excel';
		$date = DateTimeImmutable::createFromMutable(new \DateTime());
		$date = $date->setTimezone(new \DateTimeZone('UTC'));

		return response()->download(
			$file,
			$suggestionFilename,
			[
				[
					'Content-Transfer-Encoding' => 'binary',
					'Cache-Control'             => 'max-age=0', // HTTP/1.1
					'Cache-Control'             => 'max-age=1', // HTTP/1.1
					'Cache-Control'             => 'cache, must-revalidate',
					'Pragma'                    => 'public',
					'Expires'                   => 'Sat, 12 Nov 1977 23:50:00 GMT', // Date in the past
					'Last-Modified'             => $date->format('D, d M Y H:i:s') . ' GMT',
				],
			])->deleteFileAfterSend($deleteAfterSend);
	}
}
