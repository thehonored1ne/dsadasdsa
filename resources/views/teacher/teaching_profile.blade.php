<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Teaching Profile') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Expertise & Availability Settings</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Configuration Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Success Notification --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if(!$teacherProfile)
                <div class="bg-amber-50 border border-amber-100 text-amber-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm font-bold">No teaching profile found. Please contact your Program Chair.</span>
                </div>
            @else

            <form method="POST" action="{{ route('teacher.teaching.profile.update') }}" class="space-y-8">
                @csrf
                @method('PATCH')

                {{-- Expertise and Max Units Card --}}
                <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                            Expertise & Load
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Expertise Areas</label>
                                <input type="text" name="expertise_areas" value="{{ $teacherProfile->expertise_areas }}"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g. Programming|Mathematics|Database">
                                <p class="text-[9px] text-gray-400 font-bold mt-2 uppercase tracking-tighter italic">Separate multiple areas with the pipe | symbol</p>
                                @error('expertise_areas') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Maximum Semester Units</label>
                                <input type="number" name="max_units" value="{{ $teacherProfile->max_units }}" min="1" max="30"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[9px] text-gray-400 font-bold mt-2 uppercase tracking-tighter italic">Recommended range: 12 - 24 units</p>
                                @error('max_units') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
                </div>

                {{-- Availability Card --}}
                <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 tracking-tight leading-none">Availability Schedule</h3>
                            <p class="text-[10px] text-gray-500 font-bold uppercase mt-2">Specify your teaching windows</p>
                        </div>
                        <button type="button" onclick="addRow()"
                            class="px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl hover:bg-indigo-100 transition text-[10px] font-black uppercase tracking-widest">
                            + Add New Slot
                        </button>
                    </div>

                    <div class="p-8">
                        <div id="availability-rows" class="space-y-4">
                            @forelse($teacherProfile->availabilities as $index => $availability)
                            <div class="flex flex-wrap items-center gap-4 p-4 bg-gray-50/50 border border-gray-100 rounded-2xl">
                                <div class="flex-grow min-w-[150px]">
                                    <select name="days[]" class="w-full bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-indigo-500">
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}" {{ $availability->day === $day ? 'selected' : '' }}>{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="time" name="time_starts[]" value="{{ $availability->time_start }}" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-indigo-500">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">to</span>
                                    <input type="time" name="time_ends[]" value="{{ $availability->time_end }}" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-indigo-500">
                                </div>
                                <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-400 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            @empty
                            <div class="text-center py-8 border-2 border-dashed border-gray-100 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No availability slots defined</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Container for JS-added rows --}}
                        <div id="new-rows" class="space-y-4"></div>
                    </div>

                    <div class="p-8 bg-gray-50/30 border-t border-gray-50 flex justify-end">
                        <button type="submit"
                            class="px-10 py-3 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                            Save Profile Changes
                        </button>
                    </div>
                </div>
            </form>
            @endif

        </div>
    </div>

    <script>
        function addRow() {
            const container = document.getElementById('new-rows');
            const div = document.createElement('div');
            div.className = 'flex flex-wrap items-center gap-4 p-4 bg-indigo-50/30 border border-indigo-100 rounded-2xl mt-4 animate-in slide-in-from-top-2 duration-300';
            div.innerHTML = `
                <div class="flex-grow min-w-[150px]">
                    <select name="days[]" class="w-full bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-indigo-500">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input type="time" name="time_starts[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-indigo-500">
                    <span class="text-[10px] font-black text-gray-400 uppercase">to</span>
                    <input type="time" name="time_ends[]" class="bg-white border-gray-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-indigo-500">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-400 hover:text-red-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            `;
            container.appendChild(div);
        }
    </script>
</x-app-layout>