<?php

namespace App\Http\Controllers;

use App\Http\ViewModels\TrackerViewModel;
use App\Http\ViewModels\ViewModel;
use App\Repositories\Eloquent\VehicleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;


class TrackerController extends Controller {
	private TrackerViewModel $viewModel;

	public function __construct(VehicleRepository $repository) {
		$this->viewModel = new TrackerViewModel($repository);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return TrackerViewModel|ViewModel
	 */
	public function index(): ViewModel|TrackerViewModel {
		return $this->viewModel->view("pages.tracker.map");
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createSesion(): JsonResponse {
		$response = Http::asForm()
		                ->withHeaders([
			                'Access-Control-Allow-Credentials: true',
			                'Access-Control-Allow-Methods: POST, GET, OPTIONS',
			                'Access-Control-Allow-Headers: Origin',
		                ])
		                ->post('http://35.188.167.11:8082/api/session',
			                [
				                'email' => 'admin',
				                'password' => 'M}_zxd+BjkhGxc3F',
			                ]);

		$_cookies = $response->header('Set-Cookie');
		$_cookies = str_replace('path=/; ', '', $_cookies);

		return Response::json(['OK'], headers: ['Set-Cookie' => $_cookies]);
	}
}
