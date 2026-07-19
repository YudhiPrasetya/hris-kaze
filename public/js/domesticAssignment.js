
let totalTechnicians = 0;
let count = 0;

function processQuery(params) {
	// Query parameters will be ?search=[term]&type=public
	return {
		search: params.term
	};
}

function processResults(data) {
	data.results = $.map(data.results, function (obj) {
		obj.text = obj.text || obj.name; // replace name with the property used for the text
		return obj;
	});

	return data;
}

let defaultSelectOptions = {
	allowClear: true,
	tags: true
};

let defaultAjaxOptions = {
	dataType: 'json',
	data: processQuery,
	processResults: processResults
};

function loadCustomer($owner){
    $.ajax({
        type: 'GET',
        url: '/api/v1/customer/listForSelect',
        cache: false,
        success: function(data, textStatus, jqXHR){
            $.each(data, function (idx, item) {
                let option = new Option(item.customer_no + " - " + item.name, item.id)
                $owner.append(option);
            });

            $owner.trigger('change');
        }
    });
}

function loadTechnisians($owner){
    $.ajax({
        type: 'GET',
        url: '/api/v1/employee/forSelect',
        cache: false,
        success: function(data, textStatus, jqXHR){
            $.each(data, function (idx, item) {
                let option = new Option(item.name, item.id)
                $owner.append(option);
            });

        }
    });
}

function loadMachine($owner, customer){
    $.ajax({
        type: 'GET',
        url: '/api/v1/machine/' + customer + '/customer',
        cache: false,
        success: function(data, textStatus, jqXHR){
            $owner.empty();
            if(data.length > 0){
                $owner.append(new Option("Please select a machine", ""));
                $.each(data, function (idx, item) {
                    let option = new Option(item.machine.name + " - " + item.machine.type, item.machine.id)
                    $owner.append(option);
                });
            }
        }
    });
}

