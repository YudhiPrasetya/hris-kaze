<?php

namespace App\Exceptions;

use App\Libraries\HttpStatusCodes;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;


class Handler extends ExceptionHandler {
	protected $exception;

	protected $request;

	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		HttpException::class,
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register() {
		$this->reportable(function (Throwable $e) {
			//
		});
	}

	/**
	 * Report or log an exception.
	 *
	 * @param \Throwable $exception
	 *
	 * @return void
	 *
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function report(Throwable $exception) {
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Throwable               $exception
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception) {
		if ($this->isHttpException($exception)) {
			$this->exception = $exception;

			return $this->renderHttpException($exception);
		}

		return parent::render($request, $exception);
	}

	protected function registerErrorViewPaths() {
		if (empty(theme()->current())) {
			// throw new ThemeNotFoundException(null);
			$theme = config('theme.active');
			theme()->set($theme);
		}

		$theme = theme()->current(true)->get('path');

		if (File::exists($theme . '/views/errors') && File::isDirectory($theme . '/views/errors')) {
			$themeView = theme()->current(true)->get('path');
			$paths = collect(config('view.paths'));
			$paths->prepend($theme . '/views');

			$factory = View::replaceNamespace(
				'errors',
				$paths->map(
					function ($path) {
						return "{$path}/errors";
					}
				)->all()
			);
			$factory->share('httpStatusCode', new HttpStatusCodes());
			$factory->share('isLoggedIn', !empty(Request::user()));
			if ($this->exception) $factory->share('message', $this->exception->getMessage());
		}
		else {
			parent::registerErrorViewPaths();
		}
	}
}
