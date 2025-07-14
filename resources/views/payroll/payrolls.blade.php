<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll Slips</title>
    <!-- Google Fonts Link for Roboto -->
    <!-- This link imports the Roboto font from Google's servers. -->
    <!-- It's crucial for the font to be available for use in your CSS. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Basic CSS for PDF */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        .header {
            background: url("{{ public_path('images/payslip-header-sm.jpg') }}");
            background-size: cover;
            background-position: center;
            height: 53px;
            background-repeat: no-repeat;
            position:relative;
            text-align:center;
            color:#fff;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div style="width:100%">
    @php($ctr=0)
    @forelse ($payrolls as $payroll)
    @php($ctr++)
    <div style="width: 32%;float:left;padding-left:2px">
        <div class="header">
            &nbsp;
        </div>
        <table>
            <tr>
                <td style="width: 35%">Date:</td>
                <td style="width: 65%">
                    {{\Carbon\Carbon::parse($payroll->period_start)->format('M j, Y')}} - {{\Carbon\Carbon::parse($payroll->period_end)->format('M j, Y')}}
                </td>
            </tr>
            <tr>
                <td>Name:</td>
                <td style="color:#fff;background-color:#3F507F">
                    @php($first = explode(' ', $payroll->employee->first_name))
                    {{$payroll->employee->first_name}} {{$payroll->employee->last_name}}
                </td>
            </tr>
            <tr>
                <td>Basic Payroll:</td>
                <td style="font-weight: bold;color:#3F507F">₱ {{ number_format(
                        $payroll->employee->position->level < 2
                        ? $payroll->gross_salary
                        : $payroll->employee->base_salary / 2, 2
                ) }}</td>
            </tr>
            <tr>
                <td>Over Time:</td>
                <td style="font-weight: bold;color:#3F507F">&#8369; {{ number_format($payroll->overtime_pay, 2) }}</td>
            </tr>
            <tr>
                <td>Sunday Overtime</td>
                <td style="font-weight: bold;color:#3F507F">&#8369; {{ number_format($payroll->sunday_overtime, 2) }}</td>
            </tr>
            <tr>
                <td>Lates</td>
                <td style="color:red">&#8369; {{ number_format($payroll->lates, 2) }}</td>
            </tr>
            <tr>
                <td>Under Time:</td>
                <td style="color:red">&#8369; {{ number_format($payroll->undertime_pay, 2) }}</td>
            </tr>
            @php($deductions = $payroll->employee->deductions->where('effective_date', '>=', $payroll->period_start))
            @if ($deductions->isNotEmpty())

                @if($deductions->count() > 1)
                    @foreach ($deductions as $deduction)
                        <tr>
                            <td>{{$deduction->type}}: </td>
                            <td style="color:red;font-weight:bold">₱ {{ number_format($deduction->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    @if($payroll->employee->deductions->first() == 'cash-advance')
                    <tr>
                        <td>Cash Advance: </td>
                        <td style="color:red;font-weight:bold">₱ {{ number_format($payroll->employee->deductions->first()->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>loan: </td>
                        <td style="color:red;font-weight:bold">₱ 0.00</td>
                    </tr>
                    @else
                    <tr>
                        <td>Loan: </td>
                        <td style="color:red;font-weight:bold">₱ {{ number_format($payroll->employee->deductions->first()->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Cash Advance: </td>
                        <td style="color:red;font-weight:bold">₱ 0.00</td>
                    </tr>
                    @endif
                @endif
            @else
                <tr>
                    <td>Cash Advance: </td>
                    <td style="color:red;font-weight:bold">₱ 0.00</td>
                </tr>
                <tr>
                    <td>Loan: </td>
                    <td style="color:red;font-weight:bold">₱ 0.00</td>
                </tr>
            @endif
            <tr>
                <td>Absents</td>
                <td style="color:red">₱ {{ number_format($payroll->absents, 2) }}</td>
            </tr>
            <tr style="background-color:yellowgreen">
                <td style="font-size: 11px"><strong>Total:</strong></td>
                <td><strong style="font-size: 11px">₱ {{ number_format($payroll->net_salary, 2) }}</strong></td>
            </tr>
        </table>
    </div>
    @if ($ctr % 3 === 0)
        <div style="clear: both;"></div>
    @endif
    @empty
    <div>
        <!-- Column 1 content -->
        <h2>Sorry!</h2>
        <p>Your payroll is empty!</p>
    </div>
    @endforelse
</div>

</body>
</html>
