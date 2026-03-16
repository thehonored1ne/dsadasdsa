<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Assignments') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Schedule Management</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Live Engine</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Notifications --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Auto-Generate Hero Section --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6 overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Auto-Generate Schedule</h3>
                    <p class="text-sm text-gray-600 mt-1 font-medium max-w-md">Our engine matches teachers to subjects by expertise then availability automatically.</p>
                </div>
                <form method="POST" action="{{ route('chair.assignments.generate') }}" class="relative z-10 w-full md:w-auto">
                    @csrf
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                        ⚡ Generate Schedule
                    </button>
                </form>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <form method="GET" action="{{ route('chair.assignments') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Teacher or subject..."
                            class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Rationale</label>
                        <select name="rationale" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">All Logic</option>
                            <option value="expertise_match" {{ request('rationale') === 'expertise_match' ? 'selected' : '' }}>Expertise Match</option>
                            <option value="availability" {{ request('rationale') === 'availability' ? 'selected' : '' }}>Availability</option>
                            <option value="manual_override" {{ request('rationale') === 'manual_override' ? 'selected' : '' }}>Manual Override</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Status</label>
                        <select name="status" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">All Status</option>
                            <option value="ok" {{ request('status') === 'ok' ? 'selected' : '' }}>✓ OK</option>
                            <option value="overloaded" {{ request('status') === 'overloaded' ? 'selected' : '' }}>⚠ Overloaded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Schedule Day</label>
                        <select name="day" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">All Days</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                <option value="{{ $day }}" {{ request('day') === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex items-center gap-3 pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition text-xs font-black uppercase tracking-widest">
                            Apply Filters
                        </button>
                        <a href="{{ route('chair.assignments') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition text-xs font-black uppercase tracking-widest">
                            Clear
                        </a>
                        <span class="ml-auto text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Found {{ $assignments->count() }} results
                        </span>
                    </div>
                </form>
            </div>

            {{-- Main Table --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Assignment Details</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Schedule Context</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Match Logic</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Override</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignments as $assignment)
                            <tr class="group hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-gray-900 leading-none mb-1">{{ $assignment->teacherProfile->user->name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $assignment->subject->name }}</p>
                                </td>
                                <td class="px-6 py-5 text-xs font-bold text-gray-600">
                                    <span class="block text-indigo-600 mb-1 tracking-tight">{{ $assignment->schedule->day }}</span>
                                    {{ $assignment->schedule->time_start }} - {{ $assignment->schedule->time_end }}
                                    <span class="text-gray-400 ml-1">[{{ $assignment->schedule->room }}]</span>
                                </td>
                                <td class="px-6 py-5 text-center font-mono font-bold text-gray-900">{{ $assignment->total_units }}</td>
                                <td class="px-6 py-5">
                                    @if(!is_null($assignment->match_score))
                                        @php $percent = $assignment->match_score * 100; @endphp
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center justify-between text-[10px] font-black uppercase text-gray-400">
                                                <span>{{ str_replace('_', ' ', $assignment->rationale) }}</span>
                                                <span>{{ $percent }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1">
                                                <div class="bg-indigo-600 h-1 rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ str_replace('_', ' ', $assignment->rationale) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if($assignment->is_overloaded)
                                        <span class="px-3 py-1 bg-red-50 text-red-700 rounded-lg text-[10px] font-black uppercase tracking-widest">Overloaded</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest">Normal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <form method="POST" action="{{ route('chair.assignments.override') }}" class="flex items-center justify-end gap-2">
                                        @csrf
                                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                        <select name="teacher_profile_id" class="text-[11px] font-bold border-gray-200 rounded-lg bg-gray-50 py-1 pl-2 pr-8 focus:ring-indigo-500">
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ $teacher->id === $assignment->teacher_profile_id ? 'selected' : '' }}>
                                                    {{ $teacher->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="p-1.5 bg-gray-900 text-white rounded-lg hover:bg-indigo-600 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic font-medium">No active assignments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Unassigned Warning Section --}}
            @if($unassignedSubjects->count() > 0)
            <div class="bg-white border-2 border-red-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="p-1.5 bg-red-100 text-red-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5"/></svg>
                            </span>
                            Unassigned Subjects
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 font-medium italic">These subjects lack available teachers with matching expertise.</p>
                    </div>
                    <span class="px-4 py-1 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-100">
                        {{ $unassignedSubjects->count() }} Critical
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($unassignedSubjects as $subject)
                    <div class="bg-red-50/50 border border-red-100 rounded-2xl p-5 flex flex-col justify-between group hover:bg-red-50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">{{ $subject->code }}</span>
                                <h4 class="text-sm font-black text-gray-900 leading-tight mt-1">{{ $subject->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1 font-bold">Units: {{ $subject->units }} | Pre: {{ $subject->prerequisites ?? 'None' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('chair.assignments.override') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <select name="teacher_profile_id" class="flex-grow text-[11px] font-bold border-red-100 rounded-xl bg-white py-2 focus:ring-red-500">
                                <option value="">Select Teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition text-[10px] font-black uppercase tracking-widest">
                                Assign
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>