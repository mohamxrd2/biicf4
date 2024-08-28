<div wire:poll.15000ms>
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
        <div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-md mb-4">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
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
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 1</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle2"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 2</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 3</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="zone" class="block text-sm font-medium text-gray-700">Zone D'Activité :</label>
                    <select id="zone" wire:model="zone"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                    <option value="" selected>choisissez votre votre zone </option>
                    <option value="proximite" selected>Proximité</option>
                    <option value="locale">Locale</option>
                    <option value="nationale">Nationale</option>
                    <option value="sous_regionale">Sous-Regionale</option>
                    <option value="continentale">Continentale</option>
                </select>
                @error('zone')
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
                            <option value="">Sélectionnez une Sous-Régions</option>
                            @foreach ($sousregions as $sousregion)
                                <option value="{{ $sousregion }}">{{ $sousregion }}</option>
                            @endforeach
                        </select>
                        @error('sous_region')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div x-data="{ selectedCountry: @entangle('pays') }" class="col-span-1">
                        <label for="country" class="block text-sm font-semibold text-gray-800 mb-2">Pays<span
                                class="text-red-500 10px">*</span></label>
                        <select id="country" x-model="selectedCountry" wire:model="pays"
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="" disabled selected>Choisissez un pays</option>
                            <template x-for="country in @js($countries)" :key="country">
                                <option :value="country" x-text="country"></option>
                            </template>
                        </select>
                        @error('pays')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                        <input type="text" wire:model='depart'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
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
                        <input type="text" wire:model='localite'
                            class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                        @error('commune')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <!-- Pieces d'identité -->
                <h1 class="text-center text-xl font-bold mb-8">Inscrivez Vos Pieces </h1>

                <div>

                    <label for="identity" class="block text-sm font-medium text-gray-700">Piece d'identité
                        recto:</label>
                    <input type="file" id="identity" wire:model="identity"
                        class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('identity')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="permis" class="block text-sm font-medium text-gray-700">Piece d'identité
                        verso:</label>
                    <input type="file" id="permis" wire:model="permis"
                        class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('permis')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance Multirisques
                        Professionnels:</label>
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
        </div>


    @endif


    <div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-md">

        <h2 class="text-xl text-center font-semibold mb-4">POSTULER POUR ETRE PSAP</h2>

        <form wire:submit.prevent="submitPsap">

            <div class="mb-8">
                <label for="experience" class="block text-sm font-medium text-gray-700">Expérience:</label>
                <select id="experience" wire:model="experience"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" selected>Selectionnez vos années d'expérience</option>
                    <option value="moins de 1 an">Moins de 1 an</option>
                    <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                    <option value="5 ans et plus">5 ans et plus</option>
                </select>
                @error('experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Localisation -->
            <h1 class="text-center text-xl font-bold mb-8">Inscrivez votre Localisation </h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="col-span-1">
                    <label for="continent" class="block text-sm font-medium text-gray-700 mb-2">Continent:</label>
                    <select id="continent" wire:model="selectedContinent"
                        class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez un continent</option>
                        @foreach ($continents as $continent)
                            <option value="{{ $continent }}">{{ $continent }}</option>
                        @endforeach
                    </select>
                    @error('selectedContinent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label for="sous_region" class="block text-sm font-medium text-gray-700 mb-2">Sous-Région:</label>
                    <select id="sous_region" wire:model="selectedSous_region"
                        class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez une Sous-Région</option>
                        @foreach ($sousregions as $sousregion)
                            <option value="{{ $sousregion }}">{{ $sousregion }}</option>
                        @endforeach
                    </select>
                    @error('selectedSous_region') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label for="country" class="block text-sm font-semibold text-gray-800 mb-2">Pays<span
                            class="text-red-500">*</span></label>
                    <select id="country" wire:model="pays"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled>Choisissez un pays</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('pays') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label for="depart" class="block text-sm font-medium text-gray-700 mb-2">Département:</label>
                    <input type="text" id="depart" wire:model="depart"
                        class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    @error('depart') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">Ville/Sous-Préfecture:</label>
                    <input type="text" id="ville" wire:model="ville"
                        class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    @error('ville') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1">
                    <label for="localite" class="block text-sm font-medium text-gray-700 mb-2">Localité:</label>
                    <input type="text" id="localite" wire:model="localite"
                        class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tapez ici...">
                    @error('localite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pièces d'identité -->
            <h1 class="text-center text-xl font-bold mb-8">Inscrivez Vos Pièces</h1>

            <div>
                <label for="identity" class="block text-sm font-medium text-gray-700">Pièce d'identité recto:</label>
                <input type="file" id="identity" wire:model="identity"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('identity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="permis" class="block text-sm font-medium text-gray-700">Pièce d'identité verso:</label>
                <input type="file" id="permis" wire:model="permis"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('permis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance Multirisques Professionnels:</label>
                <input type="file" id="assurance" wire:model="assurance"
                    class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                @error('assurance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Soumettre la candidature
            </button>
            <button type="reset"
                class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                Annuler
            </button>

        </form>





    </div>


</div>
