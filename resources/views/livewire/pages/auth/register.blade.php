<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $whatsapp = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'whatsapp' => ['required', 'string', 'regex:/^(62|0)[0-9]{9,14}$/', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Convert leading 0 to 62 (Indonesian prefix)
        $whatsapp = $validated['whatsapp'];
        if (str_starts_with($whatsapp, '0')) {
            $whatsapp = '62' . substr($whatsapp, 1);
        }
        $validated['whatsapp'] = $whatsapp;

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'buyer';

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div class="text-center pb-4 border-b border-white/10">
        <h2 class="text-xl font-black uppercase tracking-wider text-white">Buat Akun Gutta</h2>
        <p class="text-zinc-500 text-xs mt-1">Daftar untuk menikmati kemudahan checkout & order tracking.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input wire:model="name" id="name" class="block w-full" type="text" name="name" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input wire:model="email" id="email" class="block w-full" type="email" name="email" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- WhatsApp -->
        <div>
            <x-input-label for="whatsapp" value="Nomor WhatsApp" />
            <x-text-input wire:model="whatsapp" id="whatsapp" class="block w-full" type="text" inputmode="numeric" pattern="[0-9]*" name="whatsapp" required placeholder="081234567890" x-data x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
            <p class="text-[10px] text-zinc-500 mt-1">Gunakan format angka saja (contoh: 081234567890 atau 6281234567890).</p>
            <x-input-error :messages="$errors->get('whatsapp')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="password" id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-white/10">
            <a class="text-xs text-zinc-400 hover:text-white underline transition-colors" href="{{ route('login') }}" wire:navigate>
                Sudah punya akun?
            </a>

            <button type="submit" class="btn-primary py-2 px-6 text-xs">
                Daftar Akun
            </button>
        </div>
    </form>
</div>
