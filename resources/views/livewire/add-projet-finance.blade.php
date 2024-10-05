<div>
    {{-- In work, do what you enjoy. --}}

    <div class="max-w-lg mx-auto p-6 bg-white shadow-lg rounded-lg">
        @if ($successMessage)
            <div class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">
                {{ $successMessage }}
            </div>
        @endif

        @if ($errors->has('submitError'))
            <div class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">
                {{ $errors->first('submitError') }}
            </div>
        @endif
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajouter un projet</h2>


        <form wire:submit.prevent="submit" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom du projet</label>
                <input type="text" wire:model="name" id="name"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                    placeholder="Entrez le nom du projet">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
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
                <label for="taux" class="block text-sm font-medium text-gray-700">Taux(%)</label>
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


            <div>
                <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <select id="categorie" wire:model="categorie" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="" disabled>Sélectionnez une catégorie</option>
                    <!-- Catégories Principales -->
                    <option value="Technologie et Innovation" selected>Technologie et Innovation</option>
                    <option value="Travaux Créatifs">Travaux Créatifs</option>
                    <option value="Projets Communautaires">Projets Communautaires</option>

                    <!-- Sous-catégories Technologiques -->
                    <option value="Technologie Audio">Technologie Audio</option>
                    <option value="Technologie Vestimentaire">Technologie Vestimentaire</option>
                    <option value="Énergie et Tech Verte">Énergie et Tech Verte</option>
                    <option value="Télécommunications et Accessoires">Télécommunications et Accessoires</option>
                    <option value="Produits Innovants">Produits Innovants</option>

                    <!-- Sous-catégories Éducation & Formation -->
                    <option value="Éducation">Éducation</option>
                    <option value="Formations en Ligne">Formations en Ligne</option>
                    <option value="Programmes Académiques">Programmes Académiques</option>

                    <!-- Sous-catégories Santé et Bien-être -->
                    <option value="Santé et Bien-être">Santé et Bien-être</option>
                    <option value="Nutrition et Alimentation">Nutrition et Alimentation</option>
                    <option value="Fitness et Entraînement">Fitness et Entraînement</option>

                    <!-- Sous-catégories Environnement -->
                    <option value="Environnement">Environnement</option>
                    <option value="Projets Écologiques">Projets Écologiques</option>
                    <option value="Conservation des Ressources">Conservation des Ressources</option>

                    <!-- Sous-catégories Culture et Art -->
                    <option value="Culture">Culture</option>
                    <option value="Art">Art</option>
                    <option value="Musique">Musique</option>
                    <option value="Film">Film</option>
                    <option value="Photographie">Photographie</option>
                    <option value="Comédie et Théâtre">Comédie et Théâtre</option>
                    <option value="Écriture et Publication">Écriture et Publication</option>
                    <option value="Jeux Vidéo">Jeux Vidéo</option>
                    <option value="Webséries et TV">Webséries et TV</option>
                    <option value="Podcasts et Blogs">Podcasts et Blogs</option>

                    <!-- Sous-catégories Projets Communautaires -->
                    <option value="Projets Communautaires">Projets Communautaires</option>
                    <option value="Droits Humains">Droits Humains</option>
                    <option value="Petites Entreprises">Petites Entreprises</option>
                    <option value="Activités Sociales">Activités Sociales</option>
                    <option value="Actions Humanitaires">Actions Humanitaires</option>
                    <option value="Autres Projets Communautaires">Autres Projets Communautaires</option>

                    <!-- Sous-catégories Entrepreneuriat -->
                    <option value="Entrepreneuriat">Entrepreneuriat</option>
                    <option value="Startups Technologiques">Startups Technologiques</option>
                    <option value="Commerce Électronique">Commerce Électronique</option>

                    <!-- Autres Catégories -->
                    <option value="Voyage et Aventure">Voyage et Aventure</option>
                    <option value="Mode et Accessoires">Mode et Accessoires</option>
                    <option value="Nourriture et Boissons">Nourriture et Boissons</option>
                    <option value="Transport et Mobilité">Transport et Mobilité</option>
                    <option value="Immobilier">Immobilier</option>
                    <option value="Sports et Loisirs">Sports et Loisirs</option>
                    <option value="Autres">Autres</option>
                </select>
                @error('categorie')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Photo 1 -->

                <div class="relative">
                    <label for="photo1" class="block text-sm font-medium text-gray-700">Image 1</label>
                    @if (!$photo1)
                        <!-- Zone de téléchargement stylisée -->
                        <label for="photo1"
                            class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                        </label>
                    @else
                        <!-- Affichage de la photo et bouton de suppression -->
                        <div class="relative">
                            <img src="{{ $photo1->temporaryUrl() }}" alt="Preview Photo 1"
                                class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                            <button wire:click="$set('photo1', null)" type="button"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <input wire:model="photo1" type="file" id="photo1" class="hidden" accept="image/*">
                    @error('photo1')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Photo 2 -->
                <div class="relative">
                    <label for="photo2" class="block text-sm font-medium text-gray-700">Image 2</label>
                    @if (!$photo2)
                        <label for="photo2"
                            class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                        </label>
                    @else
                        <div class="relative">
                            <img src="{{ $photo2->temporaryUrl() }}" alt="Preview Photo 2"
                                class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                            <button wire:click="$set('photo2', null)" type="button"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <input wire:model="photo2" type="file" id="photo2" class="hidden" accept="image/*">
                    @error('photo2')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Photo 3 -->
                <div class="relative">
                    <label for="photo3" class="block text-sm font-medium text-gray-700">Image 3</label>

                    @if (!$photo3)
                        <!-- Zone de téléchargement stylisée (visible si aucune photo n'est téléchargée) -->
                        <label for="photo3"
                            class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>

                            <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                        </label>
                    @else
                        <!-- Affichage de la photo et bouton de suppression (visible si une photo est téléchargée) -->
                        <div class="relative">
                            <img src="{{ $photo3->temporaryUrl() }}" alt="Preview Photo 3"
                                class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                            <!-- Bouton de suppression -->
                            <button wire:click="$set('photo3', null)" type="button"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Input de type fichier caché -->
                    <input wire:model="photo3" type="file" id="photo3" class="hidden" accept="image/*">

                    @error('photo3')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>



                <!-- Photo 4 -->
                <div class="relative">
                    <label for="photo4" class="block text-sm font-medium text-gray-700">Image 4</label>
                    @if (!$photo4)
                        <label for="photo4"
                            class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                        </label>
                    @else
                        <div class="relative">
                            <img src="{{ $photo4->temporaryUrl() }}" alt="Preview Photo 4"
                                class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                            <button wire:click="$set('photo4', null)" type="button"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <input wire:model="photo4" type="file" id="photo4" class="hidden" accept="image/*">
                    @error('photo4')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Photo 5 -->
                <div class="relative">
                    <label for="photo5" class="block text-sm font-medium text-gray-700">Image 5</label>
                    @if (!$photo5)
                        <label for="photo5"
                            class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                        </label>
                    @else
                        <div class="relative">
                            <img src="{{ $photo5->temporaryUrl() }}" alt="Preview Photo 5"
                                class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                            <button wire:click="$set('photo5', null)" type="button"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <input wire:model="photo5" type="file" id="photo5" class="hidden" accept="image/*">
                    @error('photo5')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="relative">
                <label for="durer" class="block text-sm font-medium text-gray-700">Date limite</label>
                <input wire:model="durer" type="date" id="durer" min="{{ now()->addDay()->toDateString() }}"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                @error('durer')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>




            <!-- Type de Financement -->
            <div class="relative">
                <label for="type_financement" class="block text-sm font-medium text-gray-700">Type de
                    financement</label>
                <select wire:model="type_financement" id="type_financement"
                    class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200">
                    <option value="" disabled>Sélectionnez le type de financement</option>
                    <option value="direct" selected>Direct</option>
                    <option value="groupé">Groupé</option>
                </select>
                @error('type_financement')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button with Loading -->
            <div class="flex justify-center mt-6">
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md inline-flex items-center space-x-2 transition ease-in-out duration-200 mr-3"
                    @if ($isSubmitting) disabled @endif>
                    <span>{{ $isSubmitting ? 'En cours...' : 'Soumettre' }}</span>
                    <span wire:loading wire:target="submit" class="animate-spin">
                        <div class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-gray-400 rounded-full"
                            role="status" aria-label="loading"></div>
                    </span>
                </button>

                <button type="button"
                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none"
                    wire:click="resetForm">
                    Annuler
                </button>
            </div>
        </form>
    </div>


</div>
