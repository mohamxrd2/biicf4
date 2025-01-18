<div x-data="{ type: @entangle('type') }">
    <div x-data="{ locked: @entangle('locked'), conditionnement: '', Duree: '' }">
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-white bg-green-500 rounded-md">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-white bg-red-500 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-md">
            <h1 class="mb-8 text-2xl font-bold">Ajouter un produit / Service</h1>

            <form wire:submit.prevent="submit" enctype="multipart/form-data">

                <!-- Sélecteur de catégorie -->
                <div x-data="{ selectedCategories: @entangle('selectedCategories') }">
                    <div class="col-span-2  grid lg:grid-cols-2 sm:grid-cols-2   gap-6 mb-6">

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Entrez le nom de la
                                catégorie<span class="text-red-500 10px">*</span></label>
                            <input type="text" wire:model.debounce.30ms="categorie" :disabled="locked"
                                class="w-full p-2 border border-gray-300 rounded-md"
                                placeholder="Entrez le nom de la catégorie">
                            @error('categorie')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror

                            <select multiple x-model="selectedCategories"
                                class="w-full p-2 mt-2 border border-gray-300 rounded-md" @change="updateProducts()">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->categorie_produit_services }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-data="{ selectedProduits: @entangle('selectedProduits') }">

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Recherchez votre produit ou
                                    service</label>
                                <input type="text" wire:model.live="searchTerm"
                                    class="w-full p-2 border border-gray-300 rounded-md"
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
                            <input type="text" wire:model="reference"
                                class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici..."
                                readonly>
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


                    <!-- Détails du produit -->
                    <div x-show="type === 'Produit'" class="mb-6">
                        <x-produit-form />

                    </div>

                    <!-- Détails du  service -->
                    <div x-show="type === 'Service'" class="mb-6">
                        <x-service-form />

                    </div>

                    <!-- Images -->
                    <h1 class="mb-8 text-xl font-bold text-center">Ajout D'Images</h1>
                    <x-image-upload :photos="['photoProd1', 'photoProd2', 'photoProd3', 'photoProd4']" />

                    <!-- Boutons d'action -->
                    <div class="text-right">
                        <button type="reset" class="p-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                            Annuler
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                            class="p-2 text-white bg-green-500 rounded-md hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submit">
                                Enregistrer
                            </span>
                            <span wire:loading wire:target="submit">
                                <svg class="inline w-4 h-4 mr-2 animate-spin" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Traitement en cours...
                            </span>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateProducts() {
        @this.call('updateProducts', Array.from(document.querySelector('select[x-model="selectedCategories"]')
            .selectedOptions).map(option => option.value));
    }

</script>
<script>
    document.addEventListener('livewire:navigated', function() {
        const selectElement = document.getElementById('product-select');

        selectElement.addEventListener('change', function() {
            // Récupère les IDs des options sélectionnées
            const selectedProductIds = Array.from(selectElement.selectedOptions).map(option => option
                .value);

            if (selectedProductIds.length > 0) {
                // Assurez-vous qu'il y a un seul produit sélectionné
                const selectedProductId = selectedProductIds[0];

                // Appel de la méthode Livewire avec l'ID du produit sélectionné
                @this.call('updateProductDetails', selectedProductId)
                    .then(() => {

                        // Affiche un message de confirmation dans la console après l'appel réussi
                        console.log('Product details updated successfully.');
                    })
                    .catch(error => {
                        // Affiche une erreur dans la console si l'appel échoue
                        console.error('Error updating product details:', error);
                    });

            }
        });
    });


</script>
