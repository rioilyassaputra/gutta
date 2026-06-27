<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Component;

class CategoryManager extends Component
{
    public string $search = '';
    
    // Form fields
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public bool $isEditing = false;
    public ?int $editingCategoryId = null;

    // Delete confirmation
    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        $ignoreId = $this->isEditing ? $this->editingCategoryId : 'NULL';
        return [
            'name' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:categories,slug,{$ignoreId},id,deleted_at,NULL",
            'description' => 'nullable|string|max:500',
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nama Kategori',
        'slug' => 'Slug Kategori',
        'description' => 'Deskripsi',
    ];

    public function updatedName($value): void
    {
        if (!$this->isEditing) {
            $this->slug = Str::slug($value);
        }
    }

    public function saveCategory(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->name);
        $validatedData = $this->validate();

        if ($this->isEditing) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update($validatedData);
            $this->dispatch('notify', message: "Kategori '{$category->name}' berhasil diperbarui.", type: 'success');
        } else {
            $category = Category::create($validatedData);
            $this->dispatch('notify', message: "Kategori '{$category->name}' berhasil ditambahkan.", type: 'success');
        }

        $this->resetForm();
    }

    public function editCategory(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->isEditing = true;
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            $this->dispatch(
                'notify', 
                message: "Tidak dapat menghapus. Kategori '{$category->name}' masih memiliki {$productCount} produk. Pindahkan produk terlebih dahulu.", 
                type: 'error'
            );
            return;
        }

        $this->confirmingDeleteId = $id;
    }

    public function deleteCategory(): void
    {
        if (!$this->confirmingDeleteId) {
            return;
        }

        $category = Category::findOrFail($this->confirmingDeleteId);
        $category->delete();

        $this->dispatch('notify', message: "Kategori '{$category->name}' berhasil dihapus.", type: 'success');
        $this->confirmingDeleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'slug', 'description', 'isEditing', 'editingCategoryId', 'confirmingDeleteId']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Category::withCount('products')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        $categories = $query->get();

        return view('livewire.admin.category-manager', compact('categories'));
    }
}
