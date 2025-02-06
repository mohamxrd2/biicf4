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
            <h1 class="text-2xl font-bold mb-8">Ajouter Une Consommation</h1>

            <form wire:submit.prevent="submit">

                <!-- Sélecteur de catégorie -->
                <div x-data="{ selectedCategories: @entangle('selectedCategories') }">
                    <!-- Include the new component with props -->
                    <x-product-category-form :categories="$categories" :produits="$produits" />


                    <!-- Détails du produit ou du service -->
                    <div x-show="type === 'Produit'" class="mb-6">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                                <input type="text" wire:model='format' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('format')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Origine</label>
                                <select wire:model='origine' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md">
                                    <option>Choisissez une origine</option>
                                    <option>Locale</option>
                                    <option>Importé</option>
                                </select>
                                @error('origine')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité<span class="text-red-500">*</span></label>
                                <input type="text" wire:model='qteProd'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qteProd')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Périodicité
                                    <span class="text-red-500">*</span></label>
                                <select wire:model='periodicite' class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Choisissez une périodicité</option>
                                    <option value="jour">Par Jour</option>
                                    <option value="semaine">Par Semaine</option>
                                    <option value="mois">Par Mois</option>
                                    <option value="trimestre">Par Trimestre</option>
                                    <option value="semestre">Par Semestre</option>
                                    <option value="annee">Par An</option>
                                </select>
                                @error('periodicite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="grid grid-cols-2 gap-6 mb-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prix<span class="text-red-500">*</span></label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div x-show="type === 'Service'" class="mb-6">
                        <h1 class="text-center text-xl font-bold mb-8">Détails Du Service</h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prestation/Description</label>
                                <input type="text" wire:model='descrip' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    placeholder="Petite Description">
                                @error('descrip')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité<span class="text-red-500">*</span></label>
                                <input type="number" wire:model='Quantite'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="">
                                @error('Quantite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Périodicité<span class="text-red-500">*</span></label>
                                <select wire:model='periodicite' class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Choisissez une périodicité</option>
                                    <option value="jour">Par Jour</option>
                                    <option value="semaine">Par Semaine</option>
                                    <option value="mois">Par Mois</option>
                                    <option value="trimestre">Par Trimestre</option>
                                    <option value="semestre">Par Semestre</option>
                                    <option value="annee">Par An</option>
                                </select>
                                @error('periodicite')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prix Unitaire<span
                                        class="text-red-500 10px">*</span></label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
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
