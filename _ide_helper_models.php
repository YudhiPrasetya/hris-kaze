<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
/**
 * App\Models\AnnualLeave
 *
 * @property int $id
 * @property string $no
 * @property int $year
 * @property int|null $employee_id
 * @property mixed|string|null $used_at
 * @property int $approved
 * @property int|null $approved_by
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\AnnualLeaveFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave newQuery()
 * @method static \Illuminate\Database\Query\Builder|AnnualLeave onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualLeave whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|AnnualLeave withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AnnualLeave withoutTrashed()
 */
	class AnnualLeave extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Assignment
 *
 * @property int $id
 * @property string $service_no
 * @property string $purchase_order_no
 * @property int $customer_id
 * @property string $destination
 * @property int $is_chargeable
 * @property string $product_code
 * @property int $customer_machine_id
 * @property string $work_detail
 * @property string $note
 * @property int|null $is_completed
 * @property mixed|string|null $next_service_date
 * @property mixed|string $service_date
 * @property int|null $vehicle_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\ModelStatus\Status|null $currentStatus
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\CustomerMachine|null $customerMachine
 * @property-read string $status
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AssignmentPart[] $parts
 * @property-read int|null $parts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\ModelStatus\Status[] $statuses
 * @property-read int|null $statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AssignmentEmployee[] $technicians
 * @property-read int|null $technicians_count
 * @property-read \App\Models\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment currentStatus($names)
 * @method static \Database\Factories\AssignmentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newQuery()
 * @method static \Illuminate\Database\Query\Builder|Assignment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment otherCurrentStatus($names)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCustomerMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereIsChargeable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereNextServiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment wherePurchaseOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereServiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereServiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereWorkDetail($value)
 * @method static \Illuminate\Database\Query\Builder|Assignment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Assignment withoutTrashed()
 */
	class Assignment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignmentEmployee
 *
 * @property int $assignment_id
 * @property int $employee_id
 * @property string $start_job
 * @property string $finish_job
 * @property string $travel_time
 * @property string|null $overtime
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \App\Models\Assignment|null $assignment
 * @property-read \App\Models\Employee|null $employee
 * @method static \Database\Factories\AssignmentEmployeeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee newQuery()
 * @method static \Illuminate\Database\Query\Builder|AssignmentEmployee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereFinishJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereOvertime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereStartJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereTravelTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentEmployee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AssignmentEmployee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AssignmentEmployee withoutTrashed()
 */
	class AssignmentEmployee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignmentPart
 *
 * @property int $assignment_id
 * @property string $part_name
 * @property string $part_type
 * @property int $qty
 * @property string $unit
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\AssignmentPartFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart newQuery()
 * @method static \Illuminate\Database\Query\Builder|AssignmentPart onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart wherePartName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart wherePartType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentPart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AssignmentPart withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AssignmentPart withoutTrashed()
 */
	class AssignmentPart extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $employee_id
 * @property int $attendance_reason_id
 * @property int|null $annual_leave_id
 * @property mixed|string $at
 * @property string $start
 * @property string|null $end
 * @property string|null $overtime
 * @property string|null $detail
 * @property mixed|null $attachment
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \App\Models\AnnualLeave|null $annualLeave
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\AttendanceReason|null $reason
 * @method static \Database\Factories\AttendanceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Query\Builder|Attendance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAnnualLeaveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAttendanceReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereOvertime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Attendance withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Attendance withoutTrashed()
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendanceLog
 *
 * @property int $id
 * @property int $pin
 * @property mixed|\DateTime|string $time
 * @property int $status
 * @property int $verify
 * @property int $workcode
 * @property string $reserved_1
 * @property string $reserved_2
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \App\Models\UserInfo|null $userInfo
 * @method static \Database\Factories\AttendanceLogFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendanceLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereReserved1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereReserved2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereWorkcode($value)
 * @method static \Illuminate\Database\Query\Builder|AttendanceLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendanceLog withoutTrashed()
 */
	class AttendanceLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendanceReason
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\AttendanceReasonFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendanceReason onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceReason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendanceReason withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendanceReason withoutTrashed()
 */
	class AttendanceReason extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Audit
 *
 * @property int $id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property mixed|null $old_values
 * @property mixed|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserType($value)
 */
	class Audit extends \Eloquent implements \OwenIt\Auditing\Contracts\Audit {}
}

