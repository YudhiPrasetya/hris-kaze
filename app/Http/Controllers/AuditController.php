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
 * @file   AuditController.php
 * @date   2021-03-17 19:54:19
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\AuditViewModel;
use App\Http\ViewModels\ViewModel;
use App\Managers\Form\FormBuilder;
use App\Models\Audit;
use App\Repositories\Eloquent\AuditRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class AuditController extends Controller {
	private AuditViewModel $viewModel;

	public function __construct(AuditRepository $repository, FormBuilder $builder) {
		$this->viewModel = new AuditViewModel($repository, $builder);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return AuditViewModel|ViewModel
	 */
	public function index() {
		return $this->viewModel->view('pages.audit.list');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Audit $audit
	 *
	 * @return AuditViewModel|ViewModel
	 */
	public function show(Audit $audit) {
		$this->viewModel->setModel($audit);

		return $this->viewModel->view('pages.audit.show');
	}

	public function list(Request $request): Collection {
		return $this->viewModel->list($request);
	}

	public function latest(Request $request): Collection {
		$request->offsetSet('sort', 'created_at');
		$request->offsetSet('order', 'desc');
		$request->offsetSet('limit', 10);

		$result = $this->viewModel->list($request);
		$result->offsetSet('last_page', 1);
		$result->offsetSet('total', 10);

		return $result;
	}
}
