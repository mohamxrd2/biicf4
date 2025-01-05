<div>
    @if ($showMainlever)
        @include('biicf.components.mainleveClick2')
    @else
        {{-- Afficher les messages de succès --}}
        @if (session('success'))
            <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('error') }}
            </div>
        @endif
        <section>
            <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl w-full bg-white shadow-lg rounded-xl p-8">
                    <!-- En-tête -->
                    <div class="flex items-center justify-between border-b pb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Détails de la commande</h1>
                            <p class="mt-1 text-sm text-gray-500">Commande
                                #{{ $notification->data['code_unique'] ?? $achatdirect->code_unique }}</p>
                        </div>
                        <span class="px-4 py-1 text-sm font-medium {{ $statusClass }} rounded-full">
                            Statut : {{ $statusText }}
                        </span>
                    </div>

                    <!-- Informations principales -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <!-- Informations sur le client -->
                        <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                            <h2 class="text-lg font-semibold text-gray-800">Client</h2>
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Nom : </span>{{ $achatdirect->userSenderI->name }}</p>
                                <p><span class="font-medium">Email : </span>{{ $achatdirect->userSenderI->email }}</p>
                                <p><span class="font-medium">Téléphone : </span>{{ $achatdirect->userSenderI->phone }}
                                </p>
                            </div>
                        </div>
                        @if ($notification->reponse == 'accepter' || $notification->data['type_achat'] == 'Take Away')
                            @if ($notification->data['livreur'])
                                <!-- Informations sur le livreur -->
                                <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                                    <h2 class="text-lg font-semibold text-gray-800">Livreur</h2>
                                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                                        <p><span class="font-medium">Nom : </span>{{ $livreur->name }}</p>
                                        <p><span class="font-medium">Email : </span>{{ $livreur->email }}</p>
                                        <p><span class="font-medium">Téléphone : </span>{{ $livreur->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Informations sur le fournisseur -->
                            <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                                <h2 class="text-lg font-semibold text-gray-800">Fournisseur</h2>
                                <div class="mt-2 text-sm text-gray-600 space-y-1">
                                    <p><span class="font-medium">Nom : </span>{{ $achatdirect->userTraderI->name }}</p>
                                    <p><span class="font-medium">Email : </span>{{ $achatdirect->userTraderI->email }}
                                    </p>
                                    <p><span class="font-medium">Téléphone :
                                        </span>{{ $achatdirect->userTraderI->phone }}
                                    </p>
                                    <p><span class="font-medium">Adresse :
                                        </span>{{ $achatdirect->userTraderI->commune }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Informations sur le produit/service -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800">Produit / Service commandé</h2>
                        <div class="mt-4 flex items-center bg-gray-50 p-4 rounded-lg shadow-inner">
                            <img src="{{ $achatdirect->photoProd ? asset('post/all/' . $achatdirect->photoProd) : asset('img/noimg.jpeg') }}"
                                alt="Image produit" class="w-20 h-20 object-cover rounded-lg">
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">Produit : {{ $produit->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">Quantité commandée:
                                    {{ $achatdirect->quantité }}({{ $produit->condProd }})</p>
                                <p class="mt-1 text-sm text-gray-600">Prix unitaire : {{ $produit->prix }} FCFA</p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($achatdirect->montantTotal, 2, ',', '.') }} FCFA
                            </div>
                        </div>
                    </div>

                    <!-- Résumé de la commande -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800">Résumé de la commande du
                            {{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</h2>
                        <div class="mt-4 bg-gray-50 p-4 rounded-lg shadow-inner space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Sous-total :</span>
                                <span>{{ number_format($achatdirect->montantTotal, 2, ',', '.') }}
                                    FCFA</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Frais de livraison :</span>
                                <span>{{ isset($notification->data['prixTrade']) ? number_format($notification->data['prixTrade'], 2, ',', '.') : 'N/A' }}
                                    FCFA</span>

                            </div>
                            <div class="flex justify-between font-medium text-gray-900">
                                <span>Total :</span>
                                <span>{{ number_format($achatdirect->montantTotal + $notification->data['prixTrade'], 2, ',', '.') }}
                                    FCFA</span>
                            </div>
                        </div>
                    </div>


                    <!-- Boutons -->
                    <div class="mt-8 flex justify-end space-x-4">
                        @if ($notification->reponse == 'accepter')
                            <div class="flex justify-center items-center mt-4">
                                <button wire:click="toggleComponent"
                                    class="flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                    </svg>
                                    <span>Procéder à la main levée</span>
                                </button>
                            </div>
                        @elseif ($notification->reponse == 'refuser')
                            <div class="flex space-x-2 mt-4">
                                <div class="bg-gray-400 text-white px-4 py-2 rounded-lg relative">
                                    <!-- Texte du bouton et icône -->
                                    La notification de refus a été envoyée avec succès au Fournisseur et Livreur.
                                </div>

                            </div>
                        @elseif($notification->reponse == 'mainleveclient')
                            <div class="flex space-x-2 mt-4">
                                <div class="bg-gray-400 text-white px-4 py-2 rounded-lg relative">
                                    <!-- Texte du bouton et icône -->
                                    Proceder a la recuperation de votre commande / Envoi de notification aux four et
                                    livreur

                                </div>

                            </div>
                        @else
                            {{-- Bouton Refuser --}}
                            <div class="flex space-x-2">
                                <button wire:click.prevent='FactureRefuser'
                                    wire:loading.attr="disabled"
                                    @class([
                                        'px-4 py-2 text-sm font-medium rounded-lg shadow-md transition-colors',
                                        'opacity-50 cursor-not-allowed' => $isLoading,
                                        'text-gray-700 bg-gray-100 hover:bg-gray-200' => !$isLoading,
                                    ])
                                    {{ $isLoading ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="FactureRefuser">
                                        Refuser la facture
                                    </span>
                                    <span wire:loading wire:target="FactureRefuser" class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Refus en cours...
                                    </span>
                                </button>

                                {{-- Bouton Accepter --}}
                                <button data-modal-target="popup-modal"
                                    data-modal-toggle="popup-modal"
                                    @class([
                                        'px-4 py-2 text-sm font-medium rounded-lg shadow-md transition-colors',
                                        'opacity-50 cursor-not-allowed' => $isLoading,
                                        'text-white bg-blue-600 hover:bg-blue-700' => !$isLoading,
                                    ])
                                    {{ $isLoading ? 'disabled' : '' }}>
                                    Accepter la facture
                                </button>
                            </div>

                            <!-- Popup de confirmation -->
                            <div id="popup-modal" tabindex="-1"
                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative p-4 w-full max-w-md max-h-full">
                                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                        <!-- Bouton pour fermer la modal -->
                                        <button type="button"
                                            class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="popup-modal">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                            </svg>
                                            <span class="sr-only">Fermer la modal</span>
                                        </button>

                                        <!-- Contenu de la modal -->
                                        <div class="p-4 md:p-5 text-center">
                                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                                En acceptant cette commande, je comprends que les informations du
                                                fournisseur me seront transmises.
                                                Je m'engage à entrer en contact avec le fournisseur dans les
                                                meilleurs délais pour confirmer la
                                                commande et coordonner les étapes nécessaires à sa réalisation.
                                            </h3>
                                            <!-- Bouton de confirmation -->
                                            <button data-modal-hide="popup-modal" type="button"
                                                wire:click.prevent="valider"
                                                wire:loading.attr="disabled"
                                                @class([
                                                    'text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center',
                                                    'opacity-50 cursor-not-allowed' => $isLoading,
                                                ])
                                                {{ $isLoading ? 'disabled' : '' }}>
                                                <span wire:loading.remove wire:target="valider">
                                                    Oui, je suis sûr
                                                </span>
                                                <span wire:loading wire:target="valider" class="inline-flex items-center">
                                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Validation en cours...
                                                </span>
                                            </button>
                                            <!-- Bouton d'annulation -->
                                            <button data-modal-hide="popup-modal" type="button"
                                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                                Non, annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
            </div>

        </section>
    @endif

</div>
