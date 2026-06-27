<?php

namespace App\Livewire\Dashboard;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Livewire\Component;

class AddressBook extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public string $label = '';
    public string $recipient_name = '';
    public string $phone = '';
    public int $province_id = 0;
    public string $province_name = '';
    public int $city_id = 0;
    public string $city_name = '';
    public string $district = '';
    public string $postal_code = '';
    public string $full_address = '';
    public bool $is_default = false;

    public array $provinces = [];
    public array $cities = [];

    public function mount(RajaOngkirService $rajaOngkir): void
    {
        $this->provinces = $rajaOngkir->getProvinces();
    }

    public function updatedProvinceId(RajaOngkirService $rajaOngkir): void
    {
        $this->cities = $rajaOngkir->getCities($this->province_id);
        $this->city_id = 0;
        $this->city_name = '';
        
        $province = collect($this->provinces)->firstWhere('province_id', (string) $this->province_id);
        $this->province_name = $province ? $province['province'] : '';
    }

    public function updatedCityId(): void
    {
        $city = collect($this->cities)->firstWhere('city_id', (string) $this->city_id);
        $this->city_name = $city ? trim(($city['type'] ?? '') . ' ' . ($city['city_name'] ?? '')) : '';
    }

    public function showAddForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function editAddress(int $id, RajaOngkirService $rajaOngkir): void
    {
        $address = Address::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $id;
        $this->label = $address->label;
        $this->recipient_name = $address->recipient_name;
        $this->phone = $address->phone;
        $this->province_id = $address->province_id;
        $this->province_name = $address->province_name;
        $this->cities = $rajaOngkir->getCities($address->province_id);
        $this->city_id = $address->city_id;
        $this->city_name = $address->city_name;
        $this->district = $address->district;
        $this->postal_code = $address->postal_code;
        $this->full_address = $address->full_address;
        $this->is_default = $address->is_default;
        $this->showForm = true;
    }

    public function saveAddress(): void
    {
        $this->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'province_id'    => 'required|integer|min:1',
            'city_id'        => 'required|integer|min:1',
            'district'       => 'required|string|max:100',
            'postal_code'    => 'required|string|max:10',
            'full_address'   => 'required|string',
        ]);

        $user = auth()->user();

        // Max 5 addresses
        if (! $this->editingId && $user->addresses()->count() >= 5) {
            $this->dispatch('notify', message: 'Maksimal 5 alamat per akun.', type: 'error');
            return;
        }

        $data = [
            'label'          => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone'          => $this->phone,
            'province_id'    => $this->province_id,
            'province_name'  => $this->province_name,
            'city_id'        => $this->city_id,
            'city_name'      => $this->city_name,
            'district'       => $this->district,
            'postal_code'    => $this->postal_code,
            'full_address'   => $this->full_address,
            'is_default'     => $this->is_default,
        ];

        if ($this->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        if ($this->editingId) {
            Address::where('user_id', $user->id)->where('id', $this->editingId)->update($data);
        } else {
            $user->addresses()->create($data);
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('notify', message: 'Alamat berhasil disimpan.', type: 'success');
    }

    public function setDefault(int $id): void
    {
        $user = auth()->user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
    }

    public function deleteAddress(int $id): void
    {
        Address::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->dispatch('notify', message: 'Alamat berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->label = '';
        $this->recipient_name = '';
        $this->phone = '';
        $this->province_id = 0;
        $this->province_name = '';
        $this->city_id = 0;
        $this->city_name = '';
        $this->district = '';
        $this->postal_code = '';
        $this->full_address = '';
        $this->is_default = false;
        $this->cities = [];
    }

    public function render()
    {
        $addresses = auth()->user()->addresses()->withoutTrashed()->get();
        return view('livewire.dashboard.address-book', compact('addresses'));
    }
}
