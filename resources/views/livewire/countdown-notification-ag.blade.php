<div>
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


    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <header class="mb-9">
            <h1 class="text-3xl font-bold mb-4">Facture Proformat</h1>
            <div class="text-gray-600">
                <p>Code la de Facture: <span class="font-semibold">#{{ $notification->data['code_unique'] }}</span>
                </p>
                <p>Date: <span
                        class="font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
                </p>
            </div>
        </header>



        <section class="mb-6 overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4">Détails de la Facture</h2>
            @if ($notification->data['specificite'] == 'PRO')
            <p class="text-sm font-semibold mb-4">Vous etes l'utilisateur qui a initié le groupage ,validez et proceder
                a la main levée</p>
            @endif

            <table class="min-w-full bg-white ">
                <thead>
                    <tr class="w-full bg-gray-200">
                        <th class="py-2 px-4 border-b">Elements</th>
                        <th class="py-2 px-4 border-b">Quantité commandé</th>
                        <th class="py-2 px-4 border-b">Prix Unitaire</th>
                        <th class="py-2 px-4 border-b">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 px-4 border-b">Produit commandé: {{ $produitfat->name }}</td>
                        <td class="py-2 px-4 border-b">{{ $notification->data['quantiteC'] }}</td>
                        <td class="py-2 px-4 border-b">
                            {{ number_format($this->notification->data['prixProd'], 0, ',', '.') }} FCFA</td>
                        <td class="py-2 px-4 border-b">
                            {{ number_format((int) ($notification->data['quantiteC'] * $this->notification->data['prixProd']), 0, ',', '.') }}
                            FCFA</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b">Livraiveur: {{ $userFour->name }}</td>
                        <td class="py-2 px-4 border-b">1</td>
                        <td class="py-2 px-4 border-b">
                            {{ number_format($notification->data['prixTrade'], 0, ',', '.') }} FCFA</td>
                        <td class="py-2 px-4 border-b">
                            {{ number_format($notification->data['prixTrade'], 0, ',', '.') }} FCFA</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="mb-6 flex justify-between">
            <div class="w-1/3  p-4 rounded-lg">
                @if ($notification->reponse)
                    <div class="flex space-x-2 mt-4">
                        <div class="bg-gray-400 text-white px-4 py-2 rounded-lg relative">
                            <!-- Texte du bouton et icône -->
                            Validé

                        </div>

                    </div>
                @else
                    <div class="flex space-x-2 mt-4">
                        <button wire:click.prevent='valider'
                            class="bg-green-800 text-white px-4 py-2 rounded-lg relative">
                            <!-- Texte du bouton et icône -->
                            <span wire:loading.remove>
                                Validez la commande
                            </span>
                            <span wire:loading>
                                Chargement...
                                <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                </svg>
                            </span>
                        </button>
                        @if ($notification->data['specificite'] == 'NOPRO')
                            <button wire:click.prevent='refuserPro'
                                class="bg-red-800 text-white px-4 py-2 rounded-lg relative">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Payez a l'arrivée du livreur
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="w-5 h-5 animate-spin inline-block ml-2"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>
                        @else
                            <button wire:click.prevent='refuserPro'
                                class="bg-red-800 text-white px-4 py-2 rounded-lg relative">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Refusez la commande
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="w-5 h-5 animate-spin inline-block ml-2"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>
                        @endif

                    </div>
                @endif


            </div>

            {{-- Afficher les messages d'erreur --}}


            <div class=" bg-gray-100 flex items-center p-2 rounded-lg">
                <p class="text-xl  text-center font-bold">Total TTC: <span
                        class="font-bold">{{ number_format((int) ($notification->data['quantiteC'] * $notification->data['prixProd']) + $notification->data['prixTrade'], 0, ',', '.') }}

                        FCFA</span></p>
            </div>


        </section>

        @if (session()->has('error'))
            <div class="alert text-red-500">
                {{ session('error') }}
            </div>
        @endif

        <footer>
            <p class="text-gray-600 text-center">Merci pour votre confiance.</p>
        </footer>
    </div>
</div>
