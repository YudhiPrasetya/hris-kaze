<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <title>Payroll - {{ $name }}</title>
    {{-- <style>
        margin-top: 100px;
        /* Set top margin to 100px */
        margin-right: 50px;
        /* Set right margin to 50px */
        margin-bottom: 50px;
        /* Set bottom margin to 50px */
        margin-left: 50px;
        /* Set left margin to 50px */
    </style> --}}
</head>

<body>

    <div class="container">
        <div class="card pr-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Employee Payroll</h4>
            </div>
            <div class="card-body">

                <!-- Company & Payslip Details -->
                <div class="row mb-4">
                    <div class="col-6 mb-1">
                        <img src="images/logo/omnity-48.png" alt="">
                    </div>
                    <div class="col-6">
                        <h6 class="text-info">Cleanvy Asalta</h6>
                        <p class="mb-0" style="font-size: 10px;"><small><strong>Jl. Kampung Babakan Cikeas RT/RW 003/003
                                    Desa Sentul</strong></small></p>
                        <p style="font-size: 10px;"><small><strong>Kecamatan Babakan Madang, Bogor 16810
                                    Indonesia</strong></small></p>
                    </div>
                </div>

                <!-- Employee Details -->
                {{-- <div class="row mb-2"> --}}
                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">Employee Details&nbsp;&mdash;&nbsp; Periode {{ $periode }}</h6>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="col-6 mr-2 my-0 py-0"><strong>Name:</strong> <small>{{ $name }}</small>
                                    </td>
                                    <td class="col-6 mr-2 my-0 py-0 text-right"><strong>ID Employee:</strong>
                                        <small>{{ $nik }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 mr-2 my-0 py-0"><strong>Job Title:</strong><small>
                                            {{ $jobTitle }}</small></td>
                                    <td class="col-6 mr-2 my-0 py-0 text-right"><strong>Position:</strong>
                                        <small>{{ $position }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">Presences</h6>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Present</strong></td>
                                    <td class="col-6 my-0 py-0 text-right"><small>{{ $present }} day(s)</small></td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Sick</strong></td>
                                    <td class="col-6 my-0 py-0 text-right"><small>{{ $sick }} day(s)</small></td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Business Trip</strong></td>
                                    <td class="col-6 my-0 py-0 text-right"><small>{{ $businessTrip }} day(s)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Permits</strong></td>
                                    <td class="col-6 my-0 py-0 text-right"><small>{{ $permit }} day(s)</small></td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Absents</strong></td>
                                    <td class="col-6 my-0 py-0 text-right"><small>{{ $absent }} day(s)</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{--
                </div> --}}

                <!-- Earnings and Deductions -->
                {{-- <div class="row mb-4"> --}}
                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">Earnings (IDR) Rp</h6>
                        <table class="table table-bordered table-sm compact">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Basic Salary</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($basicSalary, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Functional Allowance</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($functionalAllowance, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Transport Allowance</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($transportAllowance, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Meal Allowance</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($mealAllowance, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Other Allowance</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($otherAllowance, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Overtimes</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($overtimeEarnings, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Attendance Premium</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($attendancePremium, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>TOTAL EARNINGS</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <strong>{{ number_format($totalEarnings, 2, ",", ".") }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">NETT (IDR) Rp</h6>
                        <table class="table table-bordered table-sm compact">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Annually</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($nett, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">PTKP (IDR) Rp</h6>
                        <table class="table table-bordered table-sm compact">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0">
                                        <strong>Status</strong>&nbsp;&nbsp;&nbsp;&nbsp;<small>{{ $statusPTKP }}</small>
                                    </td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($amountPTKP, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>PKP</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>
                                            {{ number_format($PKP, 2, ",", ".") }}
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 ml-0">
                        <h6 class="text-info my-0 py-0">PPH21 (IDR) Rp</h6>
                        <table class="table table-bordered table-sm compact">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0">
                                        <strong>Per-bulan</strong </td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($PPH21PerBulan, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Per-tahun</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>
                                            {{ number_format($PPH21PerTahun, 2, ",", ".") }}
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12">
                        <h6 class="text-info my-0 py-0">Deductions (IDR) Rp</h6>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>BPJS Kesehatan</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($BPJSKes, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>JHT</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($JHT, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>JIP</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($JIP, 2, ",", ".") }}</small></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>PPH 21</strong> <small>
                                            ({{ $taxableRate }})</small></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($PPH21, 2, ",", ".") }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>Presences</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <small>{{ number_format($presencesDeduction, 2, ",", ".") }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-6 my-0 py-0"><strong>TOTAL DEDUCTIONS (IDR) Rp</strong></td>
                                    <td class="col-6 my-0 py-0 text-right">
                                        <strong>{{ number_format($totalDeductions, 2, ",", ".") }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{--
                </div> --}}

                <!-- Net Pay -->
                {{-- <div class="row mt-4"> --}}
                    <div class="col-md-12 text-right">
                        <h4 class="text-success"><strong>Take Home Pay Rp.
                                {{ number_format($takeHomePay, 2, ",", ".") }}</strong></h4>
                    </div>
                    {{--
                </div> --}}

            </div>
        </div>
    </div>
</body>

</html>