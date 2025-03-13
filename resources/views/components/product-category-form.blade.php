<!-- resources/views/components/form-section.blade.php -->
@props(['categories', 'produits'])

<div class="col-span-2 grid lg:grid-cols-2 sm:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-700">
            Entrez le nom de la catégorie<span class="text-red-500 10px">*</span>
        </label>
        <input type="text" wire:model.debounce.500ms="categorie" class="w-full p-2 border border-gray-300 rounded-md"
            placeholder="Entrez le nom de la catégorie">

        @error('categorie')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror

        <select multiple x-data="{ selectedCategories: [] }" x-model="selectedCategories"
            class="w-full p-2 mt-2 border border-gray-300 rounded-md" @change="updateProducts()">
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->categorie_produit_services }}</option>
            @endforeach
        </select>
    </div>

    <div x-data="{ selectedProduits: @entangle('selectedProduits') }">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Recherchez votre produit ou service
            </label>
            <input type="text" wire:model.live="searchTerm" class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="Entrez le nom du produit">

            <select id="product-select" multiple x-model="selectedProduits"
                class="w-full p-2 mt-2 border border-gray-300 rounded-md">
                @foreach ($produits as $produit)
                    <option value="{{ $produit->id }}">{{ $produit->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
    <!-- Référence -->
    <div class="col-span-1 md:col-span-1">
        <label class="flex items-center block mb-2 space-x-2 text-sm font-medium text-gray-700">
            Référence (
            <span>Générer</span>
            <input wire:click="toggleGenerateReference" type="checkbox" :disabled="locked"
                class="w-4 h-4 border border-gray-300 rounded-md" />
            )<span class="text-red-500 10px">*</span>
        </label>
        <input type="text" wire:model="reference" class="w-full p-2 border border-gray-300 rounded-md"
            placeholder="Tapez ici..." readonly>
        @error('reference')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <!-- Type -->
    <div class="col-span-1">
        <label class="block mb-2 text-sm font-medium text-gray-700">Type</label>
        <select x-model="type" wire:model='type' :disabled="locked"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value="">Choisissez votre type</option>
            <option value="Produit">Produit</option>
            <option value="Service">Service</option>
        </select>
        @error('type')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <!-- Nom -->
    <div class="col-span-1 md:col-span-1">
        <label class="block mb-2 text-sm font-medium text-gray-700">Nom</label>
        <input type="text" wire:model='name' id="product-name" :disabled="locked"
            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici..." />
        @error('name')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    </div>
</div>
