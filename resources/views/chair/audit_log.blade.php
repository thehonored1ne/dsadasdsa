<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Audit Log') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">System Activity & History</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Security Monitoring</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Activity Overview --}}
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6 overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Assignment History</h3>
                    <p class="text-sm text-gray-600 mt-1 font-medium max-w-md">Trace all automated generations and manual overrides performed by program chairs.</p>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Timestamp</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Administrator</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Action Type</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Activity Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                            <tr class="group hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-xs font-bold text-gray-600">{{ $log->created_at->format('M d, Y') }}</span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase">{{ $log->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-[10px] font-black border border-indigo-100">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-black text-gray-900 leading-none">{{ $log->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($log->action === 'generated')
                                        <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                                            Generated
                                        </span>
                                    @elseif($log->action === 'overridden')
                                        <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-100">
                                            Override
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-gray-100">
                                            {{ $log->action }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs text-gray-600 leading-relaxed font-medium">
                                        @if($log->action === 'generated')
                                            Success: Processed <span class="font-black text-gray-900">{{ $log->details['total_assignments'] ?? 0 }}</span> subject assignments.
                                        @elseif($log->action === 'overridden')
                                            Reassigned <span class="text-indigo-600 font-bold">{{ $log->details['subject'] ?? 'N/A' }}</span>:
                                            <span class="inline-flex items-center gap-1 opacity-70">
                                                {{ $log->details['from_teacher'] ?? 'N/A' }} 
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="2.5"/></svg>
                                                {{ $log->details['to_teacher'] ?? 'N/A' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-medium">No activity logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Custom Pagination Styling --}}
                @if($logs->hasPages())
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>