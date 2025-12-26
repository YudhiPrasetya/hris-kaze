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
 * @file   RepositoryServiceProvider.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Providers;

use App\Repositories\AnnualLeaveRepositoryInterface;
use App\Repositories\AssignmentEmployeeRepositoryInterface;
use App\Repositories\AssignmentPartRepositoryInterface;
use App\Repositories\AssignmentRepositoryInterface;
use App\Repositories\AttendanceLogRepositoryInterface;
use App\Repositories\AttendanceReasonRepositoryInterface;
use App\Repositories\AttendanceRepositoryInterface;
use App\Repositories\AuditRepositoryInterface;
use App\Repositories\CalendarEventRepositoryInterface;
use App\Repositories\CustomerMachineRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\Eloquent\AnnualLeaveRepository;
use App\Repositories\Eloquent\AssignmentEmployeeRepository;
use App\Repositories\Eloquent\AssignmentPartRepository;
use App\Repositories\Eloquent\AssignmentRepository;
use App\Repositories\Eloquent\AttendanceLogRepository;
use App\Repositories\Eloquent\AttendanceReasonRepository;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\AuditRepository;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\CustomerMachineRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\FaieldJobRepository;
use App\Repositories\Eloquent\FingerprintRepository;
use App\Repositories\Eloquent\GenderRepository;
use App\Repositories\Eloquent\MachineRepository;
use App\Repositories\Eloquent\MembershipRepository;
use App\Repositories\Eloquent\MenuRepository;
use App\Repositories\Eloquent\OperationLogRepository;
use App\Repositories\Eloquent\PasswordResetRepository;
use App\Repositories\Eloquent\PermissionRepository;
use App\Repositories\Eloquent\PersonalAccessTokenRepository;
use App\Repositories\Eloquent\PositionRepository;
use App\Repositories\Eloquent\PriorityRepository;
use App\Repositories\Eloquent\RepositoryBase;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\SalaryRepository;
use App\Repositories\Eloquent\SettingsRepository;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Eloquent\TeamInvitationRepository;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Eloquent\TelescopeEntryRepository;
use App\Repositories\Eloquent\TelescopeEntryTagRepository;
use App\Repositories\Eloquent\TelescopeMonitorRepository;
use App\Repositories\Eloquent\UserInfoRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\World\CityRepository;
use App\Repositories\Eloquent\World\ContinentRepository;
use App\Repositories\Eloquent\World\CountryNeighbourRepository;
use App\Repositories\Eloquent\World\CountryRepository;
use App\Repositories\Eloquent\World\CurrencyRepository;
use App\Repositories\Eloquent\World\DistrictRepository;
use App\Repositories\Eloquent\World\LanguageRepository;
use App\Repositories\Eloquent\World\RegionRepository;
use App\Repositories\Eloquent\World\StateRepository;
use App\Repositories\Eloquent\World\TimezoneAbbreviationRepository;
use App\Repositories\Eloquent\World\TimezoneRepository;
use App\Repositories\Eloquent\World\VillageRepository;
use App\Repositories\Eloquent\World\ZoneRepository;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\FaieldJobRepositoryInterface;
use App\Repositories\FingerprintRepositoryInterface;
use App\Repositories\GenderRepositoryInterface;
use App\Repositories\MachineRepositoryInterface;
use App\Repositories\MembershipRepositoryInterface;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\OperationLogRepositoryInterface;
use App\Repositories\PasswordResetRepositoryInterface;
use App\Repositories\PermissionRepositoryInterface;
use App\Repositories\PersonalAccessTokenRepositoryInterface;
use App\Repositories\PositionRepositoryInterface;
use App\Repositories\PriorityRepositoryInterface;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\SalaryRepositoryInterface;
use App\Repositories\SettingsRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\TeamInvitationRepositoryInterface;
use App\Repositories\TeamRepositoryInterface;
use App\Repositories\TelescopeEntryRepositoryInterface;
use App\Repositories\TelescopeEntryTagRepositoryInterface;
use App\Repositories\TelescopeMonitorRepositoryInterface;
use App\Repositories\UserInfoRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\VehicleRepositoryInterface;
use App\Repositories\World\CityRepositoryInterface;
use App\Repositories\World\ContinentRepositoryInterface;
use App\Repositories\World\CountryNeighbourRepositoryInterface;
use App\Repositories\World\CountryRepositoryInterface;
use App\Repositories\World\CurrencyRepositoryInterface;
use App\Repositories\World\DistrictRepositoryInterface;
use App\Repositories\World\LanguageRepositoryInterface;
use App\Repositories\World\RegionRepositoryInterface;
use App\Repositories\World\StateRepositoryInterface;
use App\Repositories\World\TimezoneAbbreviationRepositoryInterface;
use App\Repositories\World\TimezoneRepositoryInterface;
use App\Repositories\World\VillageRepositoryInterface;
use App\Repositories\World\ZoneRepositoryInterface;
use Illuminate\Support\ServiceProvider;


