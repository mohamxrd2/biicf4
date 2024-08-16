<div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-md" wire:poll.15000ms>
    @if ($livraison)
        <div class="mb-6 p-4 ">
            <h2 class="text-xl font-semibold text-center text-gray-700 mb-4">Suivi de la demande</h2>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="w-full h-2 bg-gray-200 rounded-full">
                            <div class="h-2 rounded-full
                            @if ($livraison->etat === 'En cours') bg-yellow-400
                            @elseif ($livraison->etat === 'Accepté')
                                bg-green-400
                            @else
                                bg-red-400 @endif"
                                style="width:
                            @if ($livraison->etat === 'En cours') 50%
                            @elseif ($livraison->etat === 'Accepté' || $livraison->etat === 'Refusé')
                                100%
                            @else
                                0% @endif;">
                            </div>
                        </div>
                        <div class="absolute top-0 left-0 flex items-center justify-between w-full mt-4">
                            <span class="text-md font-semibold text-gray-600">En cours...</span>
                            <span class="text-md font-semibold text-gray-600">Reponse</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div
                        class="flex items-center justify-center w-8 h-8 text-white rounded-full
                    @if ($livraison->etat === 'En cours') bg-yellow-400
                    @elseif ($livraison->etat === 'Accepté')
                        bg-green-400
                    @else
                        bg-red-400 @endif">
                        @if ($livraison->etat === 'En cours')
                            ⏳
                        @elseif ($livraison->etat === 'Accepté')
                            ✅
                        @else
                            ❌
                        @endif
                    </div>
                    <span class="ml-2 font-semibold">
                        @if ($livraison->etat === 'En cours')
                            En cours
                        @elseif ($livraison->etat === 'Accepté')
                            Accepté
                        @else
                            {{ $livraison->etat }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @else
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <h2 class="text-xl text-center font-semibold mb-4">POSTULER POUR ETRE UN LIVREUR</h2>

        <form wire:submit.prevent="submit" class="space-y-6">

            <div>
                <label for="experience" class="block text-sm font-medium text-gray-700">Expérience:</label>
                <select id="experience" wire:model="experience"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>Selectionnez vos années d'experience</option>
                    <option value="moins de 1 an" selected>Moins de 1 an</option>
                    <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                    <option value="5 ans et plus">5 ans et plus</option>
                </select>
                @error('experience')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>



            <div>
                <label for="license" class="block text-sm font-medium text-gray-700">Type de permis de
                    conduire:</label>

                <select id="license" wire:model="license"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>Selectionnez votre type de Permis</option>
                    <option value="Permis A" selected>Permis A</option>
                    <option value="Permis B">Permis B</option>
                    <option value="Permis C">Permis C</option>
                    <option value="Permis D">Permis D</option>
                </select>
                @error('license')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="vehicle" class="block text-sm font-medium text-gray-700">Véhicule option 1:</label>
                <select id="vehicle" wire:model="vehicle"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>Type d'engin</option>
                    <option value="Moto" selected>Moto</option>
                    <option value="Fourgonette">Fourgonette</option>
                    <option value="Camion">Camion</option>
                </select>
                @error('vehicle')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="vehicle" class="block text-sm font-medium text-gray-700">Véhicule option 2:</label>
                <select id="vehicle" wire:model="vehicle2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>Type d'engin</option>
                    <option value="Moto" selected>Moto</option>
                    <option value="Fourgonette">Fourgonette</option>
                    <option value="Camion">Camion</option>
                </select>
                @error('vehicle')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="vehicle" class="block text-sm font-medium text-gray-700">Véhicule option 3:</label>
                <select id="vehicle" wire:model="vehicle3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>Type d'engin</option>
                    <option value="Moto" selected>Moto</option>
                    <option value="Fourgonette">Fourgonette</option>
                    <option value="Camion">Camion</option>
                </select>
                @error('vehicle')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="matricule" class="block text-sm font-medium text-gray-700">Matricule du vehicule</label>
                <input id="matricule" type="text" wire:model="matricule" placeholder="Matrucule du vehicule"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('matricule')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="availability" class="block text-sm font-medium text-gray-700">Disponibilités:</label>
                <input type="text" id="availability" wire:model="availability"
                    placeholder="Dites-nous votre disponiblité"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('availability')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Localisation (commune aux deux types) -->
            <h1 class="text-center text-xl font-bold mb-8">Inscrivez votre Localisation </h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="col-span-1">
                    <label for="continent" class="block text-sm font-medium text-gray-700 mb-2">Continent:</label>
                    <select id="continent" name="continent" wire:model='selectedContinent'
                        class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez un continent</option>
                        @foreach ($continents as $continent)
                            <option value="{{ $continent }}">{{ $continent }}</option>
                        @endforeach
                    </select>
                    @error('continent')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sous-Régions</label>

                    <select id="continent" name="continent" wire:model='selectedSous_region'
                        class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez un continent</option>
                        @foreach ($sousregions as $sousregion)
                            <option value="{{ $sousregion }}">{{ $sousregion }}</option>
                        @endforeach
                    </select>
                    @error('sous_region')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                    <input type="text" wire:model='depart' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                    @error('depart')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ville/Sous-Prefecture</label>
                    <input type="text" wire:model='ville' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                    @error('ville')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Localité</label>
                    <input type="text" wire:model='localite' class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                    @error('commune')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <!-- Pieces d'identité -->
            <h1 class="text-center text-xl font-bold mb-8">Inscrivez Vos Pieces </h1>

            <div>

                <label for="identity" class="block text-sm font-medium text-gray-700">Piece d'identité recto:</label>
                <input type="file" id="identity" wire:model="identity"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('identity')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="permis" class="block text-sm font-medium text-gray-700">Piece d'identité verso:</label>
                <input type="file" id="permis" wire:model="permis"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('permis')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance Multirisques Professionnels:</label>
                <input type="file" id="assurance" wire:model="assurance"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('assurance')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class=" py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700  ">
                Soumettre la candidature
            </button>
            <button type="reset"
                class=" py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 ">
                Annuler
            </button>
        </form>

    @endif


</div>
<script src="{{ asset('js/country.js') }}"></script>
