<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('My Profile') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Faculty Settings & Availability</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Active Profile</span>
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
                    <span class="text-sm font-bold">No profile found. Please contact your Program Chair.</span>
                </div>
            @else

            <form method="POST" action="{{ route('teacher.profile.update') }}" class="space-y-8">
                @csrf
                @method('PATCH')

                {{-- Basic Info Card --}}
                <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-indigo-100">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Personal Information</h3>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">Manage your professional teaching data</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Legal Name</label>
                                <input type="text" value="{{ Auth::user()->name }}" disabled
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Institutional Email</label>
                                <input type="text" value="{{ Auth::user()->email }}" disabled
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Expertise Areas</label>
                                <input type="text" name="expertise_areas" value="{{ $teacherProfile->expertise_areas }}"
                                    class="w-full bg-white border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g. Mathematics, Programming, Database">
                                <p class="text-[9px] text-gray-400 font-bold mt-2 uppercase tracking-tight italic">Separate multiple areas with a comma (,)</p>
                                @error('expertise_areas') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Preferred Max Units</label>
                                <input type="number" name="max_units" value="{{ $teacherProfile->max_units }}" min="1" max="30"
                                    class="w-full bg-white border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                @error('max_units') <p class="text-red-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
                </div>

                {{-- Availability Card --}}
                <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Availability Schedule</h3>
                        <p class="text-xs text-gray-500 font-bold mt-1 uppercase">Define the time slots you are available to teach</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Day of the Week</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Shift Start</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Shift End</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($teacherProfile->availabilities as $index => $availability)
                                <tr class="group hover:bg-gray-50/50 transition">
                                    <td class="px-8 py-4">
                                        <select name="days[]" class="w-full bg-white border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-indigo-500">
                                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <option value="{{ $day }}" {{ $availability->day === $day ? 'selected' : '' }}>{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-8 py-4">
                                        <input type="time" name="time_starts[]" value="{{ $availability->time_start }}"
                                            class="w-full bg-white border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-8 py-4">
                                        <input type="time" name="time_ends[]" value="{{ $availability->time_end }}"
                                            class="w-full bg-white border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-indigo-500">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-6 text-center text-gray-400 italic text-xs font-medium uppercase tracking-widest">
                                        No active availability slots found.
                                    </td>
                                </tr>
                                @endforelse

                                {{-- Add New Row --}}
                                <tr class="bg-indigo-50/30">
                                    <td class="px-8 py-6">
                                        <select name="days[]" class="w-full bg-white border-indigo-100 rounded-xl px-3 py-2 text-xs font-black text-indigo-700 focus:ring-indigo-500">
                                            <option value="">+ Add Day</option>
                                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <option value="{{ $day }}">{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-8 py-6">
                                        <input type="time" name="time_starts[]" class="w-full bg-white border-indigo-100 rounded-xl px-3 py-2 text-xs font-bold text-gray-700">
                                    </td>
                                    <td class="px-8 py-6">
                                        <input type="time" name="time_ends[]" class="w-full bg-white border-indigo-100 rounded-xl px-3 py-2 text-xs font-bold text-gray-700">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
</x-app-layout>