<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Upload Files') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Data Import Center</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">System Ready</span>
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

            @if(session('upload_error'))
                <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="text-sm">
                        <strong class="font-black">Upload Error:</strong> {{ session('upload_error') }}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8">
                {{-- Dynamic Section Generator --}}
                @php
                    $uploadSections = [
                        [
                            'title' => 'Upload Teachers',
                            'route' => 'chair.upload.teachers',
                            'input_name' => 'teachers_csv',
                            'type' => 'teachers',
                            'format_desc' => 'Columns: name, email, expertise_areas, max_units, available_days, time_start, time_end',
                            'note' => 'Use pipe | to separate multiple values. Do NOT use commas inside fields.'
                        ],
                        [
                            'title' => 'Upload Subjects',
                            'route' => 'chair.upload.subjects',
                            'input_name' => 'subjects_csv',
                            'type' => 'subjects',
                            'format_desc' => 'Columns: code, name, units, prerequisites',
                            'note' => 'Prerequisites: comma separated subject codes, or leave empty.'
                        ],
                        [
                            'title' => 'Upload Schedules',
                            'route' => 'chair.upload.schedules',
                            'input_name' => 'schedules_csv',
                            'type' => 'schedules',
                            'format_desc' => 'Columns: day, time_start, time_end, room',
                            'note' => 'Day must be exact: Monday, Tuesday, Wednesday, etc.'
                        ]
                    ];
                @endphp

                @foreach($uploadSections as $section)
                <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                {{ $section['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-2 font-medium">
                                Format: <span class="bg-gray-100 px-2 py-0.5 rounded text-indigo-700 font-mono text-xs">CSV or Excel (.xlsx)</span>. 
                                <span class="block mt-1 text-gray-500 italic">{{ $section['format_desc'] }}</span>
                            </p>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-3">{{ $section['note'] }}</p>
                        </div>
                        
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('chair.templates.download', ['type' => $section['type'], 'format' => 'csv']) }}"
                                class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100 transition text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                CSV Template
                            </a>
                            <a href="{{ route('chair.templates.download', ['type' => $section['type'], 'format' => 'excel']) }}"
                                class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-100 transition text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                Excel Template
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route($section['route']) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="group relative flex flex-col md:flex-row items-center gap-4 p-4 border-2 border-dashed border-gray-200 rounded-2xl hover:border-indigo-300 hover:bg-indigo-50/30 transition-all">
                            <input type="file" 
                                   name="{{ $section['input_name'] }}" 
                                   accept=".csv,.xlsx,.xls" 
                                   class="flex-grow text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                            
                            <button type="submit" class="w-full md:w-auto px-8 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                                Import Data
                            </button>
                        </div>
                        @error($section['input_name'])
                            <p class="text-xs text-red-600 font-bold mt-3 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </form>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>