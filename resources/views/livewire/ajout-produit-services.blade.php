<div x-data="{ type: @entangle('type') }">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-8">Ajouter un produit & Service</h1>

        <form wire:submit.prevent="submit" enctype="multipart/form-data">
            <!-- Sélection du type -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entrez le nom de la catégorie</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Entrez le nom de la catégorie">
                    <select multiple class="w-full p-2 border border-gray-300 rounded-md mt-2">
                        @foreach ($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->categorie_produit_services }}</option>
                        @endforeach
                    </select>


                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entrez le nom du produit</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Entrez le nom du produit">

                    <select multiple class="w-full p-2 border border-gray-300 rounded-md mt-2">
                        <option value="alimentaire">Banane plantain</option>
                        <!-- Ajoutez d'autres options ici si nécessaire -->
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Référence -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center space-x-2">
                        Référence (
                        <span>Générer</span>
                        <input wire:click="toggleGenerateReference" type="checkbox"
                            class="w-4 h-4 border border-gray-300 rounded-md" />
                        )
                    </label>
                    <input type="text" wire:model="reference" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici..." readonly>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select x-model="type" wire:model='type' class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Choisissez votre type</option>
                        <option value="Produit">Produit</option>
                        <option value="Service">Service</option>
                    </select>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" wire:model='name' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
            </div>

            <!-- Détails du produit ou du service -->
            <div x-show="type === 'Produit'" class="mb-6">
                <h1 class="text-center text-xl font-bold mb-8">Détails Du Produit</h1>


                <div class="grid grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conditionnement</label>
                        <input type="text" wire:model='conditionnement'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                        <input type="text" wire:model='format' class="w-full p-2 border border-gray-300 rounded-md"
                            placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Particularité</label>
                        <input type="text" wire:model='particularite'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Origine</label>
                        <select wire:model='origine' class="w-full p-2 border border-gray-300 rounded-md">
                            <option>Choisissez une origine</option>
                            <option>Locale</option>
                            <option>Importé</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité Maximal</label>
                        <input type="text" wire:model='qteProd_max'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité Minimal</label>
                        <input type="text" wire:model='qteProd_min'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Spécification</label>
                        <input type="text" wire:model='specification'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix</label>
                        <input type="text" wire:model='prix' class="w-full p-2 border border-gray-300 rounded-md"
                            placeholder="Tapez ici...">
                    </div>
                </div>
            </div>

            <div x-show="type === 'Service'" class="mb-6">
                <h1 class="text-center text-xl font-bold mb-8">Détails Du Service</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Qualification</label>
                        <select wire:model='qualification'
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                            <option value="" disabled selected>Expérience dans le domaine</option>
                            <option value="Moins de 1 an">Moins de 1 an</option>
                            <option value="De 1 à 5 ans">De 1 à 5 ans</option>
                            <option value="De 5 à 10 ans">De 5 à 10 ans</option>
                            <option value="Plus de 10 ans">Plus de 10 ans</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Spécialité</label>
                        <input type="text" wire:model='specialite'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de personnel</label>
                        <input type="text" wire:model='qte_service'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix Unitaire</label>
                        <input type="text" wire:model='prix' class="w-full p-2 border border-gray-300 rounded-md"
                            placeholder="Tapez ici...">
                    </div>
                </div>
            </div>

            <!-- Localisation (commune aux deux types) -->
            <h1 class="text-center text-xl font-bold mb-8">Localisation Du Produit & Service</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                    <input type="text" wire:model='depart' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ville/Sous-Prefecture</label>
                    <input type="text" wire:model='ville' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Localité</label>
                    <input type="text" wire:model='commune' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
            </div>

            <!-- Images -->
            <h1 class="text-center text-xl font-bold mb-8">Ajout D'Image</h1>
            {{-- <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
            </div> --}}

            <!-- Boutons d'action -->
            <div class="text-right">
                <button type="reset" class="bg-red-500 text-white p-2 rounded-md">Annuler</button>
                <button type="submit" class="bg-green-500 text-white p-2 rounded-md">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
