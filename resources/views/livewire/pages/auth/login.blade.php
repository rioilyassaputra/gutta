<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div class="text-center pb-4 border-b border-white/10">
        <h2 class="text-xl font-black uppercase tracking-wider text-white">Login Gutta</h2>
        <p class="text-zinc-500 text-xs mt-1">Masuk ke akun pembeli Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-green-500 text-xs font-bold" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input wire:model="form.email" id="email" class="block w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <x-input-label for="password" value="Password" class="mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-zinc-500 hover:text-white transition-colors" href="{{ route('password.request') }}" wire:navigate>
                        Lupa password?
                    </a>
                @endif
            </div>
            <x-text-input wire:model="form.password" id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
            <input wire:model="form.remember" id="remember" type="checkbox" class="text-violet-600 focus:ring-violet-500 bg-zinc-900 border-white/20" name="remember">
            <label for="remember" class="text-xs text-zinc-400 cursor-pointer select-none">Ingat saya di perangkat ini</label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-white/10">
            <a class="text-xs text-zinc-400 hover:text-white underline transition-colors" href="{{ route('register') }}" wire:navigate>
                Daftar akun baru
            </a>

            <button type="submit" class="btn-primary py-2 px-6 text-xs">
                Masuk Akun
            </button>
        </div>
    </form>
</div>
