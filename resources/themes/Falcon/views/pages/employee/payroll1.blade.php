<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Payroll-{{ $name . "-" . $periode }}</title>
</head>

<body class="py-2">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="container mt-4">
                    <h5 class="text-center">
                        PT. CLEANVY ASALTA INDONESIA
                    </h5>
                    <h6 class="text-center">
                        <i>Jl. Kampung Babakan Cikeas RT/RW 003/003
                            Desa
                            Sentul
                            Kecamatan Babakan Madang, Bogor 16810 Indonesia</i>
                    </h6>
                    <h5 class="text-center">
                        Salary Slip
                    </h5>
                    <h6 class="text-center">{{ $periode }}</h6>
                </div>

                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <table border="0" class="table">
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left border-top">Employee ID:</td>
                                    <td class="text-md-left px-1 py-0 border-top border-right font-weight-bold">
                                        {{ $nik }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">Name:</td>
                                    <td class="text-md-left px-1 py-0 border-right font-weight-bold">{{ $name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">NIK(KTP):</td>
                                    <td class="text-md-left px-1 py-0 border-right"></td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">PTKP Status:</td>
                                    <td class="text-md-left px-1 py-0 border-right"></td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left border-bottom">Remain "CUTI":</td>
                                    <td class="text-md-left px-1 py-0 border-right border-bottom font-weight-bold">
                                        {{ $remainingQuotaLeave }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table border="0" class="table">
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left border-top">Month:</td>
                                    <td class="text-md-left px-1 py-0 border-top border-right font-weight-bold">
                                        {{ $month }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">Working Days:</td>
                                    <td class="text-md-left px-1 py-0 border-right font-weight-bold">{{ $workingDays }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">Holiday Days:</td>
                                    <td class="text-md-left px-1 py-0 border-right font-weight-bold">{{ $holidayDays }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left">Collective Leave:</td>
                                    <td class="text-md-left px-1 py-0 border-right font-weight-bold">{{ $leaves }}</td>
                                </tr>
                                <tr>
                                    <td class="text-md-right px-1 py-0 border-left border-bottom">Sick Leave:</td>
                                    <td class="text-md-left px-1 py-0 border-right border-bottom font-weight-bold">
                                        {{ $sicks }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="container mt-2">
                    <div class="row justify-content-center">
                        <div class="col-5">
                            <table class="table">
                                <thead>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </thead>
                                <tr class="bg-light">
                                    <td class="py-1 border font-weight-bold" colspan="4">
                                        INCOME
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-1 text-right">
                                        1.
                                    </td>
                                    <td class="p-1">
                                        Basic Salary
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $basicSalary }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        After Adjustment
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $basicSalary }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-1 text-right">
                                        2.
                                    </td>
                                    <td class="p-1">
                                        Allowances
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        Functional
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $functionalAllowance }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        Transportation
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $transportAllowance }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        Meal
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $mealAllowance }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        Other
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $otherAllowance }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-1 text-right">
                                        3.
                                    </td>
                                    <td class="p-1">
                                        Attendance
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-left font-weight-bold">{{ $attendancePremium }}</span>

                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-1 text-right">
                                        4.
                                    </td>
                                    <td class="p-1">
                                        Overtime
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        OUTBOUND
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $overtimeEarnings }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        INBOUND
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">0,00</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-1 text-right">
                                        5.
                                    </td>
                                    <td class="p-1">
                                        THR
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-left font-weight-bold">{{ $eidAllowance }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        TOTAL INCOME
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $totalIncome }}</span>
                                    </td>
                                </tr>

                                <tr class="bg-light">
                                    <td class="py-1 border font-weight-bold" colspan="4">
                                        TAX RELATED
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        BPJS Kes Comp. +
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $BPJSFromCompany }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        JKK Comp. +
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JKKFromCompany }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right">
                                        JKM Comp. +
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JKMFromCompany }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        BRUTO PER MONTH
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $brutoEarnings }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        JPP Emp. -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JPFromEmployee }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        JHT Emp. -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JHTFromEmployee }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="p-1 text-right font-weight-bold">
                                        NETT PER MONTH
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $nettEarnings }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table">
                                <thead>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </thead>
                                <tr class="bg-light">
                                    <td class="py-1 border font-weight-bold" colspan="3">
                                        TAKE HOME PAY
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-right font-weight-bold">
                                        TOTAL INCOME
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $totalIncome }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-right font-weight-bold">
                                        BPJS Emp. -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $BPJSFromEmployee }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-right font-weight-bold">
                                        JP Emp. -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JPFromEmployee }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-right font-weight-bold">
                                        JHT Emp. -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $JHTFromEmployee }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-right font-weight-bold">
                                        PPH21 -
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $pph21 }}</span>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="p-1 text-right font-weight-bold">
                                        TAKE HOME PAY
                                    </td>
                                    <td class="p-1">
                                        <span class="text-right font-weight-bold">IDR</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <span class="text-right font-weight-bold">{{ $takeHomePay }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</body>

</html>