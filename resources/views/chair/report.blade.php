<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Load Assignment Report') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Official Academic Records</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Live Report</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Success Notification --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header/Export Card --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6 overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Consolidated Report</h3>
                    <p class="text-sm text-gray-500 mt-1 font-medium italic">Snapshot Date: {{ now()->format('F d, Y') }}</p>
                </div>
                <div class="relative z-10 flex gap-3 w-full md:w-auto">
                    <a href="{{ route('chair.report.csv') }}"
                        class="flex-1 md:flex-none px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition text-[10px] font-black uppercase tracking-widest text-center shadow-lg shadow-emerald-100">
                        Export CSV
                    </a>
                    <a href="{{ route('chair.report.pdf') }}"
                        class="flex-1 md:flex-none px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition text-[10px] font-black uppercase tracking-widest text-center shadow-lg shadow-red-100">
                        Export PDF
                    </a>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            {{-- Consolidated Report Table --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Teacher Assignment Matrix</h3>
                    <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase">Unit distribution and rationale analysis</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Teacher</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Subject Breakdown</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Match Rationale</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Load Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($teacherSummary as $summary)
                                @php
                                    $teacherAssignments = $assignments->filter(fn($a) => $a->teacherProfile->user->name === $summary['name']);
                                    $rowSpan = $teacherAssignments->count() ?: 1;
                                @endphp
                                
                                @if($teacherAssignments->isNotEmpty())
                                    @foreach($teacherAssignments as $assignment)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        @if($loop->first)
                                        <td class="px-6 py-5 align-top border-r border-gray-50 bg-gray-50/20" rowspan="{{ $rowSpan }}">
                                            <p class="text-sm font-black text-gray-900">{{ $summary['name'] }}</p>
                                            <div class="mt-2 text-[10px] font-bold text-gray-500 uppercase tracking-tighter">
                                                Total: {{ $summary['total_units'] }} / {{ $summary['max_units'] }}
                                            </div>
                                        </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            <span class="text-[10px] font-black text-indigo-600 block leading-none">{{ $assignment->subject->code }}</span>
                                            <span class="text-xs font-bold text-gray-600">{{ $assignment->subject->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono font-bold text-gray-900 text-xs">{{ $assignment->total_units }}</td>
                                        <td class="px-6 py-4">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tight">{{ str_replace('_', ' ', $assignment->rationale) }}</span>
                                        </td>
                                        @if($loop->first)
                                        <td class="px-6 py-5 text-right align-top" rowspan="{{ $rowSpan }}">
                                            @if($summary['is_overloaded'])
                                                <span class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-red-100">⚠️ Overload</span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100">✓ Normal</span>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                @else
                                    {{-- Empty Assignment Row --}}
                                    <tr class="hover:bg-gray-50/30">
                                        <td class="px-6 py-5 font-black text-sm text-gray-900">{{ $summary['name'] }}</td>
                                        <td colspan="3" class="px-6 py-4 text-xs italic text-gray-400">No subjects currently assigned</td>
                                        <td class="px-6 py-5 text-right">
                                            <span class="inline-flex px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-[10px] font-black uppercase tracking-widest">Vacant</span>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Full Details/Schedule Grid --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Full Schedule Log</h3>
                    <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase">Detailed spatial and temporal distribution</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Teacher / Subject</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Schedule & Room</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignments as $assignment)
                            <tr class="group hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="text-xs font-black text-gray-900">{{ $assignment->teacherProfile->user->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold">{{ $assignment->subject->code }} — {{ $assignment->subject->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center gap-2 text-[11px] font-bold text-gray-600">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $assignment->schedule->day }} ({{ $assignment->schedule->time_start }} - {{ $assignment->schedule->time_end }})
                                        <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-500 text-[10px]">Room {{ $assignment->schedule->room }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-bold text-gray-900 text-xs">{{ $assignment->total_units }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if($assignment->is_overloaded)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                            Overload
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            Pass
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">No schedule data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>