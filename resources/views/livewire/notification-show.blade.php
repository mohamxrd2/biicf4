<div>

    {{-- Achat Direct --}}
    @if ($notification->type === 'App\Notifications\AchatBiicf')
        @livewire('Achatdirect', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\livraisonAchatdirect')
        @livewire('livraisonAchatdirect', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\CountdownNotificationAd')
        @livewire('CountdownNotificationAd', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerifAd')
        @livewire('command-verif-ad', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\mainleveAd')
        @livewire('mainleve-ad', ['id' => $id])
        {{-- Appel Offre Direct --}}
    @elseif ($notification->type === 'App\Notifications\AppelOffre')
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h1 class="mb-2 text-xl font-semibold text-center">Negociation de l'offre sur
                <span class="text-3xl">{{ $notification->data['productName'] }}</span>
            </h1>
        </div>
        @livewire('appeloffre', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
        @livewire('appeloffreterminer', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\livraisonAppelOffre')
        <h1 class="mb-2 text-3xl font-semibold text-center">Negociation Des Livreurs(Achat d'un client)</h1>
        @livewire('livraisonappeloffre', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\CountdownNotificationAp')
        @livewire('countdown-notification-ap', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerifAp')
        @livewire('command-verif-ap', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\mainleveAp')
        @livewire('mainleve-ap', ['id' => $id])


        {{-- Appel offre grouper --}}
    @elseif ($notification->type === 'App\Notifications\AOGrouper')
        @livewire('appeloffregrouper', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
        <h1 class="mb-2 text-3xl font-semibold text-center ">Negociations pour la quantitée groupée</h1>
        @livewire('appeloffregroupernegociation', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminerGrouper')
        @livewire('appeloffreterminergrouper', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\livraisonAppelOffregrouper')
        <h1 class="mb-2 text-3xl font-semibold text-center">Negociation Des Livreurs</h1>
        @livewire('livraisonagrouper', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\CountdownNotificationAg')
        @livewire('countdown-notification-ag', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\commandVerifag')
        @livewire('mainleveag', ['id' => $id])


        {{-- fournisseur offre negocier --}}
    @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
        <h1 class="mb-2 text-3xl font-semibold text-center">Enchere Sur {{ $notification->data['produit_name'] }}</h1>

        @livewire('enchere', ['id' => $id])
    @elseif ($notification->type === 'App\Notifications\NegosTerminer')
        @livewire('offrenegosterminer', ['id' => $id])

        {{-- fournisseur offre grouper --}}
    @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
        <div class="flex flex-col justify-center p-4 bg-white border rounded-xl">
            <h1 class="mb-4 text-xl font-medium">Ajout de quantite</h1>
            <h2 class="mb-4 text-xl font-medium"><span class="font-semibold">Titre du produit:
                    {{ $notification->data['produit_name'] }}</span></h2>

            <p class="mb-3"><strong>Quantité: </strong> {{ $sommeQuantites }}
            </p>

            <p class="mb-3"><strong>Nombre de participant: </strong> {{ $nombreParticp }}
            </p>

            <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}"
                class="flex mb-3 text-blue-700 hover:underline">
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
                        class="block w-full px-4 py-3 mr-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Ajouter une quantité" name="quantitE" id="quantiteInput" wire:model="quantitE"
                        required>
                    <input type="hidden" name="name" wire:model="name">
                    <input type="hidden" name="produit_id" wire:model="produit_id">

                    <input type="hidden" name="code_unique" wire:model="code_unique">

                    <button type="submit" class="px-4 text-white bg-purple-500 rounded-md"
                        id="submitBtn">Ajouter</button>

                </div>

            </form>

            <div id="countdown-container" class="flex flex-col items-center justify-center mt-4">



                <span class="my-2 ">Temps restant pour vous ajouter</span>

                <div id="countdown"
                    class="flex items-center w-auto gap-2 p-3 text-3xl font-semibold text-red-500 bg-red-100 rounded-xl">

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
        <div class="flex flex-col justify-center p-4 bg-white border rounded-xl">

            <h2 class="mb-4 text-xl font-medium"><span class="font-semibold">Titre:
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
                class="flex justify-center py-1 mb-3 text-white bg-blue-700 rounded-xl">
                Voir le produit de base
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

            <div class="w-full gap-2 ">


                @if ($notification->reponse)
                    <div class="w-full py-1 bg-gray-300 border rounded-xl">
                        <p class="font-medium text-center text-md">Réponse envoyée</p>
                    </div>
                @else
                    <input type="hidden" wire:model="prixArticleNegos" name="prixarticle">
                    <input type="hidden" wire:model="code_unique" name="code_unique">
                    <input type="hidden" wire:model="notifId" name="notifId">


                    <!-- Bouton accepter -->
                    <form wire:submit.prevent="acceptoffre">

                        <div class="flex items-center mb-3">
                            <!-- Date de début -->
                            <div class="relative w-1/2 mr-2">
                                <label for="datePickerStart" class="block text-sm font-medium text-gray-700">Au plus
                                    tôt</label>
                                <input type="date" id="datePickerStart" name="dateTot" wire:model="dateTot"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('dateTot')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Date de fin -->
                            <div class="relative w-1/2 mr-2">
                                <label for="datePickerEnd" class="block text-sm font-medium text-gray-700">Au plus
                                    tard</label>
                                <input type="date" id="datePickerEnd" name="dateTard" wire:model="dateTard"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('dateTard')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center mb-3">
                            <!-- Heure de début -->
                            <div class="relative w-1/2 mr-2">
                                <label for="timePickerStart" class="block text-sm font-medium text-gray-700">Heure de
                                    début</label>
                                <input type="time" id="timePickerStart" name="timeStart" wire:model="timeStart"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('timeStart')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Heure de fin -->
                            <div class="relative w-1/2 mr-2">
                                <label for="timePickerEnd" class="block text-sm font-medium text-gray-700">Heure de
                                    fin</label>
                                <input type="time" id="timePickerEnd" name="timeEnd" wire:model="timeEnd"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('timeEnd')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Sélecteur de période de la journée -->
                        <div class="w-full mb-3">
                            <label for="dayPeriod" class="block text-sm text-gray-700 dark:text-gray-300">Période de
                                la journée</label>
                            <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg pe-9 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="" selected>Choisir la période</option>
                                <option value="Matin">Matin</option>
                                <option value="Après-midi">Après-midi</option>
                                <option value="Soir">Soir</option>
                                <option value="Nuit">Nuit</option>
                            </select>
                            @error('dayPeriod')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full px-4 py-1 text-white bg-green-500 rounded-xl hover:bg-green-700">
                            <span wire:loading.remove>
                                Accepter
                            </span>
                            <span wire:loading>
                                <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                </svg>
                            </span>
                        </button>

                    </form>

                    <button wire:click='refusoffre'
                        class="w-full px-4 py-1 mt-4 text-white bg-red-500 rounded-xl hover:bg-red-700">
                        <span wire:loading.remove>
                            refuser
                        </span>

                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
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
            <div class="p-3 mb-3 font-bold text-white bg-green-500 border rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="p-3 mb-3 font-bold text-white bg-red-500 border rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif



        <div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-lg">
            <header class="mb-9">
                <h1 class="mb-4 text-3xl font-bold">Facture Proformat</h1>
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
                <h2 class="mb-4 text-xl font-semibold">Détails de la Facture</h2>
                <table class="min-w-full bg-white ">
                    <thead>
                        <tr class="w-full bg-gray-200">
                            <th class="px-4 py-2 border-b">Elements</th>
                            <th class="px-4 py-2 border-b">Quantité commandé</th>
                            <th class="px-4 py-2 border-b">Prix Unitaire</th>
                            <th class="px-4 py-2 border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b">Produit commandé: {{ $produitfat->name }}</td>
                            <td class="px-4 py-2 border-b">{{ $notification->data['quantiteC'] }}</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format($this->notification->data['prixProd'], 0, ',', '.') }} FCFA</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format((int) ($notification->data['quantiteC'] * $this->notification->data['prixProd']), 0, ',', '.') }}
                                FCFA</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border-b">Livraiveur: {{ $userFour->name }}</td>
                            <td class="px-4 py-2 border-b">1</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format($notification->data['prixTrade'], 0, ',', '.') }} FCFA</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format($notification->data['prixTrade'], 0, ',', '.') }} FCFA</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="flex justify-between mb-6">
                <div class="w-1/3 p-4 rounded-lg">
                    @if ($notification->reponse)
                        <div class="flex mt-4 space-x-2">
                            <div class="relative px-4 py-2 text-white bg-gray-400 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                Validé

                            </div>

                        </div>
                    @else
                        <div class="flex mt-4 space-x-2">
                            <button wire:click.prevent='valider'
                                class="relative px-4 py-2 text-white bg-green-800 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Validez la commande
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="inline-block w-5 h-5 ml-2 animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>
                            <button wire:click.prevent='refuserPro'
                                class="relative px-4 py-2 text-white bg-red-800 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Refusez la commande
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="inline-block w-5 h-5 ml-2 animate-spin"
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


                <div class="flex items-center p-2 bg-gray-100 rounded-lg ">
                    <p class="text-xl font-bold text-center">Total TTC: <span
                            class="font-bold">{{ number_format((int) ($notification->data['quantiteC'] * $notification->data['prixProd']) + $notification->data['prixTrade'], 0, ',', '.') }}

                            FCFA</span></p>
                </div>


            </section>

            @if (session()->has('error'))
                <div class="text-red-500 alert">
                    {{ session('error') }}
                </div>
            @endif

            <footer>
                <p class="text-center text-gray-600">Merci pour votre confiance.</p>
            </footer>
        </div>
    @elseif ($notification->type === 'App\Notifications\AllerChercher')
        @if (session('success'))
            <div class="p-3 mb-3 font-bold text-white bg-green-500 border rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="p-3 mb-3 font-bold text-white bg-red-500 border rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif



        <div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-lg">
            <header class="mb-9">
                <h1 class="mb-4 text-3xl font-bold">Facture Proformat</h1>
                <div class="text-gray-600">
                    <p>Code la de Facture: <span class="font-semibold">#{{ $notification->data['code_livr'] }}</span>
                    </p>
                    <p>Date: <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </header>



            <section class="mb-6 overflow-x-auto">
                <h2 class="mb-4 text-xl font-semibold">Détails de la Facture</h2>
                <table class="min-w-full bg-white ">
                    <thead>
                        <tr class="w-full bg-gray-200">
                            <th class="px-4 py-2 border-b">Elements</th>
                            <th class="px-4 py-2 border-b">Quantité commandé</th>
                            <th class="px-4 py-2 border-b">Prix Unitaire</th>
                            <th class="px-4 py-2 border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b">Produit commandé: {{ $produitfat->name }}</td>
                            <td class="px-4 py-2 border-b">{{ $notification->data['quantite'] }}</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format($this->notification->data['prixProd'], 0, ',', '.') }} FCFA</td>
                            <td class="px-4 py-2 border-b">
                                {{ number_format((int) ($notification->data['quantite'] * $this->notification->data['prixProd']), 0, ',', '.') }}
                                FCFA</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border-b">Fournisseur: {{ $userFour->name }}</td>
                            <td class="px-4 py-2 border-b">N/A</td>
                            <td class="px-4 py-2 border-b">
                                N/A
                            <td class="px-4 py-2 border-b">
                                N/A
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="flex justify-between mb-6">
                <div class="w-1/3 p-4 rounded-lg">
                    @if ($notification->reponse)
                        <div class="flex mt-4 space-x-2">
                            <div class="relative px-4 py-2 text-white bg-gray-400 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                Validé

                            </div>

                        </div>
                    @else
                        <div class="flex mt-4 space-x-2">
                            <button wire:click.prevent='valider'
                                class="relative px-4 py-2 text-white bg-green-800 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Payez au paiement
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="inline-block w-5 h-5 ml-2 animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                </span>
                            </button>

                            <button wire:click.prevent='refuserPro'
                                class="relative px-4 py-2 text-white bg-red-800 rounded-lg">
                                <!-- Texte du bouton et icône -->
                                <span wire:loading.remove>
                                    Refusez la commande
                                </span>
                                <span wire:loading>
                                    Chargement...
                                    <svg class="inline-block w-5 h-5 ml-2 animate-spin"
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


                <div class="flex items-center p-2 bg-gray-100 rounded-lg ">
                    <p class="text-xl font-bold text-center">Total TTC: <span
                            class="font-bold">{{ number_format((int) ($notification->data['quantite'] * $notification->data['prixProd']), 0, ',', '.') }}

                            FCFA</span></p>
                </div>


            </section>

            @if (session()->has('error'))
                <div class="text-red-500 alert">
                    {{ session('error') }}
                </div>
            @endif

            <footer>
                <p class="text-center text-gray-600">Merci pour votre confiance.</p>
            </footer>
        </div>
    @elseif ($notification->type === 'App\Notifications\commandVerif')
        <div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-lg">
            <h2 class="mb-2 text-xl font-semibold">Informations Sur Le Fournisseur</h2>
            <div class="p-4 bg-gray-100 rounded-lg">
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
        <div class="max-w-4xl p-6 mx-auto mt-3 bg-white rounded-lg shadow-lg">
            <h2 class="my-2 text-xl font-semibold">Avis de conformité</h2>

            <div class="space-y-3">
                <!-- Quantité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                    <input type="radio" id="quantite-oui" name="quantite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="quantite-non" name="quantite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Qualité Apparente -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                    <input type="radio" id="qualite-oui" name="qualite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="qualite-non" name="qualite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Diversité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                    <input type="radio" id="diversite-oui" name="diversite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="diversite-non" name="diversite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>
            </div>




        </div>

        <div class="flex max-w-4xl mt-6">
            @if ($notification->reponse)
                <div class="p-2 bg-gray-300 border rounded-md ">
                    <p class="font-medium text-center text-md">Réponse envoyée</p>
                </div>
            @else
                <button wire:click='mainleve'
                    class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="mr-2 size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                    </svg>

                    <span wire:loading.remove>
                        Léver la main
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>
                </button>
                <button wire:click='refuseVerif' class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="mr-2 size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                    </svg>
                    <span wire:loading.remove>
                        Refuser
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>

                </button>
            @endif
        </div>
    @elseif ($notification->type === 'App\Notifications\mainleve')
        <div class="max-w-4xl p-6 mx-auto mb-3 bg-white rounded-lg shadow-lg">

            <h2 class="mb-2 text-xl font-semibold">Information sur le produit à enlevé et livré</h2>

            <div class="p-4 bg-gray-100 rounded-lg">
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

        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">

            <h2 class="mb-2 text-xl font-semibold">Avis de conformité</h2>

            <div class="space-y-3">
                <!-- Quantité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                    <input type="radio" id="quantite-oui" name="quantite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="quantite-non" name="quantite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Qualité Apparente -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                    <input type="radio" id="qualite-oui" name="qualite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="qualite-non" name="qualite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>

                <!-- Diversité -->
                <div class="flex items-center mb-3">
                    <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                    <input type="radio" id="diversite-oui" name="diversite" value="oui"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                    <input type="radio" id="diversite-non" name="diversite" value="non"
                        class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                </div>
            </div>

        </div>

        <form wire:submit.prevent="departlivr" method="POST">
            @csrf

            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <h2 class="mb-2 text-xl font-semibold">Estimation de date de livraison <span
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

                <div class="relative w-full mr-2 lg:w-1/2">
                    <input type="date" id="datePickerStart" name="dateLivr" wire:model.defer="dateLivr" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Ajouter une date de livraison">
                    @error('dateLivr')
                        <span class="mt-4 text-red-500">{{ $message }}</span>
                    @enderror
                </div>


                <!-- Select -->
                <div class="relative w-full mt-4 mr-2 lg:w-1/2">
                    <select id="select" wire:model.defer="matine" name="matine"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Choisir la période de la journée</option>
                        <option value="Matin">Matin</option>
                        <option value="Apres-midi">Après-midi</option>
                        <option value="Soir">Soir</option>
                    </select>


                    @error('matine')
                        <span class="mt-4 text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Select -->
            </div>

            <div class="flex max-w-4xl mx-auto mb-4 rounded-lg">
                @if ($notification->reponse)
                    <div class="p-2 bg-gray-300 border rounded-md">
                        <p class="font-medium text-center text-md">Réponse envoyée</p>
                    </div>
                @else
                    <button type="submit" class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="mr-2 size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>

                        <span wire:loading.remove>
                            Livré
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>

                    <button wire:click='refuseVerifLivreur'
                        class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
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
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Verification du livreur</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="block w-full px-4 py-3 text-sm bg-gray-100 border-transparent rounded-lg peer focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="px-2 ml-3 font-semibold text-white bg-green-400 rounded-md">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="mt-4 text-red-500">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="mt-4 text-green-500">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 text-red-500">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Information sur le livreur</h2>

                <div class="flex-col w-full ">
                    <div class="w-20 h-20 mb-6 mr-4 overflow-hidden bg-gray-100 rounded-full">

                        <img src="{{ asset($livreur->photo) }}" alt="photot" class="">

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du client: <span
                                class="font-semibold ">{{ $client->name }}</span>
                        </p>

                        <p class="mb-3 text-md">Contact du client: <span
                                class="font-semibold ">{{ $client->phone }}</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= "font-semibold ">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\mainlevefour')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Verification du livreur</h2>
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
                        class="block w-full px-4 py-3 text-sm bg-gray-100 border-transparent rounded-lg peer focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="px-2 ml-3 font-semibold text-white bg-green-400 rounded-md">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="mt-4 text-red-500">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="mt-4 text-green-500">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 text-red-500">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">{{ $notification->data['id_client'] }}</h2>

                <div class="flex-col w-full ">
                    <div class="w-20 h-20 mb-6 mr-4 overflow-hidden bg-gray-100 rounded-full">

                        {{-- <img src="{{ asset($livreur->photo) }}" alt="photot" class=""> --}}

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du livreur: <span
                                class="font-semibold ">{{ $livreur->name }}</span>
                        </p>
                        <p class="mb-3 text-md">Adress du livreur: <span
                                class="font-semibold ">{{ $livreur->address }}</span></p>
                        <p class="mb-3 text-md">Contact du livreur: <span
                                class="font-semibold ">{{ $livreur->phone }}</span></p>
                        <p class="mb-3 text-md">Engin du livreur : <span class="font-semibold ">Moto</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= "font-semibold ">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\VerifUser')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Vérification Du Client</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="block w-full px-4 py-3 text-sm bg-gray-100 border-transparent rounded-lg peer focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">

                    <button type="submit" wire:loading.attr="disabled"
                        class="px-2 ml-3 font-semibold text-white bg-green-400 rounded-md">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="mt-4 text-red-500">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="mt-4 text-green-500">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 text-red-500">
                    {{ session('error') }}
                </div>
            @endif

        </div>

        @if (session()->has('succes'))
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Information sur le client</h2>

                <div class="flex-col w-full ">
                    <div class="w-20 h-20 mb-6 mr-4 overflow-hidden bg-gray-100 rounded-full">

                        <img src="{{ asset($client->photo) }}" alt="photot" class="">

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du client: <span
                                class="font-semibold ">{{ $client->name }}</span>
                        </p>
                        {{-- <p class="mb-3 text-md">Adress du client: <span
                                class="font-semibold ">{{ $client->address }}</span></p> --}}
                        <p class="mb-3 text-md">Contact du client: <span
                                class="font-semibold ">{{ $client->phone }}</span></p>
                        {{-- <p class="mb-3 text-md">Engin du client : <span class="font-semibold ">Moto</span></p> --}}
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= "font-semibold ">{{ $produitfat->name }}</span></p>
                    </div>


                </div>
            </div>
        @endif
    @elseif ($notification->type === 'App\Notifications\mainleveclient')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Estimation de reception du colis</h2>

            <p class="text-md">Date : <span
                    class="font-semibold">{{ \Carbon\Carbon::parse($date_livr)->translatedFormat('d F Y') }} (
                    {{ $matine_client }} )</span>
            </p>

        </div>
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Verification du livreur</h2>


            <form wire:submit.prevent="verifyCode" method="POST">
                @csrf
                <div class="flex w-full">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code de livraison"
                        class="block w-full px-4 py-3 text-sm bg-gray-100 border-transparent rounded-lg peer focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">



                    <button type="submit" wire:loading.attr="disabled"
                        class="px-2 ml-3 font-semibold text-white bg-green-400 rounded-md">
                        <span wire:loading.remove>Valider</span>
                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            @error('code_verif')
                <span class="mt-4 text-red-500">{{ $message }}</span>
            @enderror

            @if (session()->has('succes'))
                <div class="mt-4 text-green-500">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 text-red-500">
                    {{ session('error') }}
                </div>
            @endif

        </div>
        @php
            // Assurez-vous que la variable $notification est définie et accessible
            $livreur = \App\Models\User::find($notification->data['livreur']);

        @endphp

        @if (session()->has('succes'))
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Information sur le livreur</h2>

                <div class="flex-col w-full ">
                    <div class="w-20 h-20 mb-6 mr-4 overflow-hidden bg-gray-100 rounded-full">

                        {{-- <img src="{{ asset($livreur->photo) }}" alt="photo" class=""> --}}

                    </div>

                    <div class="flex flex-col">
                        <p class="mb-3 text-md">Nom du livreur: <span
                                class="font-semibold ">{{ $livreur->name }}</span>
                        </p>
                        <p class="mb-3 text-md">Adress du livreur: <span
                                class="font-semibold ">{{ $livreur->address }}</span></p>
                        <p class="mb-3 text-md">Contact du livreur: <span
                                class="font-semibold ">{{ $livreur->phone }}</span></p>
                        <p class="mb-3 text-md">Engin du livreur : <span class="font-semibold ">Moto</span></p>
                        <p class="mb-3 text-md">Produit à recuperer: <span
                                class= "font-semibold ">{{ $produitfat->name }}</span></p>



                    </div>


                </div>
            </div>

            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">

                <h2 class="mb-2 text-xl font-semibold">Avis de conformité</h2>

                <div class="space-y-3">
                    <!-- Quantité -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                        <input type="radio" id="quantite-oui" name="quantite" value="oui"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="quantite-non" name="quantite" value="non"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>

                    <!-- Qualité Apparente -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                        <input type="radio" id="qualite-oui" name="qualite" value="oui"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="qualite-non" name="qualite" value="non"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>

                    <!-- Diversité -->
                    <div class="flex items-center mb-3">
                        <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                        <input type="radio" id="diversite-oui" name="diversite" value="oui"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                        <input type="radio" id="diversite-non" name="diversite" value="non"
                            class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                        <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
                    </div>
                </div>

            </div>

            <div class="flex max-w-4xl mx-auto">
                @if ($notification->reponse)
                    <div class="p-2 bg-gray-300 border rounded-md ">
                        <p class="font-medium text-center text-md">Réponse envoyée</p>
                    </div>
                @else
                    <button wire:click='acceptColis'
                        class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                        <span wire:loading.remove>
                            Accepter
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>

                    <button wire:click='refuseColis'
                        class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
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
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Livraison terminé</h2>
            <p class="mb-3 text-md">date de livraison: <span
                    class="font-semibold ">{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</span>
            </p>
            <p class="mb-3 text-md">Code de la livraison: <span
                    class="font-semibold ">{{ $notification->data['code_unique'] }}</span></p>

            <div class="flex justify-center w-full">
                <div class="mr-3 overflow-hidden w-80 h-80">
                    <svg class="w-full text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </div>
            </div>




        </div>
    @elseif ($notification->type === 'App\Notifications\Retrait')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">

            @if (session()->has('success'))
                <div class="mt-4 text-green-500">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 text-red-500">
                    {{ session('error') }}
                </div>
            @endif
            <h2 class="mb-4 text-xl font-medium">{{ $demandeur->name }}, vous a fait une demande de retrait</h2>

            <h1 class="text-4xl font-semibold">{{ $amount }} CFA</h1>

            <div class="flex w-full mt-5">
                @if ($notification->reponse)
                    <div class="p-2 bg-gray-300 border rounded-md ">
                        <p class="font-medium text-center text-md">Réponse envoyée</p>
                    </div>
                @else
                    <button wire:click='accepteRetrait'
                        class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                        <span wire:loading.remove>
                            Accepter
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>

                    <button wire:click='refusRetrait'
                        class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                        </svg>
                        <span wire:loading.remove>
                            Refuser
                        </span>
                        <span wire:loading>
                            Chargement...
                            <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>

                    </button>
                @endif

            </div>


        </div>
    @elseif ($notification->type === 'App\Notifications\DepositSos')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <!-- Messages de notification -->
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Affichage des erreurs de validation -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Titre -->
            <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

            <!-- Informations de l'utilisateur -->
            <div class="flex items-center mb-6 space-x-4">
                <div class="flex-shrink-0">
                    <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                        alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                </div>
                <div>
                    <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}</p>
                    <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                </div>
            </div>

            <!-- Détails du dépôt -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-700">Montant demandé</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($amountDeposit ?? 0, 0, ',', ' ') }}
                        CFA</p>
                </div>
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-700">R.O.I Attendu</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                        CFA</p>
                </div>
            </div>

            <!-- Sélection de l'opérateur -->
            <label for="operator" class="block mb-2 text-gray-700">Opérateur</label>
            <select id="operator" class="w-full p-2 mb-6 border border-gray-300 rounded-md" required
                wire:model="operator">
                <option value="" disabled selected>Choisir l'opérateur où vous voulez recevoir l'argent</option>
                <option value="Wave">Wave</option>
                <option value="Orange Money">Orange Money</option>
                <option value="Moov Money">Moov Money</option>
                <option value="MTN Money">MTN Money</option>
                <option value="Tresor Pay">Tresor Pay</option>
            </select>
            @error('operator')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Numéro de réception -->
            <label for="phonenumber" class="block mb-2 text-gray-700">Le numéro de réception</label>
            <input type="text" id="phonenumber" wire:model="phonenumber"
                class="w-full p-2 border border-gray-300 rounded-md" placeholder="Entrez le numéro de réception">
            @error('phonenumber')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Boutons d'action -->
            <div class="flex justify-around mt-4">
                @if ($existingRequest)
                    <p class="font-bold text-red-600 ">Demande Expiré !</p>
                @else
                    @if ($notification->reponse)
                        <div
                            class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                            Réponse envoyée
                        </div>
                    @else
                        <button wire:click="acceptDeposit"
                            class="px-6 py-2 font-semibold text-white bg-green-500 rounded-md shadow-md hover:bg-green-600">
                            Accepter
                        </button>
                        <button wire:click="rejectDeposit"
                            class="px-6 py-2 font-semibold text-white bg-red-500 rounded-md shadow-md hover:bg-red-600">
                            Refuser
                        </button>
                    @endif

                @endif

            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\DepositRecu')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Titre -->
            <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

            <!-- Informations de l'utilisateur -->
            <div class="flex items-center mb-6 space-x-4">
                <div class="flex-shrink-0">
                    <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                        alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                </div>
                <div>
                    <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                </div>
            </div>

            <!-- Informations sur la demande -->
            <div class="p-4 mb-4 border rounded-lg bg-gray-50">
                <h3 class="mb-2 font-medium text-gray-600 text-md">Numéro de réception de l'argent</h3>
                <p class="text-xl font-semibold text-gray-700">{{ $phonenumber }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">Opérateur</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $operatorRecu }}</p>
                </div>
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">Montant à envoyer</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                        CFA</p>
                </div>
            </div>

            <!-- Zone de téléchargement du reçu -->
            <div class="mt-4">
                <div class="relative">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Télécharger le reçu</label>
                    @if (!$receipt)
                        <!-- Zone de téléchargement stylisée -->
                        <label for="receipt"
                            class="flex flex-col items-center justify-center w-full h-40 mt-1 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                <path fill-rule="evenodd"
                                    d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="mt-2 text-sm text-gray-600">Cliquez ou déposez le reçu</span>
                        </label>
                    @else
                        <!-- Affichage de l'image téléchargée et bouton de suppression -->
                        <div class="relative">
                            <img src="{{ $receipt->temporaryUrl() }}" alt="Aperçu du reçu"
                                class="w-full h-auto border border-gray-300 rounded-md shadow-lg">
                            <button wire:click="$set('receipt', null)" type="button"
                                class="absolute p-1 text-white bg-red-600 rounded-full top-2 right-2 hover:bg-red-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <input wire:model="receipt" type="file" id="receipt" class="hidden" accept="image/*">
                    @error('receipt')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-around mt-4">
                @if ($notification->reponse)
                    <div
                        class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                        Réponse envoyée
                    </div>
                @else
                    <button wire:click="sendRecu"
                        class="px-6 py-2 font-semibold text-white bg-blue-500 rounded-md shadow-md hover:bg-blue-600">
                        Envoyer
                    </button>
                    <button wire:click="resetForm"
                        class="px-6 py-2 font-semibold text-black bg-gray-300 rounded-md shadow-md hover:bg-gray-400">
                        Annuler
                    </button>
                @endif
            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\DepositSend')
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Titre -->
            <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Verification du recu</h2>

            <!-- Informations de l'utilisateur -->
            <div class="flex items-center mb-6 space-x-4">
                <div class="flex-shrink-0">
                    <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                        alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                </div>
                <div>
                    <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">Montant envoyé</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($amountDeposit ?? 0, 0, ',', ' ') }}
                    </p>
                </div>
                <div class="p-4 text-center border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">Montant à recevoir</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                        CFA</p>
                </div>
            </div>


            <div class="flex justify-around mt-4">
                @if ($notification->reponse)
                    <div
                        class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                        Réponse envoyée
                    </div>
                @else
                    <button wire:click="montantRecu"
                        class="px-6 py-2 font-semibold text-white bg-green-500 rounded-md shadow-md hover:bg-green-600">
                        j'ai recu
                    </button>
                    <button wire:click="nonrecu"
                        class="px-6 py-2 font-semibold text-white bg-red-500 rounded-md shadow-md hover:bg-red-600">
                        Non, je n'es pas recu
                    </button>
                @endif
            </div>




        </div>




        

    @endif
</div>
