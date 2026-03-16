<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Load Assignment Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Export Buttons --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Load Assignment Report</h3>
                    <p class="text-sm text-gray-500">{{ now()->format('F d, Y') }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('chair.report.csv') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                        Export CSV
                    </a>
                    <a href="{{ route('chair.report.pdf') }}"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        Export PDF
                    </a>
                </div>
            </div>

            {{-- Consolidated Load Assignment Report --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Load Assignment Report</h3>
                <p class="text-sm text-gray-500 mb-4">Shows each teacher's assigned subjects, total units, rationale, and overload status.</p>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Teacher Name</th>
                            <th class="px-4 py-3">Subject Code</th>
                            <th class="px-4 py-3">Subject Name</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Rationale</th>
                            <th class="px-4 py-3">Total Units</th>
                            <th class="px-4 py-3">Max Units</th>
                            <th class="px-4 py-3">Overload Flag</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($teacherSummary as $summary)
                            @php
                                $teacherAssignments = $assignments->filter(
                                    fn($a) => $a->teacherProfile->user->name === $summary['name']
                                );
                                $firstRow = true;
                            @endphp
                            @foreach($teacherAssignments as $assignment)
                            <tr class="hover:bg-gray-50">
                                @if($firstRow)
                                <td class="px-4 py-3 font-semibold align-top" rowspan="{{ $teacherAssignments->count() }}">
                                    {{ $summary['name'] }}
                                </td>
                                @php $firstRow = false; @endphp
                                @endif
                                <td class="px-4 py-3">{{ $assignment->subject->code }}</td>
                                <td class="px-4 py-3">{{ $assignment->subject->name }}</td>
                                <td class="px-4 py-3">{{ $assignment->total_units }}</td>
                                <td class="px-4 py-3 capitalize">
                                    {{ str_replace('_', ' ', $assignment->rationale) }}
                                </td>
                                @if($loop->first)
                                <td class="px-4 py-3 font-semibold align-top" rowspan="{{ $teacherAssignments->count() }}">
                                    {{ $summary['total_units'] }}
                                </td>
                                <td class="px-4 py-3 align-top" rowspan="{{ $teacherAssignments->count() }}">
                                    {{ $summary['max_units'] }}
                                </td>
                                <td class="px-4 py-3 align-top" rowspan="{{ $teacherAssignments->count() }}">
                                    @if($summary['is_overloaded'])
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-medium">⚠️ Overloaded</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">✓ OK</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @if($teacherAssignments->isEmpty())
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold">{{ $summary['name'] }}</td>
                                <td colspan="4" class="px-4 py-3 text-gray-400">No subjects assigned</td>
                                <td class="px-4 py-3 font-semibold">{{ $summary['total_units'] }}</td>
                                <td class="px-4 py-3">{{ $summary['max_units'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">✓ OK</span>
                                </td>
                            </tr>
                            @endif
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                                No assignments found. Generate a schedule first.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Schedule Details --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Full Schedule Details</h3>
                <p class="text-sm text-gray-500 mb-4">Complete list of all assignments with schedule and room information.</p>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3">Subject Code</th>
                            <th class="px-4 py-3">Subject Name</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Schedule</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Rationale</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $assignment->teacherProfile->user->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->subject->code }}</td>
                            <td class="px-4 py-3">{{ $assignment->subject->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->total_units }}</td>
                            <td class="px-4 py-3">
                                {{ $assignment->schedule->day }}
                                {{ $assignment->schedule->time_start }} -
                                {{ $assignment->schedule->time_end }}
                            </td>
                            <td class="px-4 py-3">{{ $assignment->schedule->room }}</td>
                            <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $assignment->rationale) }}</td>
                            <td class="px-4 py-3">
                                @if($assignment->is_overloaded)
                                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-medium">⚠️ Overloaded</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">✓ OK</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                                No assignments found. Generate a schedule first.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>