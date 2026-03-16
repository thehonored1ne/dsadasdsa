<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-black text-gray-900 tracking-tighter">Welcome Back</h2>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.2em] mt-2">Access your portal</p>
    </div>

    <x-auth-session-status class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-2xl text-xs font-black uppercase tracking-widest" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label for="email" class="block text-[10px] font-black text-gray-600 uppercase tracking-widest ml-1">Email Address</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                   placeholder="name@institution.edu">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold text-red-600 uppercase tracking-tight" />
        </div>

        <div class="space-y-2">
            <div class="flex justify-between items-center px-1">
                <label for="password" class="block text-[10px] font-black text-gray-600 uppercase tracking-widest">Security Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 transition" href="{{ route('password.request') }}">
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="current-password"
                   class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-red-600 uppercase tracking-tight" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4" name="remember">
                <span class="ms-2 text-[10px] font-black text-gray-500 uppercase tracking-widest group-hover:text-gray-700 transition">{{ __('Keep me signed in') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center py-4 px-6 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition-all shadow-xl shadow-gray-200 text-xs font-black uppercase tracking-[0.2em]">
                {{ __('Secure Log In') }}
                <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </form>
</x-guest-layout>