$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let $assignmentNo = $('#assignment_no');
    let $letterDate = $('#letter_date');
    let $srNO = $('#sr_no');
    let $assignmentDate = $('#assignment_date');
    let $isChargeable = $('#is_chargeable');
    $isChargeable.val(0);
    let $chargePrice = $('#charge_price').attr('disabled', true);

    let $customer = $('#customer');
    let $machine = $('#machine').select2();

    let $btnDomesticAssignment = $('.btnDomesticAssignment');

    let $assignmentDateFrom = $('#assignment_date_from');
    let $assignmentDateTo = $('#assignment_date_to');

    let $technicians = $('#employees').select2();
    // let arrTechnisians = [{}];

    // let $accPreService = $('#accPreService');
    let $accomodations = $('#accomodations').select2();

    let $checkIn = $('#checkIn');
    let $checkInAt = $('#checkInAt');
    let $checkOut = $('#checkOut');
    let $preServiceToggleShow = $('#preServiceToggleShow');
    let $duringServiceToggleShow = $('#duringServiceToggleShow');
    let $tableAccPreService = $('.table-accpreservice');
    let $tableAccPreServiceBody = $('.table-accpreservice tbody');

    // let $tableAccDuringService = $('.table-accduringservice');
    // let $tableAccDuringServiceBody = $('.table-accduringservice tbody');
    let $accDuringServiceTableContainer = $('#table-accduringservice-container');

    // $tableAccPreService.hide();
    // $checkIn.hide();
    // $toggleShow.hide();

    $preServiceToggleShow.hide();
    $duringServiceToggleShow.hide();

    loadCustomer($customer);
    loadTechnisians($technicians);

    $customer.select2().on('change', function(){
        if($customer.val() !== ""){
            let customerId = $customer.val();
            // $machine.empty();
            loadMachine($machine, customerId);
        }
    });

    $isChargeable.on('click', function(){
        let isChargeableVal = $isChargeable.val();
        // console.log(isChargeableVal);
        $chargePrice.attr('disabled', isChargeableVal == 1);
    });

    $technicians.select2().on('change', function(){
        let data = $technicians.select2('data');
        $tableAccPreServiceBody.empty();
        // $tableAccDuringServiceBody.empty();
        $accDuringServiceTableContainer.empty();

        totalTechnicians = 0;
        count = 0;

        $.each(data, function(i, item){
            count = totalTechnicians + 1;
            addPreService(item);
            totalTechnicians++;
        });

        let dateAssignmentFrom = new Date($assignmentDateFrom.val());
        let dateAssignmentTo = new Date($assignmentDateTo.val());
        let diffAssignmentDateInDays = 0;

        diffAssignmentDateInDays = Math.floor((dateAssignmentTo - dateAssignmentFrom) / (1000 * 60 * 60 * 24));
        console.log('diffAssignmentDateInDays: ', diffAssignmentDateInDays);
        if($assignmentDateTo.val() !== "" && dateAssignmentFrom <= dateAssignmentTo){
            let dateNow = new Date();

            for(let x = 0; x <= diffAssignmentDateInDays; x++){
                let $p = $('<p style="font-weight: bold;">').html('Day ' + ' - ' + (x+1) + ' (' + ' Job Date: ' + new Date(dateAssignmentFrom.getTime() + (x * 24 * 60 * 60 * 1000)).toISOString().split('T')[0] + ')');
                $accDuringServiceTableContainer.append($p);
                let $table = $('<table class=" table table-hover bg-white table-accduringservice">');
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
                $.each(data, function(i, itm){
                    count = totalTechnicians + 1;
                    addDuringService(itm, x, $tbody, $table, dateNow, dateAssignmentFrom);
                    totalTechnicians++;
                });
            }
        }

    });

    function addDuringService(i, day, $b, $t, now, from){
        // let countDuringService = totalTechnicians; // $table.children('tr:not(.no-records-found)').length;
        // let $container = $('.preservice.collection-container');
        // let $proto = $($container.data('prototype').replace(/__NAME__/g, count));
        console.log('day: ', day);
        let newDateAssignmentFrom = new Date(from.getTime() + (day * 24 * 60 * 60 * 1000));
        // newDateAssignmentFrom.setHours(0, 0, 0, 0);
        console.log('newDateAssignmentFrom: ', newDateAssignmentFrom);

        // let newNow = new Date(now);
        // newNow.setHours(0, 0, 0, 0);

        let $row = $('<tr>');
        // let $btnRemove = $(
        //     '<button role="button" type="button" class="btn btn-falcon-danger text-danger remove-' + count + '"><i class="fad fa-trash"></i></button>');

        let $elNo = $('<td class="text-center" style="vertical-align: middle">').html(count);
        // let $selectTechnician = $proto.find('text.technician');
        let $idTechnician = $('<td style="display: none;">').append(i.id)
        let $assignmentDate = $('<td style="display: none;">').append(newDateAssignmentFrom.toISOString().split('T')[0]);
        let $technician = $('<td>').append(i.text);

        // let $checkIn = $('<input type="date" class="form-control" name="preservice['+count+'][checkIn]" size="8">');
        let $breakfast = $('<input type="checkbox" class="p-2" name="duringservice['+count+'][breakfast]" style="cursor: pointer; width: 25px; height: 25px;">');
        // console.log('now:', now.toISOString().split('T')[0]);
        // console.log('from:', new Date(from + day).toISOString().split('T')[0]);
        // $breakfast.attr('disabled', newNow.toISOString().split('T')[0] != newDateAssignmentFrom.toISOString().split('T')[0]);

        let $startJob = $('<input type="text" class="p-2 time24h text-center w-20" style="width: 80px;" placeholder="HH:MM" name="duringservice['+count+'][start_job]" style="cursor: pointer;" />');
        // $startJob.attr('disabled', newNow.toISOString().split('T')[0] != newDateAssignmentFrom.toISOString().split('T')[0]);

        let $lunch = $('<input type="checkbox" class="p-2" name="duringservice['+count+'][lunch]" style="cursor: pointer; width: 25px; height: 25px;">');
        // $lunch.attr('disabled', newNow.toISOString().split('T')[0] != newDateAssignmentFrom.toISOString().split('T')[0]);

        let $finishJob = $('<input type="text" class="p-2 time24h text-center w-20" style="width: 80px;" placeholder="HH:MM" name="duringservice['+count+'][finish_job]" style="cursor: pointer;" />');
        // $finishJob.attr('disabled', newNow.toISOString().split('T')[0] != newDateAssignmentFrom.toISOString().split('T')[0]);

        let $dinner = $('<input type="checkbox" class="p-2" name="duringservice['+count+'][dinner]" style="cursor: pointer; width: 25px; height: 25px;">');
        // $dinner.attr('disabled', newNow.toISOString().split('T')[0] != newDateAssignmentFrom.toISOString().split('T')[0]);

        let $elIdTechnician = $('<td style="vertical-align: middle; display: none;">').append($idTechnician);
        let $elAssignmentDate = $('<td style="vertical-align: middle; display: none;">').append($assignmentDate);
        let $elTechnician = $('<td style="vertical-align: middle">').append($technician);
        // let $elCheckIn = $('<td>').append($checkIn);
        let $elBreakfast = $('<td class="text-center" style="vertical-align: middle">').append($breakfast);
        let $elStartJob = $('<td class="text-center" style="vertical-align: middle">').append($startJob);
        let $elLunch = $('<td class="text-center my-auto" style="vertical-align: middle">').append($lunch);
        let $elFinishJob = $('<td class="text-center" style="vertical-align: middle">').append($finishJob);
        let $elDinner = $('<td class="text-center" style="vertical-align: middle">').append($dinner);

        // $tableAccDuringServiceBody.append(
            $row.append($elNo)
                .append($elIdTechnician)
                .append($elAssignmentDate)
                .append($elTechnician)
                // .append($elCheckIn)
                .append($elBreakfast)
                .append($elStartJob)
                .append($elLunch)
                .append($elFinishJob)
                .append($elDinner)
                // .append($elAction)
        // );

        // $tbody.append($row);
        $b.append($row);
        $t.append($b);
        $accDuringServiceTableContainer.append($t);

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

    $accomodations.on('change', function(){
        let accVal = $accomodations.val();
        switch(accVal){
            case "1" :
                togglePreService();
                break;
            case "2" :
                toggleDuringService();
                break;
            default :
                hideToggleService();
                break;
        }
    });

    $btnDomesticAssignment.on('click', function(e){
        e.preventDefault();

        let dataPayload = {
            'assignment_no': $assignmentNo.val(),
            'letter_date': $letterDate.val(),
            'sr_no': $srNO.val(),
            'assignment_date_from': $assignmentDateFrom.val(),
            'assignment_date_to': $assignmentDateTo.val(),
            'is_chargeable': $isChargeable.val(),
            'charge_price': $chargePrice.val(),
            'customer_id': $customer.val(),
            'machine_id': $machine.val(),
            'pre_service': [],
            'during_service': []
            // 'domestic_assignment_employee': []
        };

        $tableAccPreServiceBody.children('tr').each(function(i, row){
            let $row = $(row);
            let checkInDate = $checkIn.val();
            let checkInAt = $checkInAt.val();
            let employeeId = $row.find('td:nth-child(2)').text();
            let breakfast = $row.find('td:nth-child(4) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let lunch = $row.find('td:nth-child(5) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let dinner = $row.find('td:nth-child(6) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let supper = $row.find('td:nth-child(7) input[type="checkbox"]').is(':checked') ? 1 : 0;

            // dataPayload['pre_service['+i+'][employee_id]'] = employeeId;
            // dataPayload['pre_service['+i+'][check_in_date]'] = checkInDate;
            // dataPayload['pre_service['+i+'][check_in_at]'] = checkInAt;
            // dataPayload['pre_service['+i+'][pre_service_breakfast]'] = breakfast;
            // dataPayload['pre_service['+i+'][pre_service_lunch]'] = lunch;
            // dataPayload['pre_service['+i+'][pre_service_dinner]'] = dinner;
            // dataPayload['pre_service['+i+'][pre_service_supper]'] = supper;

            // dataPayload.domestic_assignment_employee.push({
            //     'employee_id': employeeId,
            //     'check_in_date': checkInDate,
            //     'check_in_at': checkInAt,
            //     'pre_service_breakfast': breakfast,
            //     'pre_service_lunch': lunch,
            //     'pre_service_dinner': dinner,
            //     'pre_service_supper': supper
            // });

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

        // $tableAccDuringServiceBody.children('tr').each(function(i, row){
        //     let $row = $(row);
        //     let employeeId = $row.find('td:nth-child(2)').text();
        //     let breakfast = $row.find('td:nth-child(4) input[type="checkbox"]').is(':checked') ? 1 : 0;
        //     let startJob = $row.find('td:nth-child(5) input[type="text"]').val();
        //     let lunch = $row.find('td:nth-child(6) input[type="checkbox"]').is(':checked') ? 1 : 0;
        //     let finishJob = $row.find('td:nth-child(7) input[type="text"]').val();
        //     let dinner = $row.find('td:nth-child(8) input[type="checkbox"]').is(':checked') ? 1 : 0;

        //     dataPayload['during_service['+i+'][employee_id]'] = employeeId;
        //     dataPayload['during_service['+i+'][breakfast]'] = breakfast;
        //     dataPayload['during_service['+i+'][start_job]'] = startJob;
        //     dataPayload['during_service['+i+'][lunch]'] = lunch;
        //     dataPayload['during_service['+i+'][finish_job]'] = finishJob;
        //     dataPayload['during_service['+i+'][dinner]'] = dinner;
        // });
        let $tables = $accDuringServiceTableContainer.children('.table-accduringservice');
        // console.log('tables: ',$tables);
        // $tables.children('tbody').each(function(x, $tbody){
        //     console.log('tbody: ', $tbody);

        // })
        $tables.children('tbody').children('tr').each(function(i, row){
            // console.log('row: ', $row);
            let $row = $(row);
            let checkOutDate = $checkOut.val();
            let employeeId = $row.find('td:nth-child(2)').text();
            let assignmentDate = $row.find('td:nth-child(3)').text();
            let breakfast = $row.find('td:nth-child(5) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let startJob = $row.find('td:nth-child(6) input[type="text"]').val();
            let lunch = $row.find('td:nth-child(7) input[type="checkbox"]').is(':checked') ? 1 : 0;
            let finishJob = $row.find('td:nth-child(8) input[type="text"]').val();
            let dinner = $row.find('td:nth-child(9) input[type="checkbox"]').is(':checked') ? 1 : 0;

            // dataPayload['during_service['+i+'][check_out_date]'] = checkOutDate;
            // dataPayload['during_service['+i+'][assignment_date]'] = assignmentDate;
            // dataPayload['during_service['+i+'][employee_id]'] = employeeId;
            // dataPayload['during_service['+i+'][during_service_breakfast]'] = breakfast;
            // dataPayload['during_service['+i+'][start_assignment]'] = startJob;
            // dataPayload['during_service['+i+'][during_service_lunch]'] = lunch;
            // dataPayload['during_service['+i+'][end_assignment]'] = finishJob;
            // dataPayload['during_service['+i+'][during_service_dinner]'] = dinner;

            // dataPayload.during_service.push({
            //     'check_out_date': checkOutDate,
            //     'assignment_date': assignmentDate,
            //     'employee_id': employeeId,
            //     'during_service_breakfast': breakfast,
            //     'start_assignment': startJob,
            //     'during_service_lunch': lunch,
            //     'end_assignment': finishJob,
            //     'during_service_dinner': dinner
            // });

            // dataPayload.domestic_assignment_employee.push({
            //     'check_out_date': checkOutDate,
            //     'assignment_date': assignmentDate,
            //     'employee_id': employeeId,
            //     'during_service_breakfast': breakfast,
            //     'start_assignment': startJob,
            //     'during_service_lunch': lunch,
            //     'end_assignment': finishJob,
            //     'during_service_dinner': dinner
            // });

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

        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });

        $.ajax({
            method: 'POST',
            data: {'data': dataPayload},
            url: '/api/v1/assignment-domestic-AddNew',
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     // 'Accept': 'application/json',
            // },
            cache: false
        }).done(function(dt){
            // console.log(dt);
            if(dt){
                Swal.fire({
                    title: 'Created Successfully',
                    text: 'Domestic Assignment has been created successfully.',
                    icon: 'success',
                })
                window.location.href = '/assignment-domestic';
            }
        });
    });


    function hideToggleService(){
        $preServiceToggleShow.fadeOut(500, 'linear', fadeOutPreServiceComplete);
        $duringServiceToggleShow.fadeOut(500, 'linear', fadeOutDuringServiceComplete);
    }

    function togglePreService(){
        $preServiceToggleShow.fadeIn(500, 'linear', fadeInPreServiceComplete);
        $duringServiceToggleShow.fadeOut(500, 'linear', fadeOutDuringServiceComplete);
    }

    function toggleDuringService(){
        $duringServiceToggleShow.fadeIn(500, 'linear', fadeInDuringServiceComplete);
        $preServiceToggleShow.fadeOut(500, 'linear', fadeOutPreServiceComplete);
    }

    function fadeOutPreServiceComplete(){
        $preServiceToggleShow.hide();
    }

    function fadeInPreServiceComplete(){
        $preServiceToggleShow.show();
    }

    function fadeOutDuringServiceComplete(){
        $duringServiceToggleShow.hide();
    }

    function fadeInDuringServiceComplete(){
        $duringServiceToggleShow.show();
    }

    function addPreService(i){
        // let count = totalTechnicians; // $table.children('tr:not(.no-records-found)').length;
        let $container = $('.preservice.collection-container');
        let $proto = $($container.data('prototype').replace(/__NAME__/g, count));

        let $row = $('<tr>');

        let $elNo = $('<td class="text-center" style="vertical-align: middle">').html(count);
        let $textTechnician = $proto.find('text.technician');
        let $idTechnician = $('<td>').append(i.id)
        let $technician = $('<td>').append(i.text);

        // let $checkIn = $('<input type="date" class="form-control" name="preservice['+count+'][checkIn]" size="8">');
        let $breakfast = $('<input type="checkbox" class="p-2" name="preservice['+count+'][breakfast]" style="cursor: pointer; width: 25px; height: 25px;">');
        let $lunch = $('<input type="checkbox" class="p-2" name="preservice['+count+'][lunch]" style="cursor: pointer; width: 25px; height: 25px;">');
        let $dinner = $('<input type="checkbox" class="p-2" name="preservice['+count+'][dinner]" style="cursor: pointer; width: 25px; height: 25px;">');
        let $supper = $('<input type="checkbox" class="p-2" name="preservice['+count+'][supper]" style="cursor: pointer; width: 25px; height: 25px;">');

        let $elIdTechnician = $('<td style="display: none; vertical-align: middle"">').append($idTechnician);
        let $elTechnician = $('<td style="vertical-align: middle">').append($technician);
        // let $elCheckIn = $('<td>').append($checkIn);
        let $elBreakfast = $('<td class="text-center" style="vertical-align: middle">').append($breakfast);
        let $elLunch = $('<td class="text-center my-auto" style="vertical-align: middle">').append($lunch);
        let $elDinner = $('<td class="text-center" style="vertical-align: middle">').append($dinner);
        let $elSupper = $('<td class="text-center" style="vertical-align: middle">').append($supper);
        // let $elAction = $('<td class="text-center" style="vertical-align: middle">').append($btnRemove);

        // $selectTechnician.select2({
        //     ...defaultSelectOptions,
        //     ajax: {
        //         url: route('api.employee.select'),
        //         ...defaultAjaxOptions
        //     }
        // });

        $tableAccPreServiceBody.append(
            $row.append($elNo)
                .append($elIdTechnician)
                .append($elTechnician)
                // .append($elCheckIn)
                .append($elBreakfast)
                .append($elLunch)
                .append($elDinner)
                .append($elSupper)
                // .append($elAction)
        );
    }



    function removeRow() {
        // $tableAccPreServiceBody.children()

        let $parent = $(this).parent().parent();
        let $tbody = $parent.parent();

        // $parent.remove();
        $tbody.children().each(function () {
            let index = $(this).index();

            $(this).children(':nth-child(1)').html(index + 1);
            $(this).children().each(function () {
                let $el = $(this).children(':first-child');
                console.log($el.data());
                let name = $el.attr('name');
                // console.log(name);
                // if (name !== undefined) {
                //     $el.attr('name', name.replace(/\d+/g, index));
                // }
            });
        });


        // $tableAccPreServiceBody.removeAttr()

        totalTechnicians--;
        // $technisians.select2().trigger('change');
    }


    // $('button.add-technician').on('click', function(e){
    //     e.preventDefault();
	// 	let $table = $('table.table-technicians tbody');
	// 	let count = totalTechnicians; // $table.children('tr:not(.no-records-found)').length;
	// 	let $container = $('.technician.collection-container');
	// 	let $proto = $($container.data('prototype').replace(/__NAME__/g, count));

	// 	let $row = $('<tr>');
	// 	let $btnRemove = $(
	// 		'<button role="button" type="button" class="btn btn-falcon-danger text-danger remove-' + count + '"><i class="fad fa-trash"></i></button>');
	// 	let $elNo = $('<td class="text-center">').html(count + 1);
	// 	let $selectTechnician = $proto.find('select.technician');
	// 	let $elTechnician = $('<td>').append($selectTechnician);
	// 	let $start = $('<input type="time" class="form-control" name="technicians['+count+'][start_job]" size="8">');
	// 	let $finish = $('<input type="time" class="form-control" name="technicians['+count+'][finish_job]" size="8">');
	// 	let $travel = $('<input type="time" class="form-control" name="technicians['+count+'][travel_time]" size="8">');
	// 	let $overtime = $('<input type="time" class="form-control" name="technicians['+count+'][overtime]" size="8">');
	// 	let $elStart = $('<td>').append($start);
	// 	let $elFinish = $('<td>').append($finish);
	// 	let $elTravel = $('<td>').append($travel);
	// 	let $elOvertime = $('<td>').append($overtime);
	// 	let $elAction = $('<td class="text-center">').append($btnRemove);

	// 	$selectTechnician.select2({
	// 		...defaultSelectOptions,
	// 		ajax: {
	// 			url: route('api.employee.select'),
	// 			...defaultAjaxOptions
	// 		}
	// 	});

	// 	$table.append(
	// 		$row.append($elNo)
	// 		    .append($elTechnician)
	// 		    .append($elStart)
	// 		    .append($elFinish)
	// 		    .append($elTravel)
	// 		    .append($elOvertime)
	// 		    .append($elAction)
	// 	);

	// 	$btnRemove.on('click', removeRow);
	// 	totalTechnicians++;
    // });
});

