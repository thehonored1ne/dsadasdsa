<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Teacher Dashboard') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Faculty Portal — Academic Year 2025-2026</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Portal Online</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6 overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight leading-tight">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-gray-600 mt-2 font-medium max-w-lg">Your official teaching load and subject assignments for the current term are listed below.</p>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <a href="{{ route('teacher.export.schedule') }}"
                        class="flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Export My Schedule
                    </a>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Assigned Subjects</p>
                    <p class="text-4xl font-black text-gray-900 leading-none">{{ $totalSubjects }}</p>
                </div>
                
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Total Unit Load</p>
                    <p class="text-4xl font-black text-gray-900 leading-none">{{ $totalUnits }}</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Load Status</p>
                    @if($isOverloaded)
                        <div class="flex items-center gap-2">
                            <p class="text-3xl font-black text-red-600 leading-none uppercase tracking-tight">Overloaded</p>
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                        </div>
                    @else
                        <p class="text-3xl font-black text-emerald-500 leading-none uppercase tracking-tight">Normal</p>
                    @endif
                </div>
            </div>

            {{-- Assigned Subjects Table --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">My Assigned Subjects</h3>
                    <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase">Official Subject and Room Distribution</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Subject Information</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Schedule Context</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Room Location</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Match Rationale</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignments as $assignment)
                            <tr class="group hover:bg-gray-50 transition">
                                <td class="px-6 py-5">
                                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">{{ $assignment->subject->code }}</p>
                                    <p class="text-sm font-black text-gray-900 leading-none">{{ $assignment->subject->name }}</p>
                                </td>
                                <td class="px-6 py-5 text-center font-mono font-bold text-gray-900">{{ $assignment->total_units }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                        <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $assignment->schedule->day }}</span>
                                        {{ $assignment->schedule->time_start }} - {{ $assignment->schedule->time_end }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-gray-600">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $assignment->schedule->room }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $assignment->rationale) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">No subjects have been assigned to your profile yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>