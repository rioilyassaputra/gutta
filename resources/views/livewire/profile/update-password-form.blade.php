<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-6">
        <h2 class="text-lg font-black uppercase tracking-wider text-violet-400">
            {{ __('Ubah Kata Sandi') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-400">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang kuat untuk menjaga keamanan.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <label for="update_password_current_password" class="label-gutta">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
            <input wire:model="current_password" id="update_password_current_password" type="password" class="input-gutta" autocomplete="current-password" />
            @error('current_password')
                <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="label-gutta">Kata Sandi Baru <span class="text-red-500">*</span></label>
            <input wire:model="password" id="update_password_password" type="password" class="input-gutta" autocomplete="new-password" />
            @error('password')
                <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="label-gutta">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" type="password" class="input-gutta" autocomplete="new-password" />
            @error('password_confirmation')
                <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary">
                {{ __('Perbarui Kata Sandi') }}
            </button>

            <span x-data="{ show: false }" x-on:password-updated.window="show = true; setTimeout(() => show = false, 3000)" x-show="show" x-cloak class="text-xs font-bold text-green-400 flex items-center gap-1">
                ✓ {{ __('Kata sandi berhasil diperbarui.') }}
            </span>
        </div>
    </form>
</section>
