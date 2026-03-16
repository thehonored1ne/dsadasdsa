<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Account Management') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Faculty Records & Credentials</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Active Database</span>
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

            @if(session('error'))
                <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Add Teacher Form --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                        Add New Teacher
                    </h3>
                    
                    <form method="POST" action="{{ route('chair.teachers.add') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Juan Dela Cruz"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. juan@school.edu"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Expertise Areas</label>
                                <input type="text" name="expertise_areas" value="{{ old('expertise_areas') }}" placeholder="e.g. Programming|Mathematics"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[9px] text-gray-400 font-bold mt-1 italic uppercase tracking-tighter">Use pipe | as separator</p>
                                @error('expertise_areas') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Max Unit Load</label>
                                <input type="number" name="max_units" value="{{ old('max_units', 21) }}" min="1" max="30"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('max_units') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Account Password</label>
                                <input type="password" name="password" placeholder="••••••••"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('password') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Availability Section --}}
                        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Availability Schedule (Optional)</label>
                            <div id="availability-rows" class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <select name="days[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm w-full sm:w-40 focus:ring-indigo-500">
                                        <option value="">-- Day --</option>
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                    <input type="time" name="time_starts[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">to</span>
                                    <input type="time" name="time_ends[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                                    <button type="button" onclick="addAvailabilityRow()"
                                        class="px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-100 transition text-[10px] font-black uppercase tracking-widest">
                                        + Add Row
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-10 py-3 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                                Register Teacher
                            </button>
                        </div>
                    </form>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            {{-- Teachers List --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Faculty Roster</h3>
                        <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">{{ $teachers->count() }} Profiles onboarded</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Identity</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Expertise</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Load Config</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($teachers as $teacher)
                            <tr class="group hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-black group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                            {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 leading-none">{{ $teacher->user->name }}</p>
                                            <p class="text-xs text-gray-500 font-medium mt-1">{{ $teacher->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach(explode('|', $teacher->expertise_areas) as $area)
                                            @if(!empty(trim($area)))
                                            <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-[9px] font-black uppercase tracking-widest border border-indigo-100">
                                                {{ trim($area) }}
                                            </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex flex-col gap-1 items-center justify-center">
                                        <span class="text-xs font-black text-gray-900 leading-none">{{ $teacher->assignments->count() }} Assignments</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Cap: {{ $teacher->max_units }} Units</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('chair.teachers.edit', $teacher->id) }}"
                                            class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('chair.teachers.delete', $teacher->id) }}"
                                            onsubmit="return confirm('Archive faculty record for {{ $teacher->user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-medium">No faculty profiles found. Register one above to begin.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function addAvailabilityRow() {
            const container = document.getElementById('availability-rows');
            const div = document.createElement('div');
            div.className = 'flex flex-wrap items-center gap-3 animate-in fade-in duration-300';
            div.innerHTML = `
                <select name="days[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm w-full sm:w-40 focus:ring-indigo-500">
                    <option value="">-- Day --</option>
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
                <input type="time" name="time_starts[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">to</span>
                <input type="time" name="time_ends[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()"
                    class="px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition text-[10px] font-black uppercase tracking-widest border border-red-100">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }
    </script>
</x-app-layout>