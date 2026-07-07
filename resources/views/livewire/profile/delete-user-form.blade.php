<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-black uppercase tracking-wider text-red-500">
            {{ __('Hapus Akun') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-400">
            {{ __('Setelah akun Anda dihapus, semua data dan riwayat belanja Anda akan dihapus secara permanen.') }}
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn-danger">
        {{ __('Hapus Akun Saya') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-8 bg-zinc-950 border-2 border-red-600 text-white">
            <h2 class="text-lg font-black uppercase tracking-wider text-red-500 mb-2">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="text-xs text-zinc-400 mb-6">
                {{ __('Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun secara permanen.') }}
            </p>

            <div class="mb-6">
                <label for="password" class="label-gutta">Kata Sandi Anda</label>
                <input wire:model="password" id="password" type="password" class="input-gutta" placeholder="Masukkan kata sandi" />
                @error('password')
                    <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                    {{ __('Batal') }}
                </button>
                <button type="submit" class="btn-danger">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
