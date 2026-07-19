$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let $customer = $('#customer').select2();
    let $machine = $('#machine').select2();
    let $technicians = $('#technicians').select2();

    let totalTechnicians = 0;
    let count = 0;

    let id = $('#domesticAssignmentId').val();

    let $tableAccPreServiceBody = $('.table-accpreservice tbody');
    let $checkInDate = $('#checkIn');
    let $checkInAt = $('#checkInAt');


    let $tablesDuringServiceContainer = $('#table-accduringservice-container');
    let $checkOutDate = $('#checkOut');

    let $btnDomesticAssigment = $('.btnDomesticAssignment');

    function loadDataService(){
        $.when(
            $.ajax({
                type: 'GET',
                url: '/api/v1/domestic-assignment-preService/Id/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // 'Accept': 'application/json',
                },
                cache: false
            }),
            $.ajax({
                type: 'GET',
                url: '/api/v1/domestic-assignment-duringService/Id/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // 'Accept': 'application/json',
                },
                cache: false
            })
        ).done(function(preServiceResponse, duringServiceResponse){
            $checkInDate.val(preServiceResponse[0][0].check_in_date);
            $checkInAt.val(preServiceResponse[0][0].check_in_at);
            loadPreServiceData(preServiceResponse);

            $checkOutDate.val(duringServiceResponse[0][0].check_out_date);
            loadDuringServiceData(duringServiceResponse);
        });
    }
    loadDataService();

    function loadPreServiceData(preServiceData){
        $.each(preServiceData[0], function(i, data){
            count = totalTechnicians + 1;
            let $row = $('<tr>');

            let $elNo = $('<td class="text-center" style="vertical-align: middle">').html(count);
            let $idTechnician = $('<td>').append(data.employee_id)
            let $technician = $('<td>').append(data.employee_name);

            let $breakfast = $('<input type="checkbox" class="p-2" id="' + "ps_breakfast" + i + '" style="cursor: pointer; width: 25px; height: 25px;">');
            $breakfast.attr('checked', data.ps_breakfast == 1);

            let $lunch = $('<input type="checkbox" class="p-2" id="' + "ps_lunch" + i + '" style="cursor: pointer; width: 25px; height: 25px;">');
            $lunch.attr('checked', data.ps_lunch == 1);

            let $dinner = $('<input type="checkbox" class="p-2" id="' + "ps_dinner" + i + '" style="cursor: pointer; width: 25px; height: 25px;">');
            $dinner.attr('checked', data.ps_dinner == 1);

            let $supper = $('<input type="checkbox" class="p-2" id="' + "ps_supper" + i + '" style="cursor: pointer; width: 25px; height: 25px;">');
            $supper.attr('checked', data.ps_supper == 1);

            let $elIdTechnician = $('<td style="display: none; vertical-align: middle"">').append($idTechnician);
            let $elTechnician = $('<td style="vertical-align: middle">').append($technician);
            let $elBreakfast = $('<td class="text-center" style="vertical-align: middle">').append($breakfast);
            let $elLunch = $('<td class="text-center my-auto" style="vertical-align: middle">').append($lunch);
            let $elDinner = $('<td class="text-center" style="vertical-align: middle">').append($dinner);
            let $elSupper = $('<td class="text-center" style="vertical-align: middle">').append($supper);

            $tableAccPreServiceBody.append(
                $row.append($elNo)
                    .append($elIdTechnician)
                    .append($elTechnician)
                    .append($elBreakfast)
                    .append($elLunch)
                    .append($elDinner)
                    .append($elSupper)
            );
            totalTechnicians++;
        });
    }

    function loadDuringServiceData(duringServiceData){
        $tablesDuringServiceContainer.empty();
        let assignmentDateFrom = new Date($('#assignment_date_from').val());
        let assignmentDateTo = new Date($('#assignment_date_to').val());
        let diffAssignmentDateInDays = Math.floor((assignmentDateTo - assignmentDateFrom)/(1000*60*60*24)) + 1;
        let dateNow = new Date();

        for(let x = 0; x < diffAssignmentDateInDays; x++){
            let newAssignmentDateFrom = new Date(assignmentDateFrom.getTime() + (x * 24 * 60 * 60 * 1000))
            let $p = $('<p style="font-wight: bold; margin-bottom: 2px;">').html(`Day - ${x+1} (${newAssignmentDateFrom.toISOString().split('T')[0]})`);
            $tablesDuringServiceContainer.append($p);

            let $table = $('<table class=" table table-hover bg-white table-accduringservice" style="margin-bottom: 32px;">');
            let $thead = $('<thead class="thead-dark">');
            let $trHeader = $('<tr>');

            let $thNo = $('<th class="va-baseline text-center fs-0">').html('No.');
            let thId = $('<th class="va-baseline text-center fs-0" style="display: none;">').html('Id');
            let $thAssignmentDate = $('<th class="va-baseline text-center fs-0" style="display: none;">').html('Assignment Date');
            let $thName = $('<th class="va-baseline fs-0" width="400">').html('Name');
            let $thBreakfast = $('<th class="text-center fs-0">').html('Breakfast');
            let $thStartJob = $('<th class="text-center fs-0">').html('Start Job');
            let $thLunch = $('<th class="text-center fs-0">').html('Lunch');
            let $thFinishJob = $('<th class="text-center fs-0">').html('Finish Job');
            let $thDinner = $('<th class="text-center fs-0">').html('Dinner');
            let $tbody = $('<tbody class="tbody-accduringservice">');

            $trHeader
                .append($thNo)
                .append(thId)
                .append($thAssignmentDate)
                .append($thName)
                .append($thBreakfast)
                .append($thStartJob)
                .append($thLunch)
                .append($thFinishJob)
                .append($thDinner);

            $thead.append($trHeader);
            $table.append($thead).append($tbody);

            totalTechnicians = 0;
            count = 0;
            $.each(duringServiceData[0], function(i, item){
                if(item.assignment_date == newAssignmentDateFrom.toISOString().split('T')[0]){
                    count = totalTechnicians+1;
                    addDuringServiceTableRow(item, x, $tbody, $table, dateNow, assignmentDateFrom);
                    totalTechnicians++;
                }
            });
            $tablesDuringServiceContainer.append($table);
        }

    }

    function addDuringServiceTableRow(i, day, $b, $t, now, from){
        let $row = $('<tr>');
        let newDateAssignmentFrom = new Date(from.getTime() + (day * 24 * 60 * 60 * 1000));
        let $elNo = $('<td class="text-center" style="vertical-align: middle">').html(count);
        let $idTechnician = $('<td style="display: none;">').append(i.employee_id)
        let $assignmentDate = $('<td style="display: none;">').append(newDateAssignmentFrom.toISOString().split('T')[0]);
        let $technician = $('<td>').append(i.employee_name);

        let $breakfast = $('<input type="checkbox" class="p-2" id="' + "ds_breakfast" + day + '" style="cursor: pointer; width: 25px; height: 25px;">');
        $breakfast.attr('checked', i.ds_breakfast > 0);

        let $startJob = $('<input type="text" id="' + "start_job" + day + '" class="p-2 time24h text-center w-20" style="width: 80px;" placeholder="HH:MM"  style="cursor: pointer;" />');
        $startJob.val(i.start_job);

        let $lunch = $('<input type="checkbox" id="' + "ds_lunch" + day + '" class="p-2" style="cursor: pointer; width: 25px; height: 25px;">');
        $lunch.attr('checked', i.ds_lunch > 0);

        let $finishJob = $('<input type="text" id="' + "finish_job" + day + '" class="p-2 time24h text-center w-20" style="width: 80px;" placeholder="HH:MM" " style="cursor: pointer;" />');
        $finishJob.val(i.finish_job);

        let $dinner = $('<input type="checkbox" id="' + "dinner" + day + '" class="p-2" style="cursor: pointer; width: 25px; height: 25px;">');
        $dinner.attr('checked', i.ds_dinner > 0);

        let $elIdTechnician = $('<td style="vertical-align: middle; display: none;">').append($idTechnician);
        let $elAssignmentDate = $('<td style="vertical-align: middle; display: none;">').append($assignmentDate);
        let $elTechnician = $('<td style="vertical-align: middle">').append($technician);
        let $elBreakfast = $('<td class="text-center" style="vertical-align: middle">').append($breakfast);
        let $elStartJob = $('<td class="text-center" style="vertical-align: middle">').append($startJob);
        let $elLunch = $('<td class="text-center my-auto" style="vertical-align: middle">').append($lunch);
        let $elFinishJob = $('<td class="text-center" style="vertical-align: middle">').append($finishJob);
        let $elDinner = $('<td class="text-center" style="vertical-align: middle">').append($dinner);

        $row.append($elNo)
            .append($elIdTechnician)
            .append($elAssignmentDate)
            .append($elTechnician)
            .append($elBreakfast)
            .append($elStartJob)
            .append($elLunch)
            .append($elFinishJob)
            .append($elDinner)


        $b.append($row);
        // $t.append($b);

        // $tablesDuringServiceContainer.append($t);

        let $time24h = $t.find('.time24h');

        $time24h.mask('Hh:Mm', {
            translation: {
                'H': { pattern: /[0-2]/, optional: false },
                'h': { pattern: /[0-9]/, optional: false },
                'M': { pattern: /[0-5]/, optional: false },
                'm': { pattern: /[0-9]/, optional: false }
            },
            placeholder: "HH:MM"
        });

        $time24h.on('blur', function(){
            let timeVal = $time24h.val().split(':');
            if(parseInt(timeVal[0]) > 23 || parseInt(timeVal[1]) > 59){
                $time24h.val('');
            }
        });
    }

    $btnDomesticAssigment.on('click', function(){
        let dataPayload = {
            'assignment_id': id,
            'pre_service': [],
            'during_service': []
        };

        let $tablePreServiceBody = $('.table-accpreservice tbody');
        $tablePreServiceBody.children('tr').each(function(i, row){
            let $row = $(row);
            let checkInDate = $checkInDate.val();
            let checkInAt = $checkInAt.val();
            let employeeId = $row.find('td:nth-child(2)').text();
            let breakfast = $row.find('td:nth-child(4) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let lunch = $row.find('td:nth-child(5) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let dinner = $row.find('td:nth-child(6) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let supper = $row.find('td:nth-child(7) input[type="checkbox"]').is(':checked') ? 1 : 0;

            dataPayload.pre_service.push({
                'employee_id': employeeId,
                'check_in_date': checkInDate,
                'check_in_at': checkInAt,
                'pre_service_breakfast': breakfast,
                'pre_service_lunch': lunch,
                'pre_service_dinner': dinner,
                'pre_service_supper': supper
            });
        });

        let $duringServiceTables = $tablesDuringServiceContainer.children('.table-accduringservice');
        $duringServiceTables.children('tbody').children('tr').each(function(i, row){
            let $row = $(row);
            let checkOutDate = $checkOutDate.val();
            let employeeId = $row.find('td:nth-child(2)').text();
            let assignmentDate = $row.find('td:nth-child(3)').text();
            let breakfast = $row.find('td:nth-child(5) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let startJob = $row.find('td:nth-child(6) input[type="text"]').val();
            let lunch = $row.find('td:nth-child(7) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let finishJob = $row.find('td:nth-child(8) input[type="text"]').val();
            let dinner = $row.find('td:nth-child(9) input[type="checkbox"]').is(':checked') ? 1 : 0;

            dataPayload.during_service.push({
                'check_out_date': checkOutDate,
                'assignment_date': assignmentDate,
                'employee_id': employeeId,
                'during_service_breakfast': breakfast,
                'start_job': startJob,
                'during_service_lunch': lunch,
                'finish_job': finishJob,
                'during_service_dinner': dinner,
                'overtime': 0

            });
        });

        $.ajax({
            type: 'POST',
            data: {
                // _token: $('meta[name="csrf-token"]').attr('token'),
                'data': dataPayload
            },
            url: '/api/v1/assignment-domestic-update',
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            // },
            cache: false
        }).done(function(dt){
            if(dt){
                Swal.fire({
                    title: 'Updated Successfully',
                    text: 'Domestic Assignment has been updated successfully.',
                    icon: 'success',
                })
                window.location.href = '/assignment-domestic';
            }
        })

    });

});