namespace App\Models{
/**
 * App\Models\CalendarEvent
 *
 * @property int $id
 * @property string $start_date
 * @property string|null $end_date
 * @property int $recurring
 * @property string $title
 * @property string|null $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\CalendarEventFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent newQuery()
 * @method static \Illuminate\Database\Query\Builder|CalendarEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CalendarEvent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CalendarEvent withoutTrashed()
 */
	class CalendarEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $country_id
 * @property int $state_id
 * @property int $city_id
 * @property int $district_id
 * @property int $village_id
 * @property string $postal_code
 * @property string $street
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\World\City|null $city
 * @property-read \App\Models\World\Country|null $country
 * @property-read \App\Models\World\District|null $district
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerMachine[] $machines
 * @property-read int|null $machines_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Assignment[] $serviceReports
 * @property-read int|null $service_reports_count
 * @property-read \App\Models\World\State|null $state
 * @property-read \App\Models\World\Village|null $village
 * @method static \Database\Factories\CustomerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereVillageId($value)
 * @method static \Illuminate\Database\Query\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Customer withoutTrashed()
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomerMachine
 *
 * @property int $id
 * @property int $customer_id
 * @property int $machine_id
 * @property string $serial_number
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Machine|null $machine
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\CustomerMachineFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine newQuery()
 * @method static \Illuminate\Database\Query\Builder|CustomerMachine onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerMachine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CustomerMachine withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CustomerMachine withoutTrashed()
 */
	class CustomerMachine extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $nik
 * @property int $pin
 * @property string $name
 * @property string|null $personal_email
 * @property mixed|null $profile_photo_path
 * @property string $birth_date
 * @property mixed|string $effective_since
 * @property int $gender_id
 * @property int|null $position_id
 * @property int|null $job_title_id
 * @property int|null $user_id
 * @property string $country_id
 * @property int $state_id
 * @property int $city_id
 * @property int $district_id
 * @property int $village_id
 * @property string $postal_code
 * @property string $street
 * @property string $currency_code
 * @property string $basic_salary
 * @property string $functional_allowance
 * @property string $transport_allowance
 * @property string $meal_allowances
 * @property int $marital_status
 * @property int $has_npwp
 * @property int $num_of_dependents_family
 * @property int $permanent_status
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AnnualLeave[] $annualLeaves
 * @property-read int|null $annual_leaves_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AssignmentEmployee[] $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\World\City|null $city
 * @property-read \App\Models\World\Country|null $country
 * @property-read \App\Models\World\Currency|null $currency
 * @property-read \App\Models\CustomerMachine|null $customerMachine
 * @property-read \App\Models\World\District|null $district
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\World\State|null $state
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\UserInfo|null $userInfo
 * @property-read \App\Models\World\Village|null $village
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Query\Builder|Employee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBasicSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEffectiveSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFunctionalAllowance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereGenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereHasNpwp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMealAllowances($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNumOfDependentsFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePermanentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereJobTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereTransportAllowance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereVillageId($value)
 * @method static \Illuminate\Database\Query\Builder|Employee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Employee withoutTrashed()
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FaieldJob
 *
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property string $failed_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaieldJob whereUuid($value)
 */
	class FaieldJob extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fingerprint
 *
 * @property int $id
 * @property int $pin
 * @property int $finger_id
 * @property int $size
 * @property int $valid
 * @property string $template
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @method static \Database\Factories\FingerprintFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint newQuery()
 * @method static \Illuminate\Database\Query\Builder|Fingerprint onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereFingerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fingerprint whereValid($value)
 * @method static \Illuminate\Database\Query\Builder|Fingerprint withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Fingerprint withoutTrashed()
 */
	class Fingerprint extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Gender
 *
 * @property int $id
 * @property string $name
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\GenderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gender newQuery()
 * @method static \Illuminate\Database\Query\Builder|Gender onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Gender query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gender whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Gender withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Gender withoutTrashed()
 */
	class Gender extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Machine
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\MachineFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newQuery()
 * @method static \Illuminate\Database\Query\Builder|Machine onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Machine withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Machine withoutTrashed()
 */
	class Machine extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Membership
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $role
 * @property int $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUserId($value)
 */
	class Membership extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \App\Models\ModelInterface {}
}

namespace App\Models{
/**
 * Class Menu
 *
 * @property int    id
 * @property int    parent_id
 * @property string name
 * @property string title
 * @property string route
 * @property Json   attrs
 * @property int    level
 * @property int    role_id
 * @property bool   divider
 * @package App\Models\Master
 * @property int $id
 * @property string|null $name
 * @property int|null $parent_id
 * @property string|null $title
 * @property string|null $route
 * @property mixed|null $attrs
 * @property int|null $role_id
 * @property int $divider
 * @property int $enabled
 * @property int $level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newQuery()
 * @method static \Illuminate\Database\Query\Builder|Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereAttrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereDivider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Menu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Menu withoutTrashed()
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModelBase
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 */
	class ModelBase extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \App\Models\ModelInterface {}
}

namespace App\Models{
/**
 * App\Models\OperationLog
 *
 * @property int $id
 * @property int $op_type
 * @property int $op_who
 * @property mixed|string $op_time
 * @property string $value_1
 * @property string $value_2
 * @property string $value_3
 * @property int $reserved_op_type
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @method static \Database\Factories\OperationLogFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|OperationLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereOpTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereOpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereOpWho($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereReservedOpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OperationLog whereValue3($value)
 * @method static \Illuminate\Database\Query\Builder|OperationLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OperationLog withoutTrashed()
 */
	class OperationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordReset
 *
 * @property string $email
 * @property string $token
 * @property mixed|string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent implements \App\Contracts\Permission, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property string|null $abilities
 * @property string|null $last_used_at
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereUpdatedAt($value)
 */
	class PersonalAccessToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Position
 *
 * @property int $id
 * @property string $name
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\PositionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Query\Builder|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Position withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Position withoutTrashed()
 */
	class Position extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Priority
 *
 * @property int $id
 * @property string $name
 * @property int $level
 * @property string|null $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\PriorityFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Priority newQuery()
 * @method static \Illuminate\Database\Query\Builder|Priority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Priority query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Priority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Priority withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Priority withoutTrashed()
 */
	class Priority extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $level
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent implements \App\Contracts\Role, \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\Salary
 *
 * @property int $id
 * @property int $employee_id
 * @property string $currency_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\SalaryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Salary newQuery()
 * @method static \Illuminate\Database\Query\Builder|Salary onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Salary query()
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Salary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Salary withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Salary withoutTrashed()
 */
	class Salary extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Settings
 *
 * @property int $id
 * @property string $section
 * @property string $key
 * @property string|null $value
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\SettingsFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Query\Builder|Settings onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|Settings withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Settings withoutTrashed()
 */
	class Settings extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Task
 *
 * @property int $id
 * @property int|null $employee_id
 * @property int|null $priority_id
 * @property mixed|string $dateline
 * @property string $title
 * @property string $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\ModelStatus\Status|null $currentStatus
 * @property-read \App\Models\Employee|null $employee
 * @property-read string $status
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Priority|null $priority
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\ModelStatus\Status[] $statuses
 * @property-read int|null $statuses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Task currentStatus($names)
 * @method static \Database\Factories\TaskFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task otherCurrentStatus($names)
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDateline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Team
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_team
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TeamInvitation[] $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePersonalTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUserId($value)
 */
	class Team extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \App\Models\ModelInterface {}
}

namespace App\Models{
/**
 * App\Models\TeamInvitation
 *
 * @property int $id
 * @property int $team_id
 * @property string $email
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereUpdatedAt($value)
 */
	class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TelescopeEntry
 *
 * @property int $sequence
 * @property string $uuid
 * @property string $batch_id
 * @property string|null $family_hash
 * @property int $should_display_on_index
 * @property string $type
 * @property array $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereFamilyHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereShouldDisplayOnIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntry whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntryModel withTelescopeOptions($type, \Laravel\Telescope\Storage\EntryQueryOptions $options)
 */
	class TelescopeEntry extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\TelescopeEntryTag
 *
 * @property string $entry_uuid
 * @property string $tag
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntryTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntryTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntryTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntryTag whereEntryUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeEntryTag whereTag($value)
 */
	class TelescopeEntryTag extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\TelescopeMonitor
 *
 * @property string $tag
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeMonitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeMonitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeMonitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelescopeMonitor whereTag($value)
 */
	class TelescopeMonitor extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $name
 * @property string $email
 * @property mixed|string|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property string|null $current_team_id
 * @property string|null $profile_photo_path
 * @property mixed|null $config
 * @property int $enabled
 * @property mixed|string|null $last_login
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 */
	class User extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \App\Models\ModelInterface {}
}

namespace App\Models{
/**
 * App\Models\UserInfo
 *
 * @property int $pin
 * @property string $name
 * @property int $privilege
 * @property string|null $password
 * @property string $card
 * @property int $group
 * @property string $timezone
 * @property int $verify
 * @property string|null $vice_card
 * @property mixed|string $start_datetime
 * @property mixed|string $end_datetime
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AttendanceLog[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Employee|null $employee
 * @method static \Database\Factories\UserInfoFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo wherePrivilege($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInfo whereViceCard($value)
 * @method static \Illuminate\Database\Query\Builder|UserInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserInfo withoutTrashed()
 */
	class UserInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Vehicle
 *
 * @property int $id
 * @property string $plat_number
 * @property string $type
 * @property string $imei
 * @property string $device_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\VehicleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newQuery()
 * @method static \Illuminate\Database\Query\Builder|Vehicle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle wherePlatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Vehicle withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Vehicle withoutTrashed()
 */
	class Vehicle extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\City
 *
 * @property int $id
 * @property string $name
 * @property int $state_id
 * @property string $country_id
 * @property float $longitude
 * @property float $latitude
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\World\District[] $districts
 * @property-read int|null $districts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\World\State|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Continent
 *
 * @property string $code
 * @property string $name
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Continent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Continent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Continent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Continent whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Continent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continent whereUpdatedAt($value)
 */
	class Continent extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Country
 *
 * @property string $iso
 * @property string $iso3
 * @property int|null $iso_numeric
 * @property string|null $fips
 * @property string|null $equivalent_fips_code
 * @property int $geonameid
 * @property string $name
 * @property string|null $local_name
 * @property string|null $capital
 * @property string|null $area
 * @property string $continent_id
 * @property int|null $region_id
 * @property int|null $population
 * @property string|null $languages
 * @property int|null $indep_year
 * @property string|null $government_form
 * @property string|null $head_of_state
 * @property string|null $currency_id
 * @property string|null $tld
 * @property string|null $postal_code_format
 * @property string|null $postal_code_regex
 * @property string|null $phone
 * @property string|null $life_expectancy
 * @property float|null $gnp
 * @property float|null $gnp_old
 * @property float $north
 * @property float $south
 * @property float $east
 * @property float $west
 * @property float $longitude
 * @property float $latitude
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\World\Currency[] $currencies
 * @property-read int|null $currencies_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\World\State[] $states
 * @property-read int|null $states_count
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereContinentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereEast($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereEquivalentFipsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereFips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereGeonameid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereGnp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereGnpOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereGovernmentForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereHeadOfState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIndepYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIsoNumeric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLifeExpectancy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLocalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNorth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePostalCodeFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePostalCodeRegex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereSouth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereTld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereWest($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\CountryNeighbour
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|CountryNeighbour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CountryNeighbour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CountryNeighbour query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 */
	class CountryNeighbour extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Currency
 *
 * @property string $code
 * @property string $name
 * @property string|null $symbol
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUpdatedAt($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\District
 *
 * @property int $id
 * @property string $name
 * @property int $city_id
 * @property int $state_id
 * @property string $country_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\World\City|null $city
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\World\Village[] $villages
 * @property-read int|null $villages_count
 * @method static \Illuminate\Database\Eloquent\Builder|District newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|District newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|District query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|District whereUpdatedAt($value)
 */
	class District extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Language
 *
 * @property int $id
 * @property string $name
 * @property string $country_id
 * @property int $is_official
 * @property float $percentage
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsOfficial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Region
 *
 * @property int $id
 * @property string|null $name
 * @property string $continent_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereContinentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\State
 *
 * @property int $id
 * @property string|null $code
 * @property string $name
 * @property string $country_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\World\City[] $cities
 * @property-read int|null $cities_count
 * @property-read \App\Models\World\Country $country
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereUpdatedAt($value)
 */
	class State extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Timezone
 *
 * @property int $id
 * @property int $zone_id
 * @property int|null $timezone_abbreviation_id
 * @property string $time_start
 * @property int $gmt_offset
 * @property string $dst
 * @property string $utc_offset
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereDst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereGmtOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereTimezoneAbbreviationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereUtcOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereZoneId($value)
 */
	class Timezone extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\TimezoneAbbreviation
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $utc_offset
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneAbbreviation whereUtcOffset($value)
 */
	class TimezoneAbbreviation extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Village
 *
 * @property int $id
 * @property string $name
 * @property int $district_id
 * @property int $city_id
 * @property int $state_id
 * @property string $country_id
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\World\District|null $district
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Village newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Village newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Village query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Village whereUpdatedAt($value)
 */
	class Village extends \Eloquent {}
}

namespace App\Models\World{
/**
 * App\Models\World\Zone
 *
 * @property int $id
 * @property string $country_id
 * @property string $name
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereUpdatedAt($value)
 */
	class Zone extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FingerPrintDevice
 *
 * @property int $id
 * @property int $no
 * @property string $ip_address
 * @property int $port
 * @property string $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice newQuery()
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDevice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDevice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDevice withoutTrashed()
 */
	class FingerPrintDevice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JobTitle
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employee[] $employees
 * @property-read int|null $employees_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\JobTitleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newQuery()
 * @method static \Illuminate\Database\Query\Builder|JobTitle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|JobTitle withTrashed()
 * @method static \Illuminate\Database\Query\Builder|JobTitle withoutTrashed()
 */
	class JobTitle extends \Eloquent {}
}


namespace App\Models{
/**
 * App\Models\ReasonForLeave
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $number_of_days
 * @property string|null $attachment_requirement
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave query()
 * @method static Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereAttachmentRequirement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereId($value)
 * @method static Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereNumberOfDays($value)
  * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave withoutTrashed()
 * @mixin \Eloquent
 */
	class ReasonForLeave extends \Eloquent {}
}


namespace App\Models{
/**
 * App\Models\FingerPrintDeviceData
 *
 * @property int $id
 * @property int|null $finger_print_device_id
 * @property string|null $nik
 * @property string|null $timestamps
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData newQuery()
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDeviceData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereFingerPrintDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereTimestamps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FingerPrintDeviceData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDeviceData withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FingerPrintDeviceData withoutTrashed()
 */
	class FingerPrintDeviceData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permit
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $permit_date
 * @property int|null $id_employee
 * @property string|null $permit_type
 * @property int|null $id_reason_for_leave
 * @property \Illuminate\Support\Carbon|null $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property string|null $note
 * @property mixed|null $attachment_path
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ReasonForLeave|null $reasonForLeave
 * @method static \Illuminate\Database\Eloquent\Builder|Permit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permit newQuery()
 * @method static \Illuminate\Database\Query\Builder|Permit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Permit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereIdEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereIdReasonForLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit wherePermitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit wherePermitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Permit withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Permit withoutTrashed()
 */
	class Permit extends \Eloquent {}
}


namespace App\Models{
/**
 * App\Models\Overtime
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $overtime_date
 * @property int|null $id_employee
 * @property string|null $start
 * @property string|null $end
 * @property string|null $necessity
 * @property int|null $status
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime newQuery()
 * @method static \Illuminate\Database\Query\Builder|Overtime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereIdEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereNecessity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereOvertimeDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Overtime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Overtime withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Overtime withoutTrashed()
 */
	class Overtime extends \Eloquent {}
}

