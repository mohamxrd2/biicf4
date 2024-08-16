<div x-data="{ type: @entangle('type') }">
    <div x-data="{ locked: @entangle('locked') }">

        @if (session()->has('message'))
            <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-500 text-white p-4 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-8">Ajouter un produit & Service</h1>

            <form wire:submit.prevent="submit" enctype="multipart/form-data">

                <!-- Sélecteur de catégorie -->
                <div x-data="{ selectedCategories: @entangle('selectedCategories') }">
                    <div class="grid grid-cols-1 gap-6 mb-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">rechercher le nom de la
                                consommation</label>
                            <input type="text" wire:model.debounce.30ms="consommation"
                                class="w-full p-2 border border-gray-300 rounded-md"
                                placeholder="Entrez le nom de la consommation">
                            @error('categorie')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            <select multiple x-model="selectedCategories"
                                class="w-full p-2 border border-gray-300 rounded-md mt-2" @change="updateProducts()">
                                @foreach ($consommations as $cons)
                                    <option value="{{ $cons->id }}">{{ $cons->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">


                        <!-- Type -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select x-model="type" wire:model='type'
                                class="w-full p-2 border border-gray-300 rounded-md">
                                <option value="">Choisissez votre type</option>
                                <option value="produits">Produit</option>
                                <option value="services">Service</option>
                            </select>
                            @error('type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nom -->
                        <div class="col-span-1 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input type="text" wire:model='name' id="product-name" :disabled="locked"
                                class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici..." />
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <!-- Détails du produit ou du service -->
                    <div x-show="type === 'produits'" class="mb-6">
                        <h1 class="text-center text-xl font-bold mb-8">Détails Du Produit</h1>
                        <div class="grid grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Conditionnement</label>
                                <input type="text" wire:model='conditionnement' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('conditionnement')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Particularité</label>
                                <input type="text" wire:model='particularite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('particularite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Periodicité</label>
                                <select wire:model='Periodicite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md">
                                    <option>Choisissez une période</option>
                                    <option>par jour</option>
                                    <option>par semaine</option>
                                    <option>par mois</option>
                                    <option>par trimestre</option>
                                    <option>par semestre</option>
                                    <option>par année</option>
                                </select>

                                @error('Periodicite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                                <input type="text" wire:model='qteProd'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qteProd')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prix</label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div x-show="type === 'services'" class="mb-6">
                        <h1 class="text-center text-xl font-bold mb-8">Détails Du Service</h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Qualification</label>
                                <select wire:model='qualification' :disabled="locked"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                    <option value="" disabled selected>Expérience dans le domaine</option>
                                    <option value="Moins de 1 an">Moins de 1 an</option>
                                    <option value="De 1 à 5 ans">De 1 à 5 ans</option>
                                    <option value="De 5 à 10 ans">De 5 à 10 ans</option>
                                    <option value="Plus de 10 ans">Plus de 10 ans</option>
                                </select>
                                @error('qualification')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spécialité</label>
                                <input type="text" wire:model='specialite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('specialite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prix Unitaire</label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <!-- Localisation (commune aux deux types) -->
                    <h1 class="text-center text-xl font-bold mb-8">Localisation Du Produit & Service</h1>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="col-span-1">
                            <label for="continent"
                                class="block text-sm font-semibold text-gray-800 mb-2">Continent</label>
                            <select id="continent" name="continent" wire:model='selectedContinent'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionnez un continent</option>
                                @foreach ($continents as $continent)
                                    <option value="{{ $continent }}">{{ $continent }}</option>
                                @endforeach
                            </select>
                            @error('continent')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="sousregion"
                                class="block text-sm font-semibold text-gray-800 mb-2">Sous-Régions</label>
                            <select id="sousregion" name="continent" wire:model='selectedSous_region'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionnez une sous-région</option>
                                @foreach ($sousregions as $sousregion)
                                    <option value="{{ $sousregion }}">{{ $sousregion }}</option>
                                @endforeach
                            </select>
                            @error('sous_region')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="country" class="block text-sm font-semibold text-gray-800 mb-2">Pays</label>
                            <select name="country" id="country" wire:model='pays'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Choisissez un pays</option>
                                <!-- Options added dynamically via JS -->
                            </select>
                            @error('country')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="depart"
                                class="block text-sm font-semibold text-gray-800 mb-2">Département</label>
                            <input type="text" id="depart" wire:model='depart'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tapez ici...">
                            @error('depart')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="ville"
                                class="block text-sm font-semibold text-gray-800 mb-2">Ville/Sous-Prefecture</label>
                            <input type="text" id="ville" wire:model='ville'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tapez ici...">
                            @error('ville')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="commune"
                                class="block text-sm font-semibold text-gray-800 mb-2">Localité</label>
                            <input type="text" id="commune" wire:model='commune'
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tapez ici...">
                            @error('commune')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>




                    <!-- Boutons d'action -->
                    <div class="text-right">
                        <button type="reset" class="bg-red-500 text-white p-2 rounded-md">Annuler</button>
                        <button type="submit" class="bg-green-500 text-white p-2 rounded-md">Enregistrer</button>
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
    document.addEventListener('DOMContentLoaded', function() {
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
<script src="{{ asset('js/country.js') }}"></script>
