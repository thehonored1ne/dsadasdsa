<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assignments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Generate Button --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Auto-Generate Schedule</h3>
                    <p class="text-sm text-gray-500">Matches teachers to subjects by expertise then availability.</p>
                </div>
                <form method="POST" action="{{ route('chair.assignments.generate') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Generate Schedule
                    </button>
                </form>
            </div>

            {{-- Search and Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('chair.assignments') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Teacher or subject name..."
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>

                    {{-- Filter by Rationale --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Rationale</label>
                        <select name="rationale" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">All</option>
                            <option value="expertise_match" {{ request('rationale') === 'expertise_match' ? 'selected' : '' }}>Expertise Match</option>
                            <option value="availability" {{ request('rationale') === 'availability' ? 'selected' : '' }}>Availability</option>
                            <option value="manual_override" {{ request('rationale') === 'manual_override' ? 'selected' : '' }}>Manual Override</option>
                        </select>
                    </div>

                    {{-- Filter by Status --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">All</option>
                            <option value="ok" {{ request('status') === 'ok' ? 'selected' : '' }}>OK</option>
                            <option value="overloaded" {{ request('status') === 'overloaded' ? 'selected' : '' }}>Overloaded</option>
                        </select>
                    </div>

                    {{-- Filter by Day --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Day</label>
                        <select name="day" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">All Days</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                <option value="{{ $day }}" {{ request('day') === $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="sm:col-span-2 lg:col-span-4 flex gap-3">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            Search / Filter
                        </button>
                        <a href="{{ route('chair.assignments') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                            Clear
                        </a>
                    </div>

                </form>
            </div>

            {{-- Results Count --}}
            <div class="text-sm text-gray-500">
                Showing {{ $assignments->count() }} assignment(s)
            </div>

            {{-- Assignments Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Schedule</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Rationale</th>
                            <th class="px-4 py-3">Match Score</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Override</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $assignment->teacherProfile->user->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->subject->name }}</td>
                            <td class="px-4 py-3">
                                {{ $assignment->schedule->day }}
                                {{ $assignment->schedule->time_start }} -
                                {{ $assignment->schedule->time_end }}
                                ({{ $assignment->schedule->room }})
                            </td>
                            <td class="px-4 py-3">{{ $assignment->total_units }}</td>
                            <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $assignment->rationale) }}</td>
                            <td class="px-4 py-3">
                                @if(!is_null($assignment->match_score))
                                    @php $percent = $assignment->match_score * 100; @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded h-2">
                                            <div class="bg-blue-600 h-2 rounded" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold">{{ $percent }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($assignment->is_overloaded)
                                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-medium">Overloaded</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-medium">OK</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('chair.assignments.override') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                    <select name="teacher_profile_id" class="text-sm border border-gray-300 rounded px-2 py-1">
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ $teacher->id === $assignment->teacher_profile_id ? 'selected' : '' }}>
                                                {{ $teacher->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                        Override
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                                No assignments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>