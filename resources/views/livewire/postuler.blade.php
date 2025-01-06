<div class="min-h-screen bg-gray-50 py-8" >
    <div class="max-w-5xl mx-auto space-y-8">
        {{-- En-tête --}}
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">Espace Candidature</h1>
            <p class="mt-2 text-gray-600">Rejoignez notre équipe de professionnels</p>
        </div>

        {{-- Section Livreur --}}
        @if ($livraison)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 text-center mb-8">Suivi de votre candidature Livreur</h2>

                    <div class="relative">
                        {{-- Barre de progression --}}
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div
                                class="h-full transition-all duration-500 rounded-full
                            @if ($livraison->etat === 'En cours') bg-yellow-500 w-1/2
                            @elseif ($livraison->etat === 'Accepté') bg-green-500 w-full
                            @else bg-red-500 w-full @endif">
                            </div>
                        </div>

                        {{-- Étapes --}}
                        <div class="mt-8 flex justify-between">
                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($livraison->etat !== 'Nouveau') border-green-500 bg-green-50 text-green-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">Soumis</span>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($livraison->etat === 'En cours') border-yellow-500 bg-yellow-50 text-yellow-500
                                @elseif ($livraison->etat === 'Accepté' || $livraison->etat === 'Refusé') border-green-500 bg-green-50 text-green-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">En cours</span>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($livraison->etat === 'Accepté') border-green-500 bg-green-50 text-green-500
                                @elseif ($livraison->etat === 'Refusé') border-red-500 bg-red-50 text-red-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="@if ($livraison->etat=== 'Accepté') M5 13l4 4L19 7@else M6 18L18 6M6 6l12 12 @endif" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">
                                    @if ($livraison->etat === 'Accepté')
                                        Accepté
                                    @elseif($livraison->etat === 'Refusé')
                                        Refusé
                                    @else
                                        Décision
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Statut actuel --}}
                        <div
                            class="mt-8 p-4 rounded-lg
                        @if ($livraison->etat === 'En cours') bg-yellow-50 border border-yellow-200
                        @elseif ($livraison->etat === 'Accepté') bg-green-50 border border-green-200
                        @else bg-red-50 border border-red-200 @endif">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5
                                    @if ($livraison->etat === 'En cours') text-yellow-400
                                    @elseif ($livraison->etat === 'Accepté') text-green-400
                                    @else text-red-400 @endif"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3
                                        class="text-sm font-medium
                                    @if ($livraison->etat === 'En cours') text-yellow-800
                                    @elseif ($livraison->etat === 'Accepté') text-green-800
                                    @else text-red-800 @endif">
                                        Statut actuel : {{ $livraison->etat }}
                                    </h3>
                                    <div
                                        class="mt-2 text-sm
                                    @if ($livraison->etat === 'En cours') text-yellow-700
                                    @elseif ($livraison->etat === 'Accepté') text-green-700
                                    @else text-red-700 @endif">
                                        @if ($livraison->etat === 'En cours')
                                            Votre candidature est en cours d'examen. Nous vous contacterons bientôt.
                                        @elseif ($livraison->etat === 'Accepté')
                                            Félicitations ! Votre candidature a été acceptée.
                                        @else
                                            Malheureusement, votre candidature n'a pas été retenue.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-8">
                {{-- En-tête du formulaire --}}
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Devenir Livreur</h2>
                    <p class="mt-2 text-gray-600">Rejoignez notre équipe de livreurs professionnels</p>
                </div>

                {{-- Messages d'erreur globaux --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Plusieurs erreurs ont été détectées :</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-6">
                    {{-- Section Expérience --}}
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Expérience professionnelle</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="experience" class="block text-sm font-medium text-gray-700">
                                    Années d'expérience
                                </label>
                                <select id="experience" wire:model="experience"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 rounded-md @error('experience') border-red-300 @enderror">
                                    <option value="">Sélectionnez votre expérience</option>
                                    <option value="moins de 1 an">Moins de 1 an</option>
                                    <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                                    <option value="5 ans et plus">5 ans et plus</option>
                                </select>
                                @error('experience')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section Véhicules --}}
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Véhicules disponibles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach(['vehicle' => 'Principal', 'vehicle2' => 'Secondaire', 'vehicle3' => 'Additionnel'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">
                                        Véhicule {{ $label }}
                                    </label>
                                    <select id="{{ $field }}" wire:model="{{ $field }}"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 rounded-md @error($field) border-red-300 @enderror">
                                        <option value="">Sélectionnez un type</option>
                                        <option value="Moto">Moto</option>
                                        <option value="Fourgonette">Fourgonnette</option>
                                        <option value="Camion">Camion</option>
                                    </select>
                                    @error($field)
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Section Zone d'activité --}}
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Zone d'activité</h3>
                        <div>
                            <select id="zone" wire:model="zone"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 rounded-md @error('zone') border-red-300 @enderror">
                                <option value="">Sélectionnez votre zone</option>
                                <option value="proximite">Proximité</option>
                                <option value="locale">Locale</option>
                                <option value="nationale">Nationale</option>
                                <option value="sous_regionale">Sous-Régionale</option>
                                <option value="continentale">Continentale</option>
                            </select>
                            @error('zone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Section Documents --}}
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents requis</h3>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach([
                                'identity' => 'Pièce d\'identité (recto)',
                                'permis' => 'Pièce d\'identité (verso)',
                                'assurance' => 'Assurance Multirisques Professionnels'
                            ] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">
                                        {{ $label }}
                                    </label>
                                    <div class="mt-1">
                                        {{-- Zone de dépôt de fichier --}}
                                        <div class="@if(${$field}) hidden @endif">
                                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg @error($field) border-red-300 @enderror">
                                                <div class="space-y-1 text-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="{{ $field }}" class="relative cursor-pointer rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                            <span>Télécharger un fichier</span>
                                                            <input id="{{ $field }}" wire:model="{{ $field }}" type="file" class="sr-only" accept="image/*">
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Prévisualisation de l'image --}}
                                        @if(${$field})
                                            <div class="relative rounded-lg overflow-hidden">
                                                <img src="{{ ${$field}->temporaryUrl() }}" class="w-full h-48 object-cover" alt="Prévisualisation">
                                                <button type="button" wire:click="remove{{ ucfirst($field) }}"
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif

                                        @error($field)
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="resetForm"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Réinitialiser
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                            <span wire:loading.remove>Soumettre la candidature</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Traitement en cours...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Section PSAP --}}
        @if ($psap)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 text-center mb-8">Suivi de votre candidature PSAP</h2>

                    <div class="relative">
                        {{-- Barre de progression --}}
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div
                                class="h-full transition-all duration-500 rounded-full
                            @if ($psap->etat === 'En cours') bg-yellow-500 w-1/2
                            @elseif ($psap->etat === 'Accepté') bg-green-500 w-full
                            @else bg-red-500 w-full @endif">
                            </div>
                        </div>

                        {{-- Étapes --}}
                        <div class="mt-8 flex justify-between">
                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($psap->etat !== 'Nouveau') border-green-500 bg-green-50 text-green-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">Soumis</span>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($psap->etat === 'En cours') border-yellow-500 bg-yellow-50 text-yellow-500
                                @elseif ($psap->etat === 'Accepté' || $psap->etat === 'Refusé') border-green-500 bg-green-50 text-green-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">En cours</span>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div
                                    class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                @if ($psap->etat === 'Accepté') border-green-500 bg-green-50 text-green-500
                                @elseif ($psap->etat === 'Refusé') border-red-500 bg-red-50 text-red-500
                                @else border-gray-300 @endif">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="@if ($psap->etat === 'Accepté') M5 13l4 4L19 7@else M6 18L18 6M6 6l12 12 @endif" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-600">
                                    @if ($psap->etats === 'Accepté')
                                        Accepté
                                    @elseif($psap->etat === 'Refusé')
                                        Refusé
                                    @else
                                        Décision
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Statut actuel --}}
                        <div
                            class="mt-8 p-4 rounded-lg
                        @if ($psap->etat === 'En cours') bg-yellow-50 border border-yellow-200
                        @elseif ($psap->etat === 'Accepté') bg-green-50 border border-green-200
                        @else bg-red-50 border border-red-200 @endif">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5
                                    @if ($psap->etat === 'En cours') text-yellow-400
                                    @elseif ($psap->etat === 'Accepté') text-green-400
                                    @else text-red-400 @endif"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3
                                        class="text-sm font-medium
                                    @if ($psap->etat === 'En cours') text-yellow-800
                                    @elseif ($psap->etat === 'Accepté') text-green-800
                                    @else text-red-800 @endif">
                                        Statut actuel : {{ $psap->etat }}
                                    </h3>
                                    <div
                                        class="mt-2 text-sm
                                    @if ($psap->etat === 'En cours') text-yellow-700
                                    @elseif ($psap->etat === 'Accepté') text-green-700
                                    @else text-red-700 @endif">
                                        @if ($psap->etat === 'En cours')
                                            Votre candidature est en cours d'examen. Nous vous contacterons bientôt.
                                        @elseif ($psap->etat === 'Accepté')
                                            Félicitations ! Votre candidature a été acceptée.
                                        @else
                                            Malheureusement, votre candidature n'a pas été retenue.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8">
                    {{-- En-tête du formulaire --}}
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Devenir PSAP</h2>
                        <p class="mt-2 text-gray-600">Rejoignez notre réseau de prestataires de services</p>
                    </div>

                    <form wire:submit.prevent="submitPsap" class="space-y-6">
                        {{-- Messages d'erreur globaux --}}
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            Veuillez corriger les erreurs suivantes :
                                        </p>
                                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Section Expérience --}}
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Expérience professionnelle</h3>
                            <div>
                                <select id="experience" wire:model="experience"
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 rounded-md @error('experience') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                    <option value="">Sélectionnez votre expérience</option>
                                    <option value="moins de 1 an">Moins de 1 an</option>
                                    <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                                    <option value="5 ans et plus">5 ans et plus</option>
                                </select>
                                @error('experience')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Section Documents --}}
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents requis</h3>
                            <div class="grid grid-cols-1 gap-6">
                                @foreach ([
                                    'identity' => 'Pièce d\'identité (recto)',
                                    'permis' => 'Pièce d\'identité (verso)',
                                    'assurance' => 'Assurance Multirisques Professionnels',
                                ] as $field => $label)
                                    <div>
                                        <label for="{{ $field }}_psap" class="block text-sm font-medium text-gray-700">
                                            {{ $label }}
                                        </label>
                                        <div class="mt-1">
                                            {{-- Zone de dépôt de fichier --}}
                                            <div class="@if(${$field}) hidden @endif">
                                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg @error($field) border-red-300 @enderror">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="{{ $field }}_psap" class="relative cursor-pointer rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                                <span>Télécharger un fichier</span>
                                                                <input id="{{ $field }}_psap" wire:model="{{ $field }}" type="file" class="sr-only" accept="image/*">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Prévisualisation de l'image --}}
                                            @if(${$field})
                                                <div class="relative rounded-lg overflow-hidden">
                                                    <img src="{{ ${$field}->temporaryUrl() }}" class="w-full h-48 object-cover" alt="Prévisualisation">
                                                    <button type="button" wire:click="remove{{ ucfirst($field) }}"
                                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif

                                            @error($field)
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex justify-end space-x-4">
                            <button type="reset" wire:click="resetForm"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Réinitialiser
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed">
                                <span wire:loading.remove>Soumettre la candidature</span>
                                <span wire:loading>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Traitement en cours...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