/**
 * Class RepositoryServiceProvider
 *
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider {
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->bind(EloquentRepositoryInterface::class, RepositoryBase::class);
		$this->app->bind(UserRepositoryInterface::class, UserRepository::class);
		$this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
		$this->app->bind(FaieldJobRepositoryInterface::class, FaieldJobRepository::class);
		$this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
		$this->app->bind(PersonalAccessTokenRepositoryInterface::class, PersonalAccessTokenRepository::class);
		$this->app->bind(TelescopeEntryRepositoryInterface::class, TelescopeEntryRepository::class);
		$this->app->bind(TelescopeEntryTagRepositoryInterface::class, TelescopeEntryTagRepository::class);
		$this->app->bind(TelescopeMonitorRepositoryInterface::class, TelescopeMonitorRepository::class);
		$this->app->bind(CityRepositoryInterface::class, CityRepository::class);
		$this->app->bind(ContinentRepositoryInterface::class, ContinentRepository::class);
		$this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
		$this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
		$this->app->bind(DistrictRepositoryInterface::class, DistrictRepository::class);
		$this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
		$this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
		$this->app->bind(StateRepositoryInterface::class, StateRepository::class);
		$this->app->bind(TimezoneRepositoryInterface::class, TimezoneRepository::class);
		$this->app->bind(TimezoneAbbreviationRepositoryInterface::class, TimezoneAbbreviationRepository::class);
		$this->app->bind(VillageRepositoryInterface::class, VillageRepository::class);
		$this->app->bind(ZoneRepositoryInterface::class, ZoneRepository::class);
		$this->app->bind(CountryNeighbourRepositoryInterface::class, CountryNeighbourRepository::class);
		$this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
		$this->app->bind(MembershipRepositoryInterface::class, MembershipRepository::class);
		$this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
		$this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
		$this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
		$this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
		$this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
		$this->app->bind(AnnualLeaveRepositoryInterface::class, AnnualLeaveRepository::class);
		$this->app->bind(AssignmentRepositoryInterface::class, AssignmentRepository::class);
		$this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
		$this->app->bind(AttendanceReasonRepositoryInterface::class, AttendanceReasonRepository::class);
		$this->app->bind(CalendarEventRepositoryInterface::class, CalendarEventRepository::class);
		$this->app->bind(PriorityRepositoryInterface::class, PriorityRepository::class);
		$this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
		$this->app->bind(TeamInvitationRepositoryInterface::class, TeamInvitationRepository::class);
		$this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
		$this->app->bind(AssignmentEmployeeRepositoryInterface::class, AssignmentEmployeeRepository::class);
		$this->app->bind(AssignmentPartRepositoryInterface::class, AssignmentPartRepository::class);
		$this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
		$this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
		$this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
		$this->app->bind(AttendanceLogRepositoryInterface::class, AttendanceLogRepository::class);
		$this->app->bind(CustomerMachineRepositoryInterface::class, CustomerMachineRepository::class);
		$this->app->bind(FingerprintRepositoryInterface::class, FingerprintRepository::class);
		$this->app->bind(GenderRepositoryInterface::class, GenderRepository::class);
		$this->app->bind(OperationLogRepositoryInterface::class, OperationLogRepository::class);
		$this->app->bind(SalaryRepositoryInterface::class, SalaryRepository::class);
		$this->app->bind(UserInfoRepositoryInterface::class, UserInfoRepository::class);
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		//
	}
}
