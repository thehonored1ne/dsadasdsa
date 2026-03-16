<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(!$teacherProfile)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    No profile found. Please contact your Program Chair.
                </div>
            @else

            <form method="POST" action="{{ route('teacher.profile.update') }}">
                @csrf
                @method('PATCH')

                {{-- Basic Info --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Profile Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 text-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="text" value="{{ Auth::user()->email }}" disabled
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 text-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expertise Areas</label>
                        <input type="text" name="expertise_areas" value="{{ $teacherProfile->expertise_areas }}"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                            placeholder="e.g. Mathematics, Programming, Database">
                        <p class="text-xs text-gray-400 mt-1">Separate multiple areas with a comma</p>
                        @error('expertise_areas')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Units</label>
                        <input type="number" name="max_units" value="{{ $teacherProfile->max_units }}"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                            min="1" max="30">
                        @error('max_units')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Availability --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800">Availability Schedule</h3>
                    <p class="text-sm text-gray-500">Set the days and times you are available to teach.</p>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Day</th>
                                <th class="px-4 py-3 text-left">Time Start</th>
                                <th class="px-4 py-3 text-left">Time End</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($teacherProfile->availabilities as $index => $availability)
                            <tr>
                                <td class="px-4 py-2">
                                    <select name="days[]" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}" {{ $availability->day === $day ? 'selected' : '' }}>
                                                {{ $day }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="time_starts[]" value="{{ $availability->time_start }}"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="time_ends[]" value="{{ $availability->time_end }}"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-400 text-sm">
                                    No availability set.
                                </td>
                            </tr>
                            @endforelse

                            {{-- Extra empty row to add new availability --}}
                            <tr>
                                <td class="px-4 py-2">
                                    <select name="days[]" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                        <option value="">-- Select Day --</option>
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="time_starts[]"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="time_ends[]"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Save Changes
                    </button>
                </div>

            </form>
            @endif

        </div>
    </div>
</x-app-layout>