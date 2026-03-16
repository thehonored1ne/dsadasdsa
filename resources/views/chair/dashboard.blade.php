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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
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
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Scheduling Conflicts</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $conflictsCount }}</p>
                    <p class="text-xs text-green-500 mt-1">✓ Zero conflicts</p>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Chart 1: Assignments by Rationale (Doughnut) --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Assignments by Rationale</h3>
                    <div class="flex justify-center">
                        <canvas id="rationaleChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Assignments per Day (Bar) --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Assignments per Day</h3>
                    <canvas id="dayChart" style="max-height: 250px;"></canvas>
                </div>

            </div>

            {{-- Chart 3: Units per Teacher (Bar) --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Teacher Load — Assigned Units vs Max Units</h3>
                <canvas id="teacherLoadChart" style="max-height: 300px;"></canvas>
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
                    <a href="{{ route('chair.audit.log') }}"
                        class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                        Audit Log
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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart 1 — Rationale Doughnut
        new Chart(document.getElementById('rationaleChart'), {
            type: 'doughnut',
            data: {
                labels: ['Expertise Match', 'Availability', 'Manual Override'],
                datasets: [{
                    data: [{{ $expertiseCount }}, {{ $availabilityCount }}, {{ $overrideCount }}],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Chart 2 — Assignments per Day Bar
        new Chart(document.getElementById('dayChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Assignments',
                    data: {!! json_encode($assignmentsPerDay) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Chart 3 — Teacher Load Bar
        new Chart(document.getElementById('teacherLoadChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($teacherNames) !!},
                datasets: [
                    {
                        label: 'Assigned Units',
                        data: {!! json_encode($teacherUnits) !!},
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                    },
                    {
                        label: 'Max Units',
                        data: {!! json_encode($teacherMaxUnits) !!},
                        backgroundColor: '#e5e7eb',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 3 } }
                }
            }
        });
    </script>

</x-app-layout>