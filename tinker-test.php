<?php

use App\Models\DomesticAssignmentPreService;

$preServices = DomesticAssignmentPreService::with('employee')->where('assignment_id', 20)->get();

$dataPreServices = [];

foreach($preServices as $ps){
    $data = [
        'check_in_date' => $ps->check_in_date,
        'check_in_at' => $ps->check_in_at,
        'employee_id' => $ps->employee->id,
        'employee_name' => $ps->employee->name,
        'ps_breakfast' => $ps->pre_service_breakfast > 0 ? 1 : 0,
        'ps_lunch' => $ps->pre_service_lunch > 0 ? 1 : 0,
        'ps_dinner' => $ps->pre_service_dinner > 0 ? 1 : 0,
        'ps_supper' => $ps->pre_service_supper > 0 ? 1 : 0,
    ];
    array_push($dataPreServices, $data);
}

return $dataPreServices;
