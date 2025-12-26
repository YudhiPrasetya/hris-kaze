<?php

use App\Http\Controllers\AnnualLeaveController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMachineController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FingerPrintDeviceController;
use App\Http\Controllers\FingerPrintDeviceDataController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\iClockController;
use App\Http\Controllers\ImagePlaceholderController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReasonForLeaveController;
use App\Http\Controllers\Settings\AttendanceController as SettingAttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\World\CityController;
use App\Http\Controllers\World\CountryController;
use App\Http\Controllers\World\CurrencyController;
use App\Http\Controllers\World\DistrictController;
use App\Http\Controllers\World\StateController;
use App\Http\Controllers\World\VillageController;
use App\Http\Controllers\WorkingShiftController;
use App\Http\Controllers\OvertimeController;
use App\Models\Overtime;
use App\Models\ReasonForLeave;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if(!function_exists('customResource')){
    function customResource(string $uri, array $options) {
        $uris = [
            //			'/merchant.category'       => [MerchantCategoryController::class, 'merchant.category', 'category'],
        ];

        $param = $options[2] ?? Str::afterLast($uri, '/');
        $except = collect($options[3] ?? []);
        //@formatter:off
        $base = sprintf("%s/{%s}", $uri, $param);
        $routes = collect([
            "index"   => ['get',    $uri],
            "create"  => ['get',    $uri . "/create"],
            "show"    => ['get',    $base],
            "edit"    => ['get',    $base. "/edit"],
            "update"  => ['put',    $base],
            "store"   => ['post',   $uri],
            "destroy" => ['delete', $base],
        ]);
        //@formatter:on

        $routes->each(function ($v, $route) use ($options, $except) {
            if ($except->search($route) === false) {
                Route::{$v[0]}($v[1], [$options[0], $route])->name(sprintf("%s.%s", $options[1], $route));
            }
        });
    }
}

Route::middleware([])->group(function () {
	// iClockController
	Route::get('/iclock/cdata', [iClockController::class, 'cdata'])->name('iclock.cdata');
	// Route::post('/iclock/cdata', [iClockController::class, 'cdata'])->name('iclock.cdata');
	Route::post('/iclock/cdata', [iClockController::class, 'cdata'])->name('iclock.cdata.post');
	Route::get('/iclock/getrequest', [iClockController::class, 'getRequest'])->name('iclock.getrequest');
});

