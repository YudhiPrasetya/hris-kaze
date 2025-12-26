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
 * @file   UserViewModel.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\ViewModels;

use App\Http\Forms\UserForm;
use App\Http\Requests\FormRequestInterface;
use App\Http\Requests\UserFormRequest;
use App\Managers\Form\FormBuilder;
use App\Managers\Permission\PermissionRegistrar;
use App\Models\ModelInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;


class UserViewModel extends ViewModelBase {
	public function __construct(EloquentRepositoryInterface $repository, ?FormBuilder $formBuilder = null) {
		parent::__construct($repository, $formBuilder);

		$this->routeBasename = 'user';
		$this->routeKey = 'user';
		$this->form = $this->formBuilder->create(UserForm::class);
	}

	/**
	 * @param Request $request
	 * @param mixed   ...$columns
	 *
	 * @return Collection
	 */
	public function list(Request $request, ...$columns): Collection {
		$self = $this;
		$results = $this->getPaginatedList($request, $this->repository, ...$columns);
		$rows = $results->get('rows')->map(function ($result, $key) use ($self) {
			$result['email'] = '<a href="mailto:' . $result['email'] . '">' . $result['email'] . '</a>';
			$result['enabled'] = $result['enabled'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
			$result['last_login'] = !empty($result['last_login']) ? $result['last_login']->format('Y-m-d H:i:s') : null;
			$result['profile_photo_path'] =
				'<div class="avatar avatar-2xl"><img class="rounded-circle w-100" src="' . $result['profile_photo_path'] . '" /></div>';

			return $self->addDefaultListActions($result);
		});
		$results->offsetSet('rows', $rows);

		return $results;
	}

	public function createForm(string $method, string $route, ?ModelInterface $user = null, ?string $formClass = null, array $options = []): self {
		$this->setModel($user);
		$this->form->setMethod($method);
		$this->form->setUrl(route($route, ['user' => $user->id]));

		return $this;
	}

	public function update(FormRequestInterface $request, ModelInterface $user): bool {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$permissions = collect($request->get('permissions'));
		$fields = $this->getFormFields();

		if ($fields->get('password'))
			$fields->offsetSet('password', Hash::make($fields->get('password')));
		if ($fields->has('profile_photo_path'))
			$fields->offsetSet('profile_photo_path', $this->convertImage($request, 'profile_photo_path'));
		$this->setRole($request, $user);
		$fields->offsetSet('enabled', $this->toBool($fields->get('enabled', null)));

		$perms = collect([]);
		$permissions->each(function ($id, $name) use (&$perms, &$user) {
			if (($p = Permission::findByName($name))) {
				$perms->add($p);
			}
		});

		$user->syncPermissions($perms);
		$ret = $user->update($fields->toArray());

		app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

		return $ret;
	}

	private function setRole(UserFormRequest $request, User &$user): Role {
		$role = $request->post('role');
		$role = Role::findById($role);
		$perms = $role->permissions()->get();
		$request->offsetSet('role', null);

		if (!$user->hasRole($role)) {
			$user->syncPermissions($perms)
			     ->syncRoles($role);
		}

		return $role;
	}

	public function delete(Request $request, ModelInterface $user): Redirector|RedirectResponse {
		if (!User::find($user->id)->delete()) {
			$request->session()->flash('message', "Failed to delete <strong>{$user->name}</strong>");
			$request->session()->flash('alert', "danger");
		}
		else {
			$request->session()->flash('message', "Successfully delete <strong>{$user->name}</strong>.");
			$request->session()->flash('alert', "success");
		}

		return redirect(route('user.index'));
	}

	public function new(FormRequestInterface $request): mixed {
		$this->form->setRequest($request);
		$this->form->redirectIfNotValid();

		$permissions = collect($request->get('permissions'));
		$fields = $this->getFormFields();
		if ($fields->has('profile_photo_path'))
			$fields->offsetSet('profile_photo_path', $this->convertImage($request, 'profile_photo_path'));

		$role = $request->post('role');
		$role = Role::findById($fields->get('role'));
		$fields->offsetSet('password', $role->name == 'technician' ? md5($fields->get('password')) : Hash::make($fields->get('password')));
		$user = new User($fields->toArray());
		$role = $this->setRole($request, $user);

		$permissions->each(function ($id, $name) use (&$perms, &$user) {
			if (($p = Permission::findByName($name))) {
				$user->givePermissionTo($p);
			}
		});

		$ret = $user->save();
		app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

		return $ret ? $user : false;
	}

	/**
	 * Get the current sessions.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function sessions(bool $currentUser = false): Collection {
		if (config('session.driver') !== 'database') return collect();

		$request = \Illuminate\Support\Facades\Request::capture();
		$user = $currentUser ? \Illuminate\Support\Facades\Request::user() : $this->model;

		return collect(
			DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
			  ->where('user_id', $user->id)
			  ->orderBy('last_activity', 'desc')
			  ->get()
		)->map(function ($session) use ($request) {
			$agent = $this->createAgent($session);

			return (object)[
				'agent'             => [
					'is_desktop' => $agent->isDesktop(),
					'platform'   => $agent->platform(),
					'browser'    => $agent->browser(),
				],
				'ip_address'        => $session->ip_address,
				'is_current_device' => $session->id === session()->getId(),
				'last_active'       => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
			];
		});
	}

	/**
	 * Create a new agent instance from the given session.
	 *
	 * @param mixed $session
	 *
	 * @return \Jenssegers\Agent\Agent
	 */
	protected function createAgent($session): Agent {
		return tap(new Agent,
			function ($agent) use ($session) {
				$agent->setUserAgent($session->user_agent);
			});
	}
}
