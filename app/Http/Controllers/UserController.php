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
 * @file   UserController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Http\ViewModels\UserViewModel;
use App\Http\ViewModels\ViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


class UserController extends Controller {
	private UserViewModel $viewModel;

	public function __construct(UserRepository $repository, FormBuilder $builder) {
		$this->viewModel = new UserViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return UserViewModel|ViewModel
	 */
	public function index() {
		return $this->viewModel->view('pages.user.list');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return UserViewModel|ViewModel
	 */
	public function create() {
		return $this->viewModel->createForm('POST', 'user.store', new User())
		                       ->view('pages.user.form');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param UserFormRequest $request
	 *
	 * @return Application|RedirectResponse|Redirector
	 */
	public function store(UserFormRequest $request) {
		$this->viewModel->setModel(new User());

		if (($model = $this->viewModel->new($request)) !== false) {
			return redirect(route('user.show', ['user' => $model->id]));
		}

		return redirect(route('user.create'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param User                     $user
	 *
	 * @return UserViewModel|ViewModel
	 */
	public function show(Request $request, User $user) {
		$this->viewModel->setModel($user);

		return $this->viewModel->view('pages.user.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param User $user
	 *
	 * @return UserViewModel|ViewModel
	 */
	public function edit(User $user) {
		return $this->viewModel->createForm('PUT', 'user.update', $user)
		                       ->view('pages.user.form');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UserFormRequest $request
	 * @param User            $user
	 *
	 * @return UserViewModel|ViewModel|Application|RedirectResponse|Redirector
	 */
	public function update(UserFormRequest $request, User $user) {
		$this->viewModel->setModel($user);

		if (!$this->viewModel->update($request, $user)) {
			return redirect(route('user.edit', ['user' => $user->id]));
		}

		return redirect(route('user.show', ['user' => $user->id]));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param User    $user
	 *
	 * @return Application|RedirectResponse|Redirector
	 */
	public function destroy(Request $request, User $user) {
		return $this->viewModel->delete($request, $user);
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request, 'profile_photo_path', 'name', 'username', 'email', 'enabled', 'email_verified_at', 'last_login');
	}

	public function getUserDetail(Request $request, User $user) {
		return $user;
	}
}
