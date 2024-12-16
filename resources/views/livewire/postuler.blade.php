<div wire:poll.100ms>
    @if ($livraison)
        <div class="p-4 mb-6 ">
            <h2 class="mb-4 text-xl font-semibold text-center text-gray-700">Suivi de la demande pour etre livreur</h2>
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
                            <span class="font-semibold text-gray-600 text-md">En cours...</span>
                            <span class="font-semibold text-gray-600 text-md">Reponse</span>
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
        <div class="max-w-4xl p-8 mx-auto mb-4 bg-white rounded-md shadow-md">

            @if ($errors->any())
                <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                    <strong class="font-bold">Attention!</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li class="list-disc list-inside">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="mb-4 text-xl font-semibold text-center">POSTULER POUR ETRE UN LIVREUR</h2>

            <form wire:submit.prevent="submit" class="space-y-6">

                <div>
                    <label for="experience" class="block text-sm font-medium text-gray-700">Expérience:</label>
                    <select id="experience" wire:model="experience"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>Selectionnez vos années d'experience</option>
                        <option value="moins de 1 an" selected>Moins de 1 an</option>
                        <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                        <option value="5 ans et plus">5 ans et plus</option>
                    </select>
                    @error('experience')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>





                <div>
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle" required
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 1</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle2"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 2</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="vehicle" class="block text-sm font-medium text-gray-700">Type d'engin </label>
                    <select id="vehicle" wire:model="vehicle3"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>option 3</option>
                        <option value="Moto" selected>Moto</option>
                        <option value="Fourgonette">Fourgonette</option>
                        <option value="Camion">Camion</option>
                    </select>
                    @error('vehicle')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="zone" class="block text-sm font-medium text-gray-700">Zone D'Activité :</label>
                    <select id="zone" wire:model="zone"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                        <option value="" selected>choisissez votre votre zone </option>
                        <option value="proximite" selected>Proximité</option>
                        <option value="locale">Locale</option>
                        <option value="nationale">Nationale</option>
                        <option value="sous_regionale">Sous-Regionale</option>
                        <option value="continentale">Continentale</option>
                    </select>
                    @error('zone')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>


                <!-- Pieces d'identité -->
                <h1 class="mb-8 text-xl font-bold text-center">Inscrivez Vos Pieces </h1>

                <div>

                    <label for="identity" class="block text-sm font-medium text-gray-700">Piece d'identité
                        recto:</label>
                    <input type="file" id="identity" wire:model="identity"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('identity')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="permis" class="block text-sm font-medium text-gray-700">Piece d'identité
                        verso:</label>
                    <input type="file" id="permis" wire:model="permis"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('permis')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance Multirisques
                        Professionnels:</label>
                    <input type="file" id="assurance" wire:model="assurance"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('assurance')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm  hover:bg-indigo-700">
                    Soumettre la candidature
                </button>
                <button type="reset"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm  hover:bg-red-700">
                    Annuler
                </button>
            </form>
        </div>


    @endif

    @if ($psap)
        <div class="p-4 mb-6 ">
            <h2 class="mb-4 text-xl font-semibold text-center text-gray-700">Suivi de la demande pour etre PSAP</h2>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="w-full h-2 bg-gray-200 rounded-full">
                            <div class="h-2 rounded-full
                    @if ($psap->etat === 'En cours') bg-yellow-400
                    @elseif ($psap->etat === 'Accepté')
                        bg-green-400
                    @else
                        bg-red-400 @endif"
                                style="width:
                    @if ($psap->etat === 'En cours') 50%
                    @elseif ($psap->etat === 'Accepté' || $psap->etat === 'Refusé')
                        100%
                    @else
                        0% @endif;">
                            </div>
                        </div>
                        <div class="absolute top-0 left-0 flex items-center justify-between w-full mt-4">
                            <span class="font-semibold text-gray-600 text-md">En cours...</span>
                            <span class="font-semibold text-gray-600 text-md">Reponse</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div
                        class="flex items-center justify-center w-8 h-8 text-white rounded-full
            @if ($psap->etat === 'En cours') bg-yellow-400
            @elseif ($psap->etat === 'Accepté')
                bg-green-400
            @else
                bg-red-400 @endif">
                        @if ($psap->etat === 'En cours')
                            ⏳
                        @elseif ($psap->etat === 'Accepté')
                            ✅
                        @else
                            ❌
                        @endif
                    </div>
                    <span class="ml-2 font-semibold">
                        @if ($psap->etat === 'En cours')
                            En cours
                        @elseif ($psap->etat === 'Accepté')
                            Accepté
                        @else
                            {{ $psap->etat }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-4xl p-8 mx-auto bg-white rounded-md shadow-md">

            <h2 class="mb-4 text-xl font-semibold text-center">POSTULER POUR ETRE PSAP</h2>

            <form wire:submit.prevent="submitPsap">

                <div class="mb-8">
                    <label for="experience" class="block text-sm font-medium text-gray-700">Expérience:</label>
                    <select id="experience" wire:model="experience"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" selected>Selectionnez vos années d'expérience</option>
                        <option value="moins de 1 an">Moins de 1 an</option>
                        <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                        <option value="5 ans et plus">5 ans et plus</option>
                    </select>
                    @error('experience')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Pièces d'identité -->
                <h1 class="mb-8 text-xl font-bold text-center">Inscrivez Vos Pièces</h1>

                <div>
                    <label for="identity" class="block text-sm font-medium text-gray-700">Pièce d'identité
                        recto:</label>
                    <input type="file" id="identity" wire:model="identity"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('identity')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="permis" class="block text-sm font-medium text-gray-700">Pièce d'identité
                        verso:</label>
                    <input type="file" id="permis" wire:model="permis"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('permis')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance Multirisques
                        Professionnels:</label>
                    <input type="file" id="assurance" wire:model="assurance"
                        class="block w-full mt-1 text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
                    @error('assurance')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    Soumettre la candidature
                </button>
                <button type="reset"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                    Annuler
                </button>

            </form>





        </div>
    @endif




</div>
