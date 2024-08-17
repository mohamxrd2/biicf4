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
                    <div class="grid grid-cols-2 gap-6 mb-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Entrez le nom de la
                                catégorie</label>
                            <input type="text" wire:model.debounce.30ms="categorie"
                                class="w-full p-2 border border-gray-300 rounded-md"
                                placeholder="Entrez le nom de la catégorie">
                            @error('categorie')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            <select multiple x-model="selectedCategories"
                                class="w-full p-2 border border-gray-300 rounded-md mt-2" @change="updateProducts()">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->categorie_produit_services }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-data="{ selectedProduits: @entangle('selectedProduits') }">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recherchez votre produit ou
                                    service</label>
                                <input type="text" wire:model.live="searchTerm"
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    placeholder="Entrez le nom du produit">

                                <select id="product-select" multiple x-model="selectedProduits"
                                    class="w-full p-2 border border-gray-300 rounded-md mt-2">
                                    @foreach ($produits as $produit)
                                        <option value="{{ $produit->id }}">{{ $produit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Référence -->
                        <div class="col-span-1 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center space-x-2">
                                Référence (
                                <span>Générer</span>
                                <input wire:click="toggleGenerateReference" type="checkbox"
                                    class="w-4 h-4 border border-gray-300 rounded-md" />
                                )
                            </label>
                            <input type="text" wire:model="reference"
                                class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici..."
                                readonly>
                            @error('reference')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select x-model="type" wire:model='type'
                                class="w-full p-2 border border-gray-300 rounded-md">
                                <option value="">Choisissez votre type</option>
                                <option value="Produit">Produit</option>
                                <option value="Service">Service</option>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Particularité</label>
                                <input type="text" wire:model='particularite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('particularite')
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                                <input type="text" wire:model='qteProd'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qteProd')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Périodicité</label>
                                <select wire:model='periodicite' 
                                    class="w-full p-2 border border-gray-300 rounded-md">
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
                        <div class="grid grid-cols-4 gap-6 mb-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spécification</label>
                                <input type="text" wire:model='specification' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('specification')
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

                    <div x-show="type === 'Service'" class="mb-6">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de personnel</label>
                                <input type="text" wire:model='qte_service' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qte_service')
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
