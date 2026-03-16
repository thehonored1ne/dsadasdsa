<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight leading-none">
                    {{ __('Notifications') }}
                </h2>
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2 italic">Updates & Alerts</p>
            </div>
            <div class="hidden sm:flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Inbox Live</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Status Notification --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Action Header --}}
            <div class="flex justify-between items-end px-2">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Recent Alerts</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">You have {{ $notifications->where('is_read', false)->count() }} unread messages</p>
                </div>
                <form method="POST" action="{{ route('teacher.notifications.read.all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-5 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition text-[10px] font-black uppercase tracking-widest shadow-sm">
                        Mark All as Read
                    </button>
                </form>
            </div>

            {{-- Notifications List --}}
            <div class="space-y-4">
                @forelse($notifications as $notification)
                <div class="group relative bg-white border {{ $notification->is_read ? 'border-gray-100' : 'border-indigo-100' }} rounded-3xl p-6 shadow-sm transition hover:shadow-md">
                    
                    {{-- Status Indicator Bar --}}
                    <div class="absolute left-0 top-6 bottom-6 w-1 rounded-r-full {{ $notification->is_read ? 'bg-gray-100' : 'bg-indigo-600' }}"></div>

                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-black text-gray-900 text-sm tracking-tight leading-none">
                                    {{ $notification->title }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="px-2 py-0.5 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">New</span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-xs font-medium mt-2 leading-relaxed">{{ $notification->message }}</p>
                            <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>

                        @if(!$notification->is_read)
                        <form method="POST" action="{{ route('teacher.notifications.read', $notification->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white border border-dashed border-gray-200 rounded-3xl p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Your inbox is empty</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>