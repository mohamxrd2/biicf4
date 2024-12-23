<div>
    @if ($showMainlever)
        @include('biicf.components.mainlevePayment')
    @else

        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">

            {{-- Section Estimation --}}
            <div
                class="max-w-4xl p-8 mx-auto mb-6 bg-white rounded-2xl shadow-xl transform hover:scale-[1.01] transition-all duration-300">
                <h2
                    class="mb-4 text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Estimation de reception du colis</h2>
                <p class="text-lg flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Date : <span
                            class="font-semibold text-indigo-700">{{ \Carbon\Carbon::parse($notification->data['date_livr'])->translatedFormat('d F Y') }}
                            (Heure: {{ $notification->data['time'] }} )</span></span>
                </p>
            </div>

            {{-- Section Vérification --}}
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    {{-- En-tête --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Vérification Code Livreur</h1>
                        <p class="mt-2 text-gray-600">Entrez le code du livreur pour vérifier sa validité</p>
                    </div>

                    {{-- Formulaire --}}
                    <form wire:submit.prevent="verifyCode" class="space-y-6">
                        <div class="relative">
                            <input type="text" name="code_verif" wire:model.defer="code_verif"
                                placeholder="Entrez le code à 4 chiffres"
                                class="w-full pl-4 pr-10 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                maxlength="4" required />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Bouton de validation --}}
                        <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Vérifier le code</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Vérification...
                            </span>
                        </button>

                        @error('code_verif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>

                    {{-- Instructions --}}
                    <div class="mt-8 bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Instructions
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Le code livreur doit contenir 4 chiffres
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Vérifiez que le code correspond à votre bon de livraison
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                En cas de problème, contactez le support
                            </li>
                        </ul>
                    </div>

                    {{-- Messages de notification --}}
                    @if (session()->has('succes'))
                        <button wire:click="toggleComponent"
                            class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-700">{{ session('succes') }}</span>
                        </button>
                    @endif

                    @if (session()->has('error'))
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-red-700">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section Information Livreur (conditionnelle) --}}
            @if (session()->has('succes'))
                <div class="max-w-2xl mx-auto mt-8 bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Information sur le livreur
                        </h2>

                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 ring-4 ring-blue-50">
                                    <img src="{{ asset($livreur->photo) }}" alt="Photo du livreur"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>

                            <div class="flex-1 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-500">Nom du client</p>
                                        <p class="font-semibold text-gray-900">{{ $livreur->name }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-500">Contact</p>
                                        <p class="font-semibold text-gray-900">{{ $livreur->phone }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-500">Adresse</p>
                                        <p class="font-semibold text-gray-900">{{ $livreur->commune }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-500">Produit à récupérer</p>
                                        <p class="font-semibold text-gray-900">{{ $achatdirect->nameProd }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
