<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
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
            font-size: 14px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .header {
            background: url("{{ public_path('images/payslip-header.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position:relative;
            text-align:center;
            color:#fff;
        }
        .page-break {
            page-break-after: always;
        }
        .logo2 {

        }
    </style>
</head>
<body>
    <div class="header">
        <div style="padding:11px;2px solid #000;font-size:1.12rem;position: relative;">
            Serbisyong CongPleyto Movement
        </div>
        <div style="font-size:1rem;padding:11px;position: relative;">
            OFFICIAL PAY
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%"></th>
                <th style="width: 80%"></th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>Date:</td>
                <td>
                    {{\Carbon\Carbon::parse($payroll->period_start)->format('M j, Y')}} - {{\Carbon\Carbon::parse($payroll->period_end)->format('M j, Y')}}
                </td>
            </tr>
            <tr>
                <td>Name:</td>
                <td style="color:#fff;background-color:#3F507F">
                    {{$payroll->employee->first_name}} {{$payroll->employee->last_name}}
                </td>
            </tr>
            <tr>
                <td>Basic Payroll:</td>
                <td style="font-weight: bold;color:#3F507F">₱
                    {{ number_format(
                        $payroll->employee->position->level < 2
                        ? $payroll->gross_salary
                        : $payroll->employee->base_salary / 2, 2
                    ) }}
                </td>
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
            @forelse ($payroll->deductions->where('effective_date', '>=', $payroll->period_start ) as $deduction)
            <tr>
                <td>{{ ucfirst($deduction->type)}}: </td>
                <td style="color:red;font-weight:bold">₱ {{number_format($deduction->amount,2)}}</td>

            </tr>
            @empty
                <tr>
                    <td>Cash Advance: </td>
                    <td style="color:red;font-weight:bold">₱ 0.00</td>

                </tr>
            @endforelse
            <tr>
                <td>Absents</td>
                <td style="color:red">₱ {{ number_format($payroll->absents, 2) }}</td>
            </tr>
        </tbody>
        <tfoot style="background-color:yellowgreen">
            <tr>
                <td style="font-size: 16px"><strong>Total:</strong></td>
                <td><strong style="font-size: 16px">₱ {{ number_format($payroll->net_salary, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