Route::domain(config('app.domain'))->group(function () {
	Route::get('/',
		function () {
			return view('welcome');
		});

	// Image Placeholder
	Route::get('/icon/{size?}/{bgColor?}/{textColor?}{ext?}', [ImagePlaceholderController::class, 'icon'])
	     ->name('icon-placeholder')
	     ->where(
		     [
			     'size'      => '^[0-9x]+',
			     'bgColor'   => '^[a-fA-F0-9]{6}$',
			     'textColor' => '^[a-fA-F0-9]{6}$',
			     //'quality'   => '^[0-9]{1,3}$',
			     'ext'       => '^\.(png|gif|webp|jpeg|wbmp)$',
		     ]
	     )
		//->uses('ImagePlaceholderController@placeholder')
		 ->setDefaults(
			[
				'size'      => 64,
				'bgColor'   => '#cdcdcd',
				'textColor' => '#fafafa',
				'ext'       => '.png',
			]
		);
	Route::get('/placeholder/{size?}/{bgColor?}/{textColor?}{ext?}', [ImagePlaceholderController::class, 'placeholder'])
	     ->name('image-placeholder')
	     ->where(
		     [
			     'size'      => '^[0-9x]+',
			     'bgColor'   => '^[a-fA-F0-9]{6}$',
			     'textColor' => '^[a-fA-F0-9]{6}$',
			     //'quality'   => '^[0-9]{1,3}$',
			     'ext'       => '^\.(png|gif|webp|jpeg|wbmp)$',
		     ]
	     )
		//->uses('ImagePlaceholderController@placeholder')
		 ->setDefaults(
			[
				'size'      => 64,
				'bgColor'   => '#cdcdcd',
				'textColor' => '#fafafa',
				'ext'       => '.png',
			]
		);

	Route::middleware(['auth', 'web'/*, 'verified'*/])->group(function () {
		Route::get('/', [HomeController::class, 'index'])->name('home');

		// Route::resource('annual.leave', AnnualLeaveController::class);
		Route::resource('attendance', AttendanceController::class);
		Route::match(['POST', 'GET'], '/report/attendance', [AttendanceController::class, 'report'])->name('report.attendance');
		Route::match(['GET'], '/report/attendance/{employee}', [AttendanceController::class, 'reportDetail'])->name('report.attendance.employee');
		Route::match(['GET'], '/report/attendance/{employee}/download', [AttendanceController::class, 'downloadEmployeeReport'])->name('report.attendance.employee.download');

		Route::match(['POST', 'GET'],'/report/salary', [SalaryController::class, 'report'])->name('report.salary');

		Route::resource('user', UserController::class);
		Route::resource('employee', EmployeeController::class);

		Route::resource('task', TaskController::class);
		Route::get('/task/{task}/confirm', [TaskController::class, 'confirm'])->name('task.employee.confirm');
		Route::get('/task/{task}/done', [TaskController::class, 'done'])->name('task.employee.done');

		Route::get('/tracker', [TrackerController::class, 'index'])->name('tracker');

		Route::resource('assignment', AssignmentController::class);
		Route::resource('customer', CustomerController::class);
		Route::resource('customer.machine', CustomerMachineController::class);
		Route::resource('vehicle', VehicleController::class);
		Route::resource('machine', MachineController::class);
		Route::resource('calendar', CalendarEventController::class);
		Route::resource('workingshift', WorkingShiftController::class);
        Route::resource('position', PositionController::class);
        Route::resource('reasonforleave', ReasonForLeaveController::class);
		customResource('/settings/calendar', [CalendarEventController::class, 'settings.calendar']);
		Route::get('/settings/attendance', [SettingAttendanceController::class, 'index'])->name('settings.attendance.show');
		Route::post('/settings/attendance', [SettingAttendanceController::class, 'update'])->name('settings.attendance.edit');

        // Route::get('/settings/jobtitle', [JobTitleController::class, 'index'])->name('settings.jobtitle.show');
        // Route::post('/settings/jobtitle', [JobTitleController::class, 'create'])->name('settings.jobtitle.create');
        Route::resource('jobtitle', JobTitleController::class);

        // Route::get('/fp-devices', [FingerPrintDeviceController::class, 'index'])
        // ->name('fp-devices.index');

        Route::resource('fingerprintdevice', FingerPrintDeviceController::class);
        // Route::resource('fingerprintdevicedata', FingerPrintDeviceDataController::class);
        Route::resource('devicelog', FingerPrintDeviceDataController::class);

        // Permit
        Route::resource('permit', PermitController::class);

        // Leave
        Route::resource('leave', LeaveController::class);

        // Overtime
        Route::resource('ot', OvertimeController::class);

		Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
		Route::get('/audit/{audit}', [AuditController::class, 'show'])->name('audit.show');

		Route::get('/country/{country?}.svg', [CountryController::class, 'countryFlag'])
		     ->setDefaults(['country' => 'ID'])
		     ->name('country.flag');

		// API that used for web only.
		// You have to access from web to be able to access this API
		// Route::middleware(['auth', 'api'])->group(function () {
			Route::post('/api/v1/tracker/create-session', [TrackerController::class, 'createSesion'])->name('api.tracker.session');
			Route::get('/api/v1/permission/role/{role}', [PermissionController::class, 'apiPermissionByRole'])->name('api.permission.role');

			// Employee
			Route::get('/api/v1/working-shift', [WorkingShiftController::class, 'list'])->name('api.working-shift');
			Route::get('/api/v1/employee', [EmployeeController::class, 'list'])->name('api.employee');
			Route::get('/api/v1/employee/select', [EmployeeController::class, 'select2List'])->name('api.employee.select');
			Route::get('/api/v1/employee/attendance/available', [EmployeeController::class, 'selectAvailableEmployee'])
			     ->name('api.employee.attendance.available');
			Route::get('/api/v1/employee/{employee}/payroll', [EmployeeController::class, 'getpayroll'])->name('api.employee.payroll');

			Route::get('/api/v1/task', [TaskController::class, 'list'])->name('api.task');
			Route::get('/api/v1/task/employee/{employee}', [TaskController::class, 'getByEmployee'])->name('api.task.employee');

			// Assignment / Service Report
			Route::get('/api/v1/assignment', [AssignmentController::class, 'list'])->name('api.assignment');
			Route::get('/api/v1/assignment/customer/{customer}', [AssignmentController::class, 'getByCustomer'])->name('api.assignment.customer');
			Route::get('/api/v1/assignment/employee/{employee}', [AssignmentController::class, 'getByEmployee'])->name('api.assignment.employee');

			Route::get('/api/v1/customer', [CustomerController::class, 'list'])->name('api.customer');
			Route::get('/api/v1/customer/{customer}/machines', [CustomerMachineController::class, 'list'])->name('api.customer.machine');
			Route::get('/api/v1/customer/{customer}/machines.json', [CustomerMachineController::class, 'selectMachineByCustomer'])
			     ->name('api.customer.machine.select');

			Route::get('/api/v1/vehicle', [VehicleController::class, 'list'])->name('api.vehicle');

			Route::get('/api/v1/machine', [MachineController::class, 'list'])->name('api.machine');

			Route::get('/api/v1/attendance', [AttendanceController::class, 'list'])->name('api.attendance');
			Route::get('/api/v1/attendance/employee/{employee}', [AttendanceController::class, 'byEmployee'])->name('api.attendance.employee');
			Route::get('/api/v1/attendance/reports', [AttendanceController::class, 'reports'])->name('api.attendance.report');
			Route::get('/api/v1/attendance/reports/{employee}', [AttendanceController::class, 'getReportDetail'])->name('api.attendance.report.employee');
			Route::get('/api/v1/attendance/config', [SettingAttendanceController::class, 'getConfig'])->name('api.settings.attendance');

			Route::get('/api/v1/salary/reports', [SalaryController::class, 'reports'])->name('api.salary.report');

			Route::get('/api/v1/annual/{employee}', [AnnualLeaveController::class, 'selectByEmployee'])->name('api.annual.employee');
			Route::get('/api/v1/calendar/events/national', [CalendarEventController::class, 'nationalEvents'])->name('api.calendar.events.national');

			// User
			Route::get('/api/v1/user', [UserController::class, 'list'])->name('api.user');
			Route::get('/api/v1/user/{user}/detail', [UserController::class, 'getUserDetail'])->name('api.user.detail');

			// Audit Trail
			Route::get('/api/v1/audit', [AuditController::class, 'list'])->name('api.audit');
			Route::get('/api/v1/audit.latest', [AuditController::class, 'latest'])->name('api.audit.latest');

			// Location
			Route::get('/api/v1/currency/{country?}.json', [CurrencyController::class, 'getByCountry'])
			     ->setDefaults(['country' => 'ID'])
			     ->name('api.currency.country');

			Route::get('/api/v1/states/{country?}.json', [StateController::class, 'getByCountry'])
			     ->setDefaults(['country' => 'ID'])
			     ->name('api.state.country');

			Route::get('/api/v1/cities/{state?}.json', [CityController::class, 'getByState'])
			     ->setDefaults(['state' => '1620'])
			     ->name('api.city.state');

			Route::get('/api/v1/districts/{city?}.json', [DistrictController::class, 'getByCity'])
			     ->setDefaults(['city' => '143160'])
			     ->name('api.district.city');

			// Route::get('/api/v1/villages/{district?}.json', [VillageController::class, 'getByDistrict'])
			//      ->setDefaults(['district' => '1959'])
			//      ->name('api.district.city');

			Route::get('/api/v1/villages/{district?}.json', [VillageController::class, 'getByDistrict'])
			     ->setDefaults(['district' => '1959'])
			     ->name('api.villages.district');

            // Finger print devices
			Route::get('/api/v1/fingerprintdevice', [FingerPrintDeviceController::class, 'list'])->name('api.fingerprintdevice');

            // Finger print devices pull data
            Route::get('/api/v1/devicelog', [FingerPrintDeviceDataController::class, 'list'])->name('api.devicelog');

            // Job title
			Route::get('/api/v1/jobtitle', [JobTitleController::class, 'list'])->name('api.jobtitle');

            // Position
            Route::get('/api/v1/position', [PositionController::class, 'list'])->name('api.position');

            // Reason for leave
            Route::get('/api/v1/reasonforleave', [ReasonForLeaveController::class, 'list'])->name('api.reasonforleave');

            // Permit
            Route::get('/api/v1/permit', [PermitController::class, 'list'])->name('api.permit');

            // Leave
            Route::get('/api/v1/leave', [LeaveController::class, 'list'])->name('api.leave');

            // Overtime
            Route::get('/api/v1/ot', [OvertimeController::class, 'list'])->name('api.ot');
		// });
	});

	Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard',
		function () {
			return view('dashboard');
		})->name('dashboard');
});
