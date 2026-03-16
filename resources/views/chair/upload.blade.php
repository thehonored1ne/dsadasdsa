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

            {{-- Error Message --}}
            @if(session('upload_error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Upload Error:</strong> {{ session('upload_error') }}
                </div>
            @endif

            {{-- Upload Teachers --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Teachers</h3>
                            <p class="text-sm text-gray-500 mb-1">
                                Format: CSV or Excel (.xlsx). Columns: <code>name, email, expertise_areas, max_units, available_days, time_start, time_end</code>
                            </p>
                            <p class="text-xs text-gray-400">Use pipe | to separate multiple values. Do NOT use commas inside fields.</p>
                        </div>
                        <div class="flex gap-2 shrink-0 ml-4">
                            <a href="{{ route('chair.templates.download', ['type' => 'teachers', 'format' => 'csv']) }}"
                                class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs">
                                ⬇️ CSV Template
                            </a>
                            <a href="{{ route('chair.templates.download', ['type' => 'teachers', 'format' => 'excel']) }}"
                                class="px-3 py-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 text-xs">
                                ⬇️ Excel Template
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('chair.upload.teachers') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="teachers_csv" accept=".csv,.xlsx,.xls" class="text-sm text-gray-600">
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
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Subjects</h3>
                            <p class="text-sm text-gray-500 mb-1">
                                Format: CSV or Excel (.xlsx). Columns: <code>code, name, units, prerequisites</code>
                            </p>
                            <p class="text-xs text-gray-400">Prerequisites: comma separated subject codes, or leave empty.</p>
                        </div>
                        <div class="flex gap-2 shrink-0 ml-4">
                            <a href="{{ route('chair.templates.download', ['type' => 'subjects', 'format' => 'csv']) }}"
                                class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs">
                                ⬇️ CSV Template
                            </a>
                            <a href="{{ route('chair.templates.download', ['type' => 'subjects', 'format' => 'excel']) }}"
                                class="px-3 py-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 text-xs">
                                ⬇️ Excel Template
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('chair.upload.subjects') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="subjects_csv" accept=".csv,.xlsx,.xls" class="text-sm text-gray-600">
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
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Schedules</h3>
                            <p class="text-sm text-gray-500 mb-1">
                                Format: CSV or Excel (.xlsx). Columns: <code>day, time_start, time_end, room</code>
                            </p>
                            <p class="text-xs text-gray-400">Day must be exact: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday.</p>
                        </div>
                        <div class="flex gap-2 shrink-0 ml-4">
                            <a href="{{ route('chair.templates.download', ['type' => 'schedules', 'format' => 'csv']) }}"
                                class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs">
                                ⬇️ CSV Template
                            </a>
                            <a href="{{ route('chair.templates.download', ['type' => 'schedules', 'format' => 'excel']) }}"
                                class="px-3 py-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 text-xs">
                                ⬇️ Excel Template
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('chair.upload.schedules') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="schedules_csv" accept=".csv,.xlsx,.xls" class="text-sm text-gray-600">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                Upload
                            </button>
                        </div>
                        @error('schedules_csv')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>