<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Forms\Settings\AttendanceForm;
use App\Http\Requests\EmployeeFormRequest;
use App\Http\Requests\SettingsFormRequest;
use App\Http\ViewModels\SettingsViewModel;
use App\Http\ViewModels\ViewModel;
use App\Http\ViewModels\ViewModel as HttpViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Employee;
use App\Repositories\Eloquent\SettingsRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;


class AttendanceController extends Controller {
	private SettingsViewModel $viewModel;

	public function __construct(SettingsRepository $repository, FormBuilder $builder) {
		$this->viewModel = new SettingsViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return SettingsViewModel|ViewModel
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function index(): HttpViewModel|SettingsViewModel {
		$repository = $this->viewModel->getRepository();
		$self = $this;
		collect(['ip', 'port', 'serial_number', 'service_ip', 'service_port', 'user', 'password', 'cutoff'/*, 'start', 'end'*/])->each(function($item) use($self, $repository) {
			$result = $repository->findOneBySectionAndKey('attendance', $item);
			$self->viewModel->addData($item, $result);
		});

		return $this->viewModel->createForm('POST', 'settings.attendance.edit', formClass: AttendanceForm::class)
		                       ->view('pages.settings.attendance.form');
	}

	/**
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function update(SettingsFormRequest $request): Application|RedirectResponse|Redirector {
		$this->viewModel->createForm('POST', 'settings.attendance.edit', formClass: AttendanceForm::class);

		if (!$this->viewModel->update($request)) {
			return redirect(route('settings.attendance.show'));
		}

		return redirect(route('settings.attendance.show'));
	}

	public function getConfig() {
		return $this->viewModel->getConfig();
	}
}
