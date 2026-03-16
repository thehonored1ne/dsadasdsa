<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-gray-500 mt-1">View your assigned subjects and teaching load below.</p>
                </div>
                <a href="{{ route('teacher.export.schedule') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                    Export My Schedule
                </a>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Assigned Subjects</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalSubjects }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Total Units</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUnits }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Load Status</p>
                    @if($isOverloaded)
                        <p class="text-3xl font-bold text-red-600 mt-1">Overloaded</p>
                    @else
                        <p class="text-3xl font-bold text-green-600 mt-1">Normal</p>
                    @endif
                </div>
            </div>

            {{-- Assigned Subjects Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">My Assigned Subjects</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Subject Code</th>
                            <th class="px-4 py-3">Subject Name</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Schedule</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Rationale</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                No subjects assigned yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>