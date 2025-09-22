<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export Attendance</title>
</head>
<style>
    @page {
        margin: 0;
    }
    /* Basic CSS for PDF */
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 8pt;
        color: #333;
        font-weight: normal;
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
        /* background: url("{{ url('images/payslip-header.jpg') }}"); */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position:relative;
        text-align:center;
    }
    .page-break {
        page-break-after: always;
    }
</style>
<body>
    @php
        $ctr = 0;
    @endphp
    @foreach ($attendances as $item)
        @php
            $ctr++;
        @endphp
        <div style="width: 47%; {{ $ctr % 2 === 0 ? 'float:right;margin-right: 10px;' : 'float:left;margin-left:10px;' }} padding-left:2px;  margin-top: 10px">
            <div style="margin: 0">
                <table style="width: 100%; border-collapse: collapse; border:none; margin:0;padding:0">
                    <tr>
                        <td style="width: 15%;border:none">
                            <img style="width: 100%" src="{{public_path('/images/sp_logo-sm.png')}}" alt="">
                        </td>
                        <td style="background-color: text-align: center; border:none">
                            <p style="text-align: center; font-weight: bold; font-size: 10pt">Serbisyong CongPleyto Movement</p>
                        </td>
                        <td style="width: 15%;border:none">
                            <img style="width: 100%" src="{{public_path('/images/hrp_logo.png')}}" alt="">
                        </td>
                    </tr>
                </table>
                <hr style="margin:0;padding:0; border: none; border-top: 1px solid #000">
                <p style="text-align: center; padding-top 10px; margin-0; font-size: 11pt">Daily Attendance</p>
                <table style="border:none">
                    <tr>
                        <td style="width: 50%;border:none;border-bottom:1px solid;padding-bottom: 0;font-weight: bold">{{ $item['last_name']}}</td>
                        <td style="width: 50%;border:none;border-bottom:1px solid;padding-bottom: 0;font-weight: bold">{{ $item['first_name']}}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;border:none; padding-top: 0">Last Name</td>
                        <td style="width: 50%;border:none; padding-top: 0">First Name</td>
                    </tr>
                </table>
                <div>
                    <div>
                        @php
                            $month = now()->format('F');
                        @endphp
                        For the month of {{ $month }}
                    </div>
                </div>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 7%; text-align:center">Day</th>
                        <th rowspan="2" style="width: 17%;text-align:center">Time-in</th>
                        <th rowspan="2" style="text-align:center">Time-out</th>
                        {{-- <th colspan="2" style="text-align:center">Undertime</th> --}}
                        <th style="width: 20%;text-align:center" colspan="2">Undertime</th>

                    </tr>
                    <tr>

                        <th style="text-align: center">Hours</th>
                        <th style="text-align: center">Minutes</th>
                    </tr>
                </thead>

                <tbody>
                @foreach ($item['days'] as $key => $day)
                    @php
                        $undertime = $day['undertime'] ?? 0;
                        $hours = floor($undertime);
                        $minutes = floor(($undertime - $hours) * 60);
                    @endphp

                    <tr>
                        <td style="padding: 2px; text-align:center">{{ $key + 1 }}</td>
                        <td style="padding: 2px; text-align:center">{{ $day ? Carbon\Carbon::parse($day['in'])->format('h:i A') : '-' }}</td>
                        <td style="padding: 2px; text-align:center">{{ $day ? Carbon\Carbon::parse($day['out'])->format('h:i A') : '-' }}</td>
                        <td style="padding: 2px; text-align:center">{{ $hours ?? '-' }}</td>
                        <td style="padding: 2px; text-align:center">{{ $minutes ?? '-' }}</td>
                    </tr>
                @endforeach

                @php
                    $totalUndertime = $item['total_undertime'] ?? 0;
                    $totalHours = floor($totalUndertime);
                    $totalMinutes = floor(($totalUndertime - $totalHours) * 60);
                @endphp
                <tr>
                    <td colspan="3" style="padding: 2px; text-align:right">Total:</td>
                    <td style="padding: 2px; text-align:center">{{ $totalHours ?? '-' }}</td>
                    <td style="padding: 2px; text-align:center">{{ $totalMinutes ?? '-' }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        @if ($ctr % 2 === 0)
            <div style="clear: both;" class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
