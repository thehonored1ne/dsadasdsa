<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Files') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Upload Teachers --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Teachers</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        CSV columns: <code>name, email, expertise_areas, max_units, available_days, time_start, time_end</code>
                    </p>
                    <form method="POST" action="{{ route('chair.upload.teachers') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="teachers_csv" accept=".csv" class="text-sm text-gray-600">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                Upload
                            </button>
                        </div>
                        @error('teachers_csv')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

            {{-- Upload Subjects --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Subjects</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        CSV columns: <code>code, name, units, prerequisites</code>
                    </p>
                    <form method="POST" action="{{ route('chair.upload.subjects') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="subjects_csv" accept=".csv" class="text-sm text-gray-600">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                Upload
                            </button>
                        </div>
                        @error('subjects_csv')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

            {{-- Upload Schedules --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Schedules</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        CSV columns: <code>day, time_start, time_end, room</code>
                    </p>
                    <form method="POST" action="{{ route('chair.upload.schedules') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="schedules_csv" accept=".csv" class="text-sm text-gray-600">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                Upload
                            </button>
                        </div>
                        @error('schedules_csv')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

