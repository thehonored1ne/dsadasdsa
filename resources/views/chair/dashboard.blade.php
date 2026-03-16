<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Program Chair Dashboard') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Academic Control Center</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">System Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Top Row: Unified White Cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-2 relative overflow-hidden bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col justify-center">
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Welcome back,<br>{{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-gray-600 mt-3 leading-relaxed">You have <span class="text-red-700 font-bold">{{ $overloadedCount }} overloaded</span> teachers. Distribution data is current.</p>
                    <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
                </div>
                
                {{-- Fixed: Total Assignments now matches the rest --}}
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col justify-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-1">Total Assignments</p>
                    <p class="text-4xl font-black text-gray-900">{{ $totalAssignments }}</p>
                    <div class="mt-4">
                        <span class="text-[10px] font-bold py-1 px-2 bg-indigo-50 rounded-lg text-indigo-700 border border-indigo-100 uppercase tracking-tighter">Spring 2026 Term</span>
                    </div>
                </div>

                {{-- Stats: Conflicts --}}
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col justify-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-1">Conflicts Found</p>
                    <p class="text-4xl font-black {{ $conflictsCount > 0 ? 'text-red-600' : 'text-emerald-500' }}">{{ $conflictsCount }}</p>
                    <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase">✓ Data Integrity</p>
                </div>
            </div>

            {{-- Main Layout Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                
                {{-- Left Side: Charts --}}
                <div class="lg:col-span-2 flex flex-col gap-8">
                    {{-- Load Analysis Bar Chart --}}
                    <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                                Load Analysis
                            </h3>
                            <div class="flex gap-4">
                                <span class="flex items-center gap-1.5 text-[10px] font-black text-gray-600 uppercase"><span class="w-2 h-2 rounded-full bg-indigo-600"></span> Assigned</span>
                                <span class="flex items-center gap-1.5 text-[10px] font-black text-gray-600 uppercase"><span class="w-2 h-2 rounded-full bg-slate-200"></span> Max</span>
                            </div>
                        </div>
                        <div class="h-[280px] w-full">
                            <canvas id="teacherLoadChart"></canvas>
                        </div>
                    </div>

                    {{-- Bottom Charts Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 flex-grow">
                        {{-- Volume Chart --}}
                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col h-full">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-1">Daily Volume</h3>
                            <p class="text-[10px] text-gray-500 font-bold mb-6 italic">Last 7 days trend</p>
                            <div class="flex-grow w-full relative min-h-[220px]">
                                <canvas id="dayChart"></canvas>
                            </div>
                        </div>

                        {{-- Rationale Chart --}}
                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col h-full">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 text-center">Rationale Logic</h3>
                            <div class="flex-grow w-full relative min-h-[220px]">
                                <canvas id="rationaleChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Feed & Actions --}}
                <div class="flex flex-col gap-8">
                    {{-- Fixed: Command Center now matches the white UI --}}
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4">Command Center</h3>
                        <div class="grid grid-cols-1 gap-2">
                            <a href="{{ route('chair.upload') }}" class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100 group text-indigo-700">
                                <span class="text-xs font-black uppercase tracking-widest">Upload Data</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                            </a>
                            <a href="{{ route('chair.report') }}" class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100 group text-indigo-700">
                                <span class="text-xs font-black uppercase tracking-widest">Reports</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2.5" stroke-linecap="round"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Activity Feed --}}
                    <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden flex flex-col flex-grow">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest leading-none">Activity Feed</h3>
                        </div>
                        <div class="divide-y divide-gray-50 overflow-y-auto flex-grow custom-scrollbar" style="max-height: 520px;">
                            @forelse($recentAssignments->take(10) as $assignment)
                            <div class="p-4 hover:bg-gray-50 transition flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-[10px] font-black group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    {{ strtoupper(substr($assignment->teacherProfile->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-black text-gray-900 truncate tracking-tight">{{ $assignment->teacherProfile->user->name }}</p>
                                    <p class="text-[10px] text-gray-600 font-bold uppercase tracking-tighter truncate">{{ $assignment->subject->name }}</p>
                                </div>
                                <div class="text-[9px] font-black text-gray-500 uppercase whitespace-nowrap pt-1">
                                    {{ $assignment->created_at->diffForHumans(null, true) }}
                                </div>
                            </div>
                            @empty
                            <div class="p-12 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest italic">No recent activity</div>
                            @endforelse
                        </div>
                        <a href="{{ route('chair.assignments') }}" class="block p-5 text-center text-[9px] font-black text-indigo-600 hover:bg-indigo-50 border-t border-gray-50 uppercase tracking-[0.25em] transition bg-white mt-auto">
                            Full Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const uiColors = {
            indigo: '#4f46e5',
            emerald: '#10b981',
            amber: '#f59e0b',
            slateFill: '#E2E8F0', 
            textSecondary: '#64748b'
        };

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.weight = '700';
        Chart.defaults.color = uiColors.textSecondary;

        // Load Analysis Chart
        new Chart(document.getElementById('teacherLoadChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($teacherNames) !!},
                datasets: [
                    { label: 'Units', data: {!! json_encode($teacherUnits) !!}, backgroundColor: uiColors.indigo, borderRadius: 8, barThickness: 24 },
                    { label: 'Max', data: {!! json_encode($teacherMaxUnits) !!}, backgroundColor: uiColors.slateFill, borderRadius: 8, barThickness: 24 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                    y: { grid: { borderDash: [4, 4], color: '#CBD5E1' }, beginAtZero: true }
                }
            }
        });

        // Daily Volume Chart
        new Chart(document.getElementById('dayChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    data: {!! json_encode($assignmentsPerDay) !!},
                    backgroundColor: uiColors.indigo + '30',
                    borderColor: uiColors.indigo,
                    borderWidth: 2,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                    y: { grid: { color: '#E2E8F0' }, ticks: { font: { size: 9 }, stepSize: 1 } }
                }
            }
        });

        // Rationale Doughnut
        new Chart(document.getElementById('rationaleChart'), {
            type: 'doughnut',
            data: {
                labels: ['Expertise', 'Availability', 'Override'],
                datasets: [{
                    data: [{{ $expertiseCount }}, {{ $availabilityCount }}, {{ $overrideCount }}],
                    backgroundColor: [uiColors.indigo, uiColors.emerald, uiColors.amber],
                    borderWidth: 5,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 9 } } }
                }
            }
        });
    </script>
</x-app-layout>