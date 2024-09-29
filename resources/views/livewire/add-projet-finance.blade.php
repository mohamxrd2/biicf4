<div>
    {{-- In work, do what you enjoy. --}}

    <div class="max-w-lg mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajouter un projet</h2>
        @if ($successMessage)
            <div class="bg-green-500 text-white p-4 rounded-md mb-6">
                {{ $successMessage }}
            </div>
        @endif

        <form wire:submit.prevent="submit" class="space-y-6">
            <!-- Montant -->
            <div class="relative">
                <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                <input wire:model="montant" type="number" id="montant"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                    placeholder="Entrez le montant">
                @error('montant')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Taux -->
            <div class="relative">
                <label for="taux" class="block text-sm font-medium text-gray-700">Taux</label>
                <input wire:model="taux" type="number" id="taux"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                    placeholder="Entrez le taux">
                @error('taux')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="relative">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" id="description"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                    placeholder="Entrez la description"></textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Catégorie -->
            {{-- <div class="relative">
                <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <input wire:model="categorie" type="text" id="categorie"
                       class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                       placeholder="Entrez la catégorie">
                @error('categorie') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div> --}}

            <div>
                <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <select id="categorie" wire:model="categorie" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="" disabled>Sélectionnez une catégorie</option>
                    <option value="Technologie et Innovation">Technologie et Innovation</option>
                    <option value="Travaux Créatifs">Travaux Créatifs</option>
                    <option value="Projets Communautaires">Projets Communautaires</option>
                    <option value="Éducation">Éducation</option>
                    <option value="Environnement">Environnement</option>
                    <option value="Culture">Culture</option>
                    <option value="Santé et Bien-être">Santé et Bien-être</option>
                    <option value="Art">Art</option>
                    <option value="Musique">Musique</option>
                    <option value="Film">Film</option>
                    <option value="Photographie">Photographie</option>
                    <option value="Écriture et Publication">Écriture et Publication</option>
                    <option value="Jeux Vidéo">Jeux Vidéo</option>
                    <option value="Comédie et Théâtre">Comédie et Théâtre</option>
                    <option value="Droits Humains">Droits Humains</option>
                    <option value="Petites Entreprises">Petites Entreprises</option>
                    <option value="Autres Projets Communautaires">Autres Projets Communautaires</option>
                    <!-- Ajoutez d'autres catégories ici -->
                </select>
                @error('categorie')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>


            <!-- Type de Financement -->
            <div class="relative">
                <label for="type_financement" class="block text-sm font-medium text-gray-700">Type de
                    financement</label>
                <select wire:model="type_financement" id="type_financement"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200">
                    <option value="">Sélectionnez le type de financement</option>
                    <option value="direct">Direct</option>
                    <option value="groupé">Groupé</option>
                </select>
                @error('type_financement')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button with Loading -->
            <div class="flex justify-center">
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md inline-flex items-center space-x-2 transition ease-in-out duration-200"
                    @if ($isSubmitting) disabled @endif>

                    <span>{{ $isSubmitting ? 'En cours...' : 'Soumettre' }}</span>

                    <span wire:loading wire:target="submit" class="animate-spin">
                        <div class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-gray-400 rounded-full"
                            role="status" aria-label="loading">
                        </div>
                    </span>
                </button>
            </div>
        </form>
    </div>


</div>
