<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Load Assignment Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 8px; }
        p { font-size: 10px; color: #666; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 6px 8px;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #e5e7eb;
        }
        td { padding: 6px 8px; border: 1px solid #e5e7eb; vertical-align: top; }
        .ok { color: green; font-weight: bold; }
        .overloaded { color: red; font-weight: bold; }
        .teacher-name { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Load Assignment Report</h1>
    <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>

    <h2>Load Assignment Details</h2>
    <table>
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Units</th>
                <th>Rationale</th>
                <th>Total Units</th>
                <th>Max Units</th>
                <th>Overload Flag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teacherSummary as $summary)
                @php
                    $teacherAssignments = $assignments->filter(
                        fn($a) => $a->teacherProfile->user->name === $summary['name']
                    );
                    $count = $teacherAssignments->count();
                    $first = true;
                @endphp
                @foreach($teacherAssignments as $assignment)
                <tr>
                    @if($first)
                    <td class="teacher-name" rowspan="{{ $count }}">{{ $summary['name'] }}</td>
                    @php $first = false; @endphp
                    @endif
                    <td>{{ $assignment->subject->code }}</td>
                    <td>{{ $assignment->subject->name }}</td>
                    <td>{{ $assignment->total_units }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($assignment->rationale)) }}</td>
                    @if($loop->first)
                    <td rowspan="{{ $count }}" style="font-weight:bold;">{{ $summary['total_units'] }}</td>
                    <td rowspan="{{ $count }}">{{ $summary['max_units'] }}</td>
                    <td rowspan="{{ $count }}" class="{{ $summary['is_overloaded'] ? 'overloaded' : 'ok' }}">
                        {{ $summary['is_overloaded'] ? 'Overloaded' : 'OK' }}
                    </td>
                    @endif
                </tr>
                @endforeach
                @if($count === 0)
                <tr>
                    <td class="teacher-name">{{ $summary['name'] }}</td>
                    <td colspan="7" style="color:#999;">No subjects assigned</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- Full Schedule Details --}}
    <h2>Full Schedule Details</h2>
    <table>
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
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
                <td>{{ str_replace('_', ' ', ucfirst($assignment->rationale)) }}</td>
                <td class="{{ $assignment->is_overloaded ? 'overloaded' : 'ok' }}">
                    {{ $assignment->is_overloaded ? 'Overloaded' : 'OK' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>