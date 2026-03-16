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


            {{-- Report Table --}}
            
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Table</h3>
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
                                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-medium">Overloaded</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">OK</span>
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


        {{-- Teacher Load Summary --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Teacher Load Summary</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Teacher</th>
                        <th class="px-4 py-3">Assigned Subjects</th>
                        <th class="px-4 py-3">Total Units</th>
                        <th class="px-4 py-3">Max Units</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($teacherSummary as $summary)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $summary['name'] }}</td>
                        <td class="px-4 py-3">{{ $summary['subject_count'] }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $summary['total_units'] }}</td>
                        <td class="px-4 py-3">{{ $summary['max_units'] }}</td>
                        <td class="px-4 py-3">
                            @if($summary['is_overloaded'])
                                <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-medium">Overloaded</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">OK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        </div>
    </div>
</x-app-layout>