<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Edit Teacher') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Modify Faculty Credentials</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Editing Mode</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Success Notification --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-100">
                            {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">Edit Teacher — {{ $teacher->user->name }}</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">ID: #{{ $teacher->id }} Faculty Member</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('chair.teachers.update', $teacher->id) }}" class="space-y-8">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $teacher->user->name) }}" 
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $teacher->user->email) }}" 
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Expertise Areas</label>
                                <input type="text" name="expertise_areas" value="{{ old('expertise_areas', $teacher->expertise_areas) }}" 
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[9px] text-gray-400 font-bold mt-1 italic uppercase tracking-tighter">Current format uses pipe | as separator</p>
                                @error('expertise_areas') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Maximum Unit Load</label>
                                <input type="number" name="max_units" value="{{ old('max_units', $teacher->max_units) }}" min="1" max="30"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                @error('max_units') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Availability --}}
                        <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Availability Schedule</label>
                                <button type="button" onclick="addAvailabilityRow()"
                                    class="px-4 py-1.5 bg-white text-indigo-600 border border-indigo-100 rounded-xl hover:bg-indigo-50 transition text-[10px] font-black uppercase tracking-widest">
                                    + Add New Slot
                                </button>
                            </div>

                            <div id="availability-rows" class="space-y-3">
                                @forelse($teacher->availabilities as $availability)
                                <div class="flex flex-wrap items-center gap-3 animate-in fade-in duration-300">
                                    <select name="days[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm w-full sm:w-40 focus:ring-indigo-500">
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}" {{ $availability->day === $day ? 'selected' : '' }}>{{ $day }}</option>
                                        @endforeach
                                    </select>
                                    <input type="time" name="time_starts[]" value="{{ $availability->time_start }}" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">to</span>
                                    <input type="time" name="time_ends[]" value="{{ $availability->time_end }}" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                @empty
                                <div class="text-center py-4 text-gray-400 italic text-xs">No active availability slots found.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-6 border-t border-gray-50">
                            <a href="{{ route('chair.teachers') }}"
                                class="px-6 py-2.5 bg-white text-gray-500 border border-gray-200 rounded-2xl hover:bg-gray-50 transition text-xs font-black uppercase tracking-widest">
                                ← Back to Roster
                            </a>
                            <button type="submit"
                                class="px-10 py-2.5 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
                <div class="absolute -left-10 -top-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

        </div>
    </div>

    <script>
        function addAvailabilityRow() {
            const container = document.getElementById('availability-rows');
            const div = document.createElement('div');
            div.className = 'flex flex-wrap items-center gap-3 animate-in slide-in-from-top-2 duration-300';
            div.innerHTML = `
                <select name="days[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm w-full sm:w-40 focus:ring-indigo-500">
                    <option value="">-- Day --</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
                <input type="time" name="time_starts[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                <span class="text-[10px] font-black text-gray-400 uppercase">to</span>
                <input type="time" name="time_ends[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()"
                    class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition text-xs font-black uppercase tracking-widest border border-red-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            `;
            container.appendChild(div);
        }
    </script>
</x-app-layout>