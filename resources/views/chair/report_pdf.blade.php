<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Load Assignment Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        p { font-size: 11px; color: #666; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #e5e7eb;
        }
        td { padding: 8px; border: 1px solid #e5e7eb; }
        .ok { color: green; font-weight: bold; }
        .overloaded { color: red; font-weight: bold; }
    </style>
            </head>
            <body>
            <h2 style="font-size:14px; margin-top:20px;">Teacher Load Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Assigned Subjects</th>
                        <th>Total Units</th>
                        <th>Max Units</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teacherSummary as $summary)
                    <tr>
                        <td>{{ $summary['name'] }}</td>
                        <td>{{ $summary['subject_count'] }}</td>
                        <td>{{ $summary['total_units'] }}</td>
                        <td>{{ $summary['max_units'] }}</td>
                        <td class="{{ $summary['is_overloaded'] ? 'overloaded' : 'ok' }}">
                            {{ $summary['is_overloaded'] ? 'Overloaded' : 'OK' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    <h1>Load Assignment Report</h1>
    <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Code</th>
                <th>Subject</th>
                <th>Units</th>
                <th>Schedule</th>
                <th>Room</th>
                <th>Rationale</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $assignment)
            <tr>
                <td>{{ $assignment->teacherProfile->user->name }}</td>
                <td>{{ $assignment->subject->code }}</td>
                <td>{{ $assignment->subject->name }}</td>
                <td>{{ $assignment->total_units }}</td>
                <td>
                    {{ $assignment->schedule->day }}
                    {{ $assignment->schedule->time_start }} -
                    {{ $assignment->schedule->time_end }}
                </td>
                <td>{{ $assignment->schedule->room }}</td>
                <td>{{ str_replace('_', ' ', $assignment->rationale) }}</td>
                <td class="{{ $assignment->is_overloaded ? 'overloaded' : 'ok' }}">
                    {{ $assignment->is_overloaded ? 'Overloaded' : 'OK' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>