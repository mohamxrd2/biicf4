<div>
    @if ($notification->type === 'App\Notifications\AOGrouper')

        @livewire('appeloffregrouper', ['id' => $id])


        {{-- Achat Direct --}}
    @elseif ($notification->type === 'App\Notifications\AchatBiicf')
        @livewire('Achatdirect', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\livraisonAchatdirect')
        <h1 class="text-center text-3xl font-semibold mb-2">Negociation Des Livreurs(Achat d'un client)</h1>
        @livewire('livraisonAchatdirect', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\CountdownNotificationAd')
        @livewire('CountdownNotificationAd', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerifAd')
        @livewire('command-verif-ad', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\mainleveAd')
        @livewire('mainleve-ad', ['id' => $id])


        {{-- Appel Offre Direct --}}
    @elseif ($notification->type === 'App\Notifications\AppelOffre')
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-center text-xl font-semibold mb-2">Negociation de l'offre sur
                <span class="text-3xl">{{ $notification->data['productName'] }}</span>
            </h1>
        </div>
        @livewire('appeloffre', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
        @livewire('appeloffreterminer', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\livraisonAppelOffre')
        <h1 class="text-center text-3xl font-semibold mb-2">Negociation Des Livreurs(Achat d'un client)</h1>
        @livewire('livraisonappeloffre', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\CountdownNotificationAp')
        @livewire('countdown-notification-ap', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerifAp')
        @livewire('command-verif-ap', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\mainleveAp')
        @livewire('mainleve-ap', ['id' => $id])


        {{-- Appel offre grouper --}}
    @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
        <h1 class="text-center text-3xl font-semibold mb-2 ">Negociations pour la quantitée groupée</h1>
        @livewire('appeloffregroupernegociation', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminerGrouper')
        @livewire('appeloffreterminergrouper', ['id' => $id])


        {{-- fournisseur offre negocier --}}
    @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
        <h1 class="text-center text-3xl font-semibold mb-2">Enchere Sur {{ $notification->data['produit_name'] }}</h1>

        @livewire('enchere', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\NegosTerminer')
        @livewire('offrenegosterminer', ['id' => $id])

        {{-- fournisseur offre grouper --}}
    @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
            <h1 class="text-xl font-medium mb-4">Ajout de quantite</h1>
            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre du produit:
                    {{ $notification->data['produit_name'] }}</span></h2>

            <p class="mb-3"><strong>Quantité: </strong> {{ $sommeQuantites }}
            </p>

            <p class="mb-3"><strong>Nombre de participant: </strong> {{ $nombreParticp }}
            </p>

            <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

            <form wire:submit.prevent="add">
                @csrf
                <div class="flex">
                    <input type="number"
                        class="py-3 px-4 block w-full mr-3 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Ajouter une quantité" name="quantitE" id="quantiteInput" wire:model="quantitE"
                        required>
                    <input type="hidden" name="name" wire:model="name">
                    <input type="hidden" name="produit_id" wire:model="produit_id">

                    <input type="hidden" name="code_unique" wire:model="code_unique">

                    <button type="submit" class="bg-purple-500 text-white px-4 rounded-md"
                        id="submitBtn">Ajouter</button>

                </div>

            </form>

            <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">



                <span class=" my-2">Temps restant pour vous ajouter</span>

                <div id="countdown"
                    class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                    <div>-</div>:
                    <div>-</div>:
                    <div>-</div>
                </div>

            </div>

            <script>
                window.addEventListener('form-submitted', function() {
                    // Reload the page
                    location.reload();
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const quantiteInput = document.getElementById('quantiteInput');
                    const submitBtn = document.getElementById('submitBtn');

                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date("{{ $oldestNotificationDate }}");
                    startDate.setMinutes(startDate.getMinutes() + 2);


                    // Mettre à jour le compte à rebours à intervalles réguliers
                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        const currentDate = new Date();
                        const difference = startDate.getTime() - currentDate.getTime();

                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        const countdownElement = document.getElementById('countdown');
                        countdownElement.innerHTML = `
                            <div>${hours}h</div>:
                            <div>${minutes}m</div>:
                            <div>${seconds}s</div>
                        `;

                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            countdownElement.innerHTML = "Temps écoulé !";

                            // Désactiver le champ de saisie et le bouton
                            quantiteInput.disabled = true;
                            submitBtn.disabled = true;
                        }
                    }
                });
            </script>

        </div>
    @elseif ($notification->type === 'App\Notifications\OffreNegosDone')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">

            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $produit->name }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantite'] }}</p>
            @php
                // Assurez-vous que la variable $notification est définie et accessible
                $produit = \App\Models\ProduitService::find($notification->data['produit_id']);

                // Assurez-vous que $this->notification->data['quantite'] et $this->namefourlivr->prix sont définis et accessibles
                $quantite = $this->notification->data['quantite'] ?? 0;
                $prixUnitaire = $this->produit->prix ?? 0;

                // Calcul du prix total de la négociation
                $prixArticleNego = $quantite * $prixUnitaire;
            @endphp

            <p class="mb-3">
                <strong>Prix Total:</strong> {{ number_format($prixArticleNego, 2, ',', ' ') }} Fcfa
            </p>


            <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}"
                class="mb-3 bg-blue-700 text-white justify-center rounded-xl py-1 flex">
                Voir le produit de base
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

            <div class=" w-full gap-2 ">


                @if ($notification->reponse)
                    <div class="w-full bg-gray-300 border py-1 rounded-xl">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <input type="hidden" wire:model="prixArticleNegos" name="prixarticle">
                    <input type="hidden" wire:model="code_unique" name="code_unique">
                    <input type="hidden" wire:model="notifId" name="notifId">


                    <!-- Bouton accepter -->
                    <button wire:click='acceptoffre'
                        class="px-4 py-1 w-full text-white bg-green-500 rounded-xl hover:bg-green-700">
                        <span wire:loading.remove>
                            Accepter
                        </span>

                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                    <button wire:click='refusoffre'
                        class="mt-4 px-4 py-1 w-full text-white bg-red-500 rounded-xl hover:bg-red-700">
                        <span wire:loading.remove>
                            refuser
                        </span>

                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                @endif

            </div>


        </div>
    @elseif ($notification->type === 'App\Notifications\CountdownNotification')
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
                    <p>Code la de Facture: <span
                            class="font-semibold">#{{ $notification->data['code_unique'] }}</span>
                    </p>
                    <p>Date: <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </header>



            <section class="mb-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4">Détails de la Facture</h2>
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
                                    <svg class="w-5 h-5 animate-spin inline-block ml-2"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>
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
    @elseif ($notification->type === 'App\Notifications\AllerChercher')
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
                    <p>Code la de Facture: <span class="font-semibold">#{{ $notification->data['code_livr'] }}</span>
                    </p>
                    <p>Date: <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </header>



            <section class="mb-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4">Détails de la Facture</h2>
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
                            <td class="py-2 px-4 border-b">{{ $notification->data['quantite'] }}</td>
                            <td class="py-2 px-4 border-b">
                                {{ number_format($this->notification->data['prixProd'], 0, ',', '.') }} FCFA</td>
                            <td class="py-2 px-4 border-b">
                                {{ number_format((int) ($notification->data['quantite'] * $this->notification->data['prixProd']), 0, ',', '.') }}
                                FCFA</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">Fournisseur: {{ $userFour->name }}</td>
                            <td class="py-2 px-4 border-b">N/A</td>
                            <td class="py-2 px-4 border-b">
                                N/A
                            <td class="py-2 px-4 border-b">
                                N/A
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
                                    Payez au paiement
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

                        </div>
                    @endif

                </div>

                {{-- Afficher les messages d'erreur --}}


                <div class=" bg-gray-100 flex items-center p-2 rounded-lg">
                    <p class="text-xl  text-center font-bold">Total TTC: <span
                            class="font-bold">{{ number_format((int) ($notification->data['quantite'] * $notification->data['prixProd']), 0, ',', '.') }}

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
    @elseif ($notification->type === 'App\Notifications\GrouperFactureNotifications')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <header class="mb-9">
                <h1 class="text-3xl font-bold mb-4">Facture Proformat</h1>
                <div class="text-gray-600">
                    <p>Code la de Facture: <span
                            class="font-semibold">#{{ $notification->data['code_unique'] }}</span>
                    </p>
                    <p>Date: <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </header>



            <section class="mb-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4">Détails de la Facture</h2>
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
                            <td class="py-2 px-4 border-b">{{ $produitfat->prix }} FCFA</td>
                            <td class="py-2 px-4 border-b">
                                {{ (int) ($notification->data['quantiteC'] * $produitfat->prix) }} FCFA</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">Livraiveur: {{ $userFour->name }}</td>
                            <td class="py-2 px-4 border-b">N/A</td>
                            <td class="py-2 px-4 border-b">{{ $notification->data['prixTrade'] }} FCFA</td>
                            <td class="py-2 px-4 border-b">{{ $notification->data['prixTrade'] }} FCFA</td>
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
                                    <svg class="w-5 h-5 animate-spin inline-block ml-2"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>

                        </div>
                    @endif

                </div>

                <div class=" bg-gray-100 flex items-center p-2 rounded-lg">
                    <p class="text-xl  text-center font-bold">Total TTC: <span
                            class="font-bold">{{ (int) ($notification->data['quantiteC'] * $produitfat->prix) + $notification->data['prixTrade'] }}
                            FCFA</span></p>
                </div>
            </section>

            <footer>
                <p class="text-gray-600 text-center">Merci pour votre confiance.</p>
            </footer>
        </div>
    @elseif ($notification->type === 'App\Notifications\livraisonVerif')
        <h1 class="text-center text-3xl font-semibold mb-2">Negociation Des Livreurs</h1>
        @livewire('livraisonagrouper', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerif')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-2">Informations Sur Le Fournisseur</h2>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="mb-2">Nom du fournisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->name }}</span>
                </p>
                <p class="mb-2">Adresse du fournisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->address }}</span>
                </p>
                <p class="mb-2">Email du founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->email }}</span>
                </p>
                <p class="mb-2">Téléphone founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->phone }}</span>
                </p>
                <p class="mb-2">Code de Vérification : <span
                        class="font-semibold">{{ $notification->data['code_unique'] }}</span>
                </p>
            </div>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-3">
            <h2 class="text-xl font-semibold my-2">Avis de conformité</h2>

            <div class="space-y-3">
                <!-- Quantité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                    <input type="radio" id="quantite-oui" name="quantite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="quantite-non" name="quantite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Qualité Apparente -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                    <input type="radio" id="qualite-oui" name="qualite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="qualite-non" name="qualite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Diversité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                    <input type="radio" id="diversite-oui" name="diversite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="diversite-non" name="diversite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>
            </div>




        </div>

        <div class="max-w-4xl mt-6 flex">
            @if ($notification->reponse)
                <div class=" bg-gray-300 border p-2 rounded-md">
                    <p class="text-md font-medium text-center">Réponse envoyée</p>
                </div>
            @else
                <button wire:click='mainleve'
                    class="p-2 flex text-white font-medium bg-green-700 rounded-md mr-4"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                    </svg>

                    <span wire:loading.remove>
                        Léver la main
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
                <button wire:click='refuseVerif' class="p-2 text-white flex font-medium bg-red-700 rounded-md"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                    </svg>
                    <span wire:loading.remove>
                        Refuser
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
            @endif
        </div>
    @elseif ($notification->type === 'App\Notifications\commandVerifag')
        @livewire('mainleveag', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\mainleve')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-3">

            <h2 class="text-xl font-semibold mb-2">Information sur le produit à enlevé et livré</h2>

            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="mb-2">Nom du produit: <span class="font-semibold">{{ $produitfat->name }}</span></p>
                <p class="mb-2">Quantité: <span class="font-semibold">{{ $notification->data['quantite'] }}</span>
                </p>
                <p class="mb-2">Code de livraison: <span
                        class="font-semibold">{{ $notification->data['code_unique'] }}</span></p>
                <p class="mb-2">Téléphone founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->phone }}</span>
                </p>
                <p class="mb-2">Email founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->email }}</span>
                </p>
                @php
                    $produits = \App\Models\ProduitService::find($idProd);
                    $address = $produits->comnServ;
                    $clients = \App\Models\User::find($notification->data['id_client']);
                    $clientsadress = $clients->address;
                @endphp
                <p class="mb-2">Lieu d'enlevement: <span class="font-semibold">{{ $address }}</span>
                </p>
                <p class="mb-2">Lieu de livraison: <span class="font-semibold">{{ $clientsadress }}</span></p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">

            <h2 class="text-xl font-semibold mb-2">Avis de conformité</h2>

            <div class="space-y-3">
                <!-- Quantité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                    <input type="radio" id="quantite-oui" name="quantite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="quantite-non" name="quantite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Qualité Apparente -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                    <input type="radio" id="qualite-oui" name="qualite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="qualite-non" name="qualite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Diversité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                    <input type="radio" id="diversite-oui" name="diversite" value="oui"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="diversite-non" name="diversite" value="non"
                        class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>
            </div>

        </div>

        <form wire:submit.prevent="departlivr" method="POST">
            @csrf

            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-2">Estimation de date de livraison <span
                        class="text-red-700">*</span>

                    <span class="font-medium">Date prévue de récupération du client:</span>
                    <span>
                        @if (isset($notification->data['date_tot']) && isset($notification->data['date_tard']))
                            {{ $notification->data['date_tot'] }} - {{ $notification->data['date_tard'] }}
                        @else
                            Non spécifiée
                        @endif
                    </span>
                </h2>

                <div class="lg:w-1/2 w-full mr-2 relative">
                    <input type="date" id="datePickerStart" name="dateLivr" wire:model.defer="dateLivr" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Ajouter une date de livraison">
                    @error('dateLivr')
                        <span class="text-red-500 mt-4">{{ $message }}</span>
                    @enderror
                </div>


                <!-- Select -->
                <div class="lg:w-1/2 w-full mr-2 relative mt-4">
                    <select id="select" wire:model.defer="matine" name="matine"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Choisir la période de la journée</option>
                        <option value="Matin">Matin</option>
                        <option value="Apres-midi">Après-midi</option>
                        <option value="Soir">Soir</option>
                    </select>


                    @error('matine')
                        <span class="text-red-500 mt-4">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Select -->
            </div>

            <div class="max-w-4xl mx-auto flex rounded-lg mb-4">
                @if ($notification->reponse)
                    <div class="bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <button type="submit" class="p-2 flex text-white font-medium bg-green-700 rounded-md mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>

                        <span wire:loading.remove>
                            Livré
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

                    <button wire:click='refuseVerifLivreur'
                        class="p-2 text-white flex font-medium bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
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
                @endif
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dateLivrInput = document.querySelector('input[name="dateLivr"]');
                const startDate = new Date("{{ $notification->data['date_tot'] }}");
                const endDate = new Date("{{ $notification->data['date_tard'] }}");

                dateLivrInput.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);

                    if (selectedDate < startDate || selectedDate > endDate) {
                        alert('La date de livraison doit être dans l\'intervalle spécifié.');
                        this.value = ''; // Réinitialiser le champ si la date est invalide
                    }
                });
            });
        </script>
    @elseif ($notification->type === 'App\Notifications\attenteclient')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Verification du livreur</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-400 text-white font-semibold rounded-md px-2 ml-3">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="text-red-500 mt-4">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="text-green-500 mt-4">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-500 mt-4">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-4">Information sur le livreur</h2>

                <div class=" w-full flex-col">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4 mb-6">

                        <img src="{{ asset($livreur->photo) }}" alt="photot" class="">

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du client: <span
                                class=" font-semibold">{{ $client->name }}</span>
                        </p>

                        <p class="mb-3 text-md">Contact du client: <span
                                class=" font-semibold">{{ $client->phone }}</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= " font-semibold">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\mainlevefour')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Verification du livreur</h2>
            @php
                // Assurez-vous que la variable $notification est définie et accessible
                $livreur = \App\Models\User::find($notification->data['livreur']);

                // Assurez-vous que $this->notification->data['quantite'] et $this->namefourlivr->prix sont définis et accessibles
                $name = $livreur->name;

            @endphp

            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-400 text-white font-semibold rounded-md px-2 ml-3">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="text-red-500 mt-4">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="text-green-500 mt-4">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-500 mt-4">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-4">{{ $notification->data['id_client'] }}</h2>

                <div class=" w-full flex-col">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4 mb-6">

                        {{-- <img src="{{ asset($livreur->photo) }}" alt="photot" class=""> --}}

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du livreur: <span
                                class=" font-semibold">{{ $livreur->name }}</span>
                        </p>
                        <p class="mb-3 text-md">Adress du livreur: <span
                                class=" font-semibold">{{ $livreur->address }}</span></p>
                        <p class="mb-3 text-md">Contact du livreur: <span
                                class=" font-semibold">{{ $livreur->phone }}</span></p>
                        <p class="mb-3 text-md">Engin du livreur : <span class=" font-semibold">Moto</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= " font-semibold">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\VerifUser')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Vérification Du Client</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">

                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-400 text-white font-semibold rounded-md px-2 ml-3">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="text-red-500 mt-4">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="text-green-500 mt-4">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-500 mt-4">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-4">Information sur le client</h2>

                <div class=" w-full flex-col">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4 mb-6">

                        <img src="{{ asset($client->photo) }}" alt="photot" class="">

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du client: <span
                                class=" font-semibold">{{ $client->name }}</span>
                        </p>
                        {{-- <p class="mb-3 text-md">Adress du client: <span
                                class=" font-semibold">{{ $client->address }}</span></p> --}}
                        <p class="mb-3 text-md">Contact du client: <span
                                class=" font-semibold">{{ $client->phone }}</span></p>
                        {{-- <p class="mb-3 text-md">Engin du client : <span class=" font-semibold">Moto</span></p> --}}
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= " font-semibold">{{ $produitfat->name }}</span></p>
                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\mainleveclient')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Estimation de reception du colis</h2>

            <p class="text-md">Date : <span
                    class="font-semibold">{{ \Carbon\Carbon::parse($date_livr)->translatedFormat('d F Y') }} (
                    {{ $matine_client }} )</span>
            </p>

        </div>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Verification du livreur</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-400 text-white font-semibold rounded-md px-2 ml-3">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="text-red-500 mt-4">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="text-green-500 mt-4">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-500 mt-4">
                    {{ session('error') }}
                </div>
            @endif

        </div>
        @php
            // Assurez-vous que la variable $notification est définie et accessible
            $livreur = \App\Models\User::find($notification->data['livreur']);

        @endphp

        @if (session()->has('succes'))
            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-4">Information sur le livreur</h2>

                <div class=" w-full flex-col">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4 mb-6">

                        {{-- <img src="{{ asset($livreur->photo) }}" alt="photo" class=""> --}}

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du livreur: <span
                                class=" font-semibold">{{ $livreur->name }}</span>
                        </p>
                        <p class="mb-3 text-md">Adress du livreur: <span
                                class=" font-semibold">{{ $livreur->address }}</span></p>
                        <p class="mb-3 text-md">Contact du livreur: <span
                                class=" font-semibold">{{ $livreur->phone }}</span></p>
                        <p class="mb-3 text-md">Engin du livreur : <span class=" font-semibold">Moto</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= " font-semibold">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>

            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">

                <h2 class="text-xl font-semibold mb-2">Avis de conformité</h2>

                <div class="space-y-3">
                    <!-- Quantité -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                        <input type="radio" id="quantite-oui" name="quantite" value="oui"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="quantite-non" name="quantite" value="non"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>

                    <!-- Qualité Apparente -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                        <input type="radio" id="qualite-oui" name="qualite" value="oui"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="qualite-non" name="qualite" value="non"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>

                    <!-- Diversité -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                        <input type="radio" id="diversite-oui" name="diversite" value="oui"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="diversite-non" name="diversite" value="non"
                            class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>
                </div>

            </div>

            <div class="max-w-4xl mx-auto flex">
                @if ($notification->reponse)
                    <div class=" bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <button wire:click='acceptColis'
                        class="p-2 flex text-white font-medium bg-green-700 rounded-md mr-4">


                        <span wire:loading.remove>
                            Accepter
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

                    <button wire:click='refuseColis'
                        class="p-2 text-white flex font-medium bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
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
                @endif
            </div>

        @endif
    @elseif ($notification->type === 'App\Notifications\colisaccept')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-4">Livraison terminé</h2>
            <p class="mb-3 text-md">date de livraison: <span
                    class=" font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
            </p>
            <p class="mb-3 text-md">Code de la livraison: <span
                    class=" font-semibold">{{ $notification->data['code_unique'] }}</span></p>

            <div class="flex w-full justify-center">
                <div class=" w-80 h-80 overflow-hidden mr-3">
                    <svg class="w-full text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </div>
            </div>




        </div>
    @elseif ($notification->type === 'App\Notifications\Retrait')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">

            @if (session()->has('success'))
                <div class="text-green-500 mt-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-500 mt-4">
                    {{ session('error') }}
                </div>
            @endif
            <h2 class="text-xl font-medium mb-4">{{ $demandeur->name }}, vous a fait une demande de retrait</h2>

            <h1 class="font-semibold text-4xl">{{ $amount }} CFA</h1>

            <div class="w-full flex mt-5">
                @if ($notification->reponse)
                    <div class=" bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <button wire:click='accepteRetrait'
                        class="p-2 flex text-white font-medium bg-green-700 rounded-md mr-4">


                        <span wire:loading.remove>
                            Accepter
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

                    <button wire:click='refusRetrait'
                        class="p-2 text-white flex font-medium bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
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
                @endif

            </div>


        </div>

    @endif
</div>
