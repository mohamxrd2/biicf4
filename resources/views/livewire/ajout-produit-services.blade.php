<div x-data="{ type: @entangle('type') }">
    <div x-data="{ locked: @entangle('locked') }">

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
            <h1 class="mb-8 text-2xl font-bold">Ajouter un produit & Service</h1>

            <form wire:submit.prevent="submit" enctype="multipart/form-data">

                <!-- Sélecteur de catégorie -->
                <div x-data="{ selectedCategories: @entangle('selectedCategories') }">
                    <div class="grid grid-cols-2 gap-6 mb-6">

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


                    <!-- Détails du produit ou du service -->
                    <div x-show="type === 'Produit'" class="mb-6">
                        <h1 class="mb-8 text-xl font-bold text-center">Détails Du Produit</h1>


                        <div class="grid grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Conditionnement</label>
                                <input type="text" wire:model='conditionnement' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('conditionnement')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Format</label>
                                <input type="text" wire:model='format' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('format')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Particularité</label>
                                <input type="text" wire:model='particularite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('particularite')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Origine</label>
                                <select wire:model='origine' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md">
                                    <option>Choisissez une origine</option>
                                    <option>Locale</option>
                                    <option>Importé</option>
                                </select>
                                @error('origine')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Quantité Minimal<span
                                        class="text-red-500 10px">*</span></label>
                                <input type="text" wire:model='qteProd_min'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qteProd_min')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Quantité Maximal<span
                                        class="text-red-500 10px">*</span></label>
                                <input type="text" wire:model='qteProd_max'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('qteProd_max')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-6 mb-6">

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Spécification</label>
                                <input type="text" wire:model='specification' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('specification')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Prix<span
                                        class="text-red-500 10px">*</span></label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div x-show="type === 'Service'" class="mb-6">
                        <h1 class="mb-8 text-xl font-bold text-center">Détails Du Service</h1>
                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Année d'Experience <span
                                        class="text-red-500 10px">*</span></label>
                                <select wire:model='qualification' :disabled="locked"
                                    class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                    <option value="" disabled selected>Expérience dans le domaine</option>
                                    <option value="Moins de 1 an">Moins de 1 an</option>
                                    <option value="De 1 à 5 ans">De 1 à 5 ans</option>
                                    <option value="De 5 à 10 ans">De 5 à 10 ans</option>
                                    <option value="Plus de 10 ans">Plus de 10 ans</option>
                                </select>
                                @error('qualification')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Spécialité</label>
                                <input type="text" wire:model='specialite' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('specialite')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>


                        </div>
                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700">Prestation/Description</label>
                                <input type="text" wire:model='descrip' :disabled="locked"
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    placeholder="Petite Description">
                                @error('descrip')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Quantité</label>
                                <input type="number" wire:model='Quantite'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="">
                                @error('Quantite')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Prix Unitaire<span
                                        class="text-red-500 10px">*</span></label>
                                <input type="text" wire:model='prix'
                                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                                @error('prix')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <h1 class="mb-8 text-xl font-bold text-center">Ajout D'Images</h1>
                    <div class="grid grid-cols-4 mb-6 gap-9">
                        <!-- Image 1 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Image 1 <span class="ml-1 text-red-500">*</span>
                            </label>
                            <input type="file" wire:model="photoProd1"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('photoProd1')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                            @if ($photoProd1 && is_string($photoProd1))
                                <img class="object-cover w-full h-48 mt-5 rounded"
                                    src="{{ asset('post/all/' . $photoProd1) }}">
                                <input type="hidden" name="photo1" value="{{ $photoProd1 }}">
                            @endif
                        </div>

                        <!-- Image 2 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Image 2<span
                                    class="ml-1 text-red-500">*</span></label>
                            <input type="file" wire:model="photoProd2"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('photoProd2')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror

                            @if ($photoProd2 && is_string($photoProd2))
                                <img class="object-cover w-full h-48 mt-5 rounded"
                                    src="{{ asset('post/all/' . $photoProd2) }}">
                                <input type="hidden" name="photo2" value="{{ $photoProd2 }}">
                            @endif
                        </div>

                        <!-- Image 3 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Image 3<span
                                    class="ml-1 text-red-500">*</span></label>
                            <input type="file" wire:model="photoProd3"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('photoProd3')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                            @if ($photoProd3 && is_string($photoProd3))
                                <img class="object-cover w-full h-48 mt-5 rounded"
                                    src="{{ asset('post/all/' . $photoProd3) }}">
                                <input type="hidden" name="photo3" value="{{ $photoProd3 }}">
                            @endif
                        </div>

                        <!-- Image 4 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Image 4<span
                                    class="ml-1 text-red-500">*</span></label>
                            <input type="file" wire:model="photoProd4"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('photoProd4')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                            @if ($photoProd4 && is_string($photoProd4))
                                <img class="object-cover w-full h-48 mt-5 rounded"
                                    src="{{ asset('post/all/' . $photoProd4) }}">
                                <input type="hidden" name="photo4" value="{{ $photoProd4 }}">
                            @endif
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="text-right">
                        <button type="reset" class="p-2 text-white bg-red-500 rounded-md">Annuler</button>
                        <button type="submit" class="p-2 text-white bg-green-500 rounded-md">Enregistrer</button>
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
