<section>
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full bg-white shadow-lg rounded-xl p-8">
            <!-- En-tête -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Commande a livrer</h1>
                    <p class="mt-1 text-sm text-gray-500">Commande #{{ $notification->data['code_unique'] }}
                    </p>
                </div>
                <span class="px-4 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">
                    Statut : en cours
                </span>
            </div>
            <x-offre.alert-messages />

            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                @if ($achatdirect->type_achat === 'OffreGrouper')
                    @foreach ($usersLocations as $userLocation)
                        <div class="p-4 bg-gray-50 rounded-lg shadow-inner mb-4">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-800">Fournisseur {{ $loop->iteration }}</h2>
                                <span
                                    class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">{{ $userLocation->quantite }}
                                    unités</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Nom : </span>{{ $userLocation->user->name }}</p>
                                <p><span class="font-medium">Email : </span>{{ $userLocation->user->email }}</p>
                                <p><span class="font-medium">Téléphone : </span>{{ $userLocation->user->phone }}</p>
                                <div class="flex items-center justify-between mt-4">
                                    <p class="bg-green-100 text-green-700 text-sm rounded-md p-3">
                                        <span class="font-medium">lieu de recuperation :
                                        </span>{{ $userLocation->localite }}
                                    </p>
                                    <button wire:click="sendNotification({{ $userLocation->user_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="sendNotification({{ $userLocation->user_id }})"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <span wire:loading.remove
                                            wire:target="sendNotification({{ $userLocation->user_id }})">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            Aller vers
                                        </span>
                                        <span wire:loading
                                            wire:target="sendNotification({{ $userLocation->user_id }})">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                            Envoi...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                        <h2 class="text-lg font-semibold text-gray-800">Fournisseur</h2>
                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <p><span class="font-medium">Nom : </span>{{ $achatdirect->userTraderI->name }}</p>
                            <p><span class="font-medium">Email : </span>{{ $achatdirect->userTraderI->email }}</p>
                            <p><span class="font-medium">Téléphone : </span>{{ $achatdirect->userTraderI->phone }}</p>
                            <div class="flex items-center justify-between mt-4">
                                <p class="bg-green-100 text-green-700 text-sm rounded-md p-3">
                                    <span class="font-medium">lieu de recuperation :
                                    </span>{{ $achatdirect->userTraderI->commune }}
                                </p>
                                <button wire:click="sendNotification({{ $achatdirect->userTraderI->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <span wire:loading.remove>
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Aller vers
                                    </span>
                                    <span wire:loading>
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Envoi...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informations sur le client -->
                <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                    <h2 class="text-lg font-semibold text-gray-800">Client</h2>
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <p><span class="font-medium">Nom : </span>{{ $achatdirect->userSenderI->name }}</p>
                        <p><span class="font-medium">Email : </span>{{ $achatdirect->userSenderI->email }}</p>
                        <p><span class="font-medium">Téléphone : </span>{{ $achatdirect->userSenderI->phone }}
                        </p>
                        <p class="bg-green-100 text-green-700 text-sm rounded-md p-3">
                            <span class="font-medium">lieu a livrer :
                            </span>{{ $achatdirect->localite }}
                        </p>
                    </div>
                </div>

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
                        <span>Frais de livraison :
                        </span>
                        <span>{{ number_format($notification->data['prixTrade'], 2, ',', '.') }}
                            FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Frais de service :
                        </span>
                        <span>1%</span>
                    </div>
                    <div class="flex justify-between font-medium text-gray-900">
                        <span>A recevoir :</span>
                        <span>
                            {{ number_format($notification->data['prixTrade'] - $notification->data['prixTrade'] * 0.01, 2, ',', '.') }}
                            FCFA</span>
                    </div>
                </div>
                @if ($notification->reponse == 'mainleveclient')
                    <!-- Code de vérification -->
                    <div
                        class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 dark:from-blue-900 dark:to-blue-800 dark:border-blue-700">
                        <div class="flex items-center space-x-3">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300">Code de
                                vérification</h3>
                        </div>
                        <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-white tracking-wider">
                            {{ $notification->data['livreurCode'] ?? 'N/A' }}
                        </p>
                    </div>
                @endif
            </div>
            <!-- Boutons -->
            <div class="mt-8 flex justify-end space-x-4">
                @if ($notification->reponse == 'mainleveclient')
                    <div class="flex space-x-2 mt-4">
                        <div class="bg-gray-400 text-white px-4 py-2 rounded-lg relative">
                            <!-- Texte du bouton et icône -->
                            Proceder a la livraison de la commande
                        </div>

                    </div>
                @else
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
                @endif
            </div>


        </div>
    </div>
</section>
