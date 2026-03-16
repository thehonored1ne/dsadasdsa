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
                                No assignments yet. Click Generate Schedule.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>