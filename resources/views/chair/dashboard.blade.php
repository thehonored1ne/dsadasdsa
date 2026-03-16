<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Program Chair Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}!</h3>
                <p class="text-sm text-gray-500 mt-1">Manage teacher assignments, upload files, and generate reports from this dashboard.</p>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Total Teachers</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalTeachers }}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Total Subjects</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalSubjects }}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Total Assignments</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAssignments }}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Overloaded Teachers</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $overloadedCount }}</p>
                </div>

            </div>

            {{-- Quick Actions --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('chair.upload') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Upload Files
                    </a>
                    <a href="{{ route('chair.assignments') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                        View Assignments
                    </a>
                    <a href="{{ route('chair.report') }}"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        Generate Report
                    </a>
                </div>
            </div>

            {{-- Recent Assignments --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Assignments</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Rationale</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAssignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $assignment->teacherProfile->user->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->subject->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->total_units }}</td>
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
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                No assignments yet. Go to Assignments to generate a schedule.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>