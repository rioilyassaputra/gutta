<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header class="mb-6">
        <h2 class="text-lg font-black uppercase tracking-wider text-violet-400">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-400">
            {{ __('Perbarui informasi nama lengkap dan alamat email akun Anda.') }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        <div>
            <label for="name" class="label-gutta">Nama Lengkap <span class="text-red-500">*</span></label>
            <input wire:model="name" id="name" type="text" class="input-gutta" required autofocus autocomplete="name" />
            @error('name')
                <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label-gutta">Alamat Email <span class="text-red-500">*</span></label>
            <input wire:model="email" id="email" type="email" class="input-gutta" required autocomplete="username" />
            @error('email')
                <p class="text-red-400 text-xs mt-1 font-bold">{{ $message }}</p>
            @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-xs text-amber-400">
                        {{ __('Alamat email Anda belum diverifikasi.') }}
                        <button wire:click.prevent="sendVerification" class="underline hover:text-white font-bold ml-1">
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-green-400">
                            {{ __('Link verifikasi baru telah dikirimkan ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary">
                {{ __('Simpan Perubahan') }}
            </button>

            <span x-data="{ show: false }" x-on:profile-updated.window="show = true; setTimeout(() => show = false, 3000)" x-show="show" x-cloak class="text-xs font-bold text-green-400 flex items-center gap-1">
                ✓ {{ __('Profil berhasil diperbarui.') }}
            </span>
        </div>
    </form>
</section>
