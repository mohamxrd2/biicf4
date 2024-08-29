<div>
    @if ($notification->type === 'App\Notifications\AOGrouper')
        <div>
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

            <!-- Centrer le titre en utilisant des classes de flexbox -->

            <div class="flex justify-center items-center h-16">
                <h1 class="text-3xl font-semibold">AJOUT DE QUANTITE </h1>

            </div>
            <div class="grid grid-cols-2 gap-4 p-4">
                <div class="lg:col-span-1 col-span-2">
                    <div class="w-full gap-y-2  mt-4">

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Prix unitaire maximal</p>
                            <p class="text-md font-medium text-gray-600">
                                {{ $appelOffreGroup->lowestPricedProduct }}
                            </p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Quantité total du groupage</p>
                            <p class="text-md font-medium text-gray-600">{{ $sumquantite }}</p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Payement</p>
                            <p class="text-md font-medium text-gray-600">{{ $appelOffreGroup->payment }}</p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Livraison</p>
                            <p class="text-md font-medium text-gray-600">{{ $appelOffreGroup->Livraison }}</p>
                        </div>


                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Specificité</p>
                            <p class="text-md font-medium text-gray-600">{{ $appelOffreGroup->specificity }}</p>
                        </div>



                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tôt</p>
                            <p class="text-md font-medium text-gray-600">{{ $appelOffreGroup->dateTot }}</p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tard</p>
                            <p class="text-md font-medium text-gray-600">{{ $appelOffreGroup->dateTard }}</p>
                        </div>


                    </div>
                </div>
                <div class="lg:col-span-1 col-span-2">

                    <div class="flex flex-col p-4">

                        <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                            uk-sticky="media: 1024; end: #js-oversized; offset: 80">
                            <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">

                                <form wire:submit.prevent="storeoffre" id="commentForm">
                                    <div
                                        class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                        <div class="flex flex-col space-y-4">
                                            <input type="number" wire:model.defer="quantite"
                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                placeholder="Ajouter une quantité..." required>

                                            <input type="text" wire:model.defer="localite"
                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                placeholder="Lieu De Livraison" required>

                                            <select wire:model="selectedOption" name="type"
                                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                                <option selected>Type de livraison</option>
                                                @foreach ($options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <button type="submit" id="submitBtnAppel"
                                            class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 18 20">
                                                    <path
                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                </svg>
                                            </span>
                                            <span wire:loading>
                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="w-full flex justify-center">
                                        <span id="prixTradeError"
                                            class="text-red-500 text-sm hidden text-center py-3"></span>
                                    </div>
                                </form>


                            </div>

                        </div>

                        <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">

                            @if ($datePlusAncienne)
                                <span class="mb-2">Temps restant pour le groupage</span>
                                <div id="countdown"
                                    class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100 p-3 rounded-xl w-auto">
                                    <div>-</div>:
                                    <div>-</div>:
                                    <div>-</div>
                                </div>
                            @endif

                        </div>

                        <div class="text-center mt-6 w-full">
                            <div class="bg-blue-500 text-white px-4 py-2 rounded">
                                Participants: {{ $appelOffreGroupcount }}
                            </div>
                        </div>



                    </div>

                    <script>
                        const qteInput = document.getElementById('quantite');
                        // Convertir la date de départ en objet Date JavaScript
                        const startDate = new Date("{{ $datePlusAncienne }}");

                        // Ajout d'une minute à la date de départ
                        startDate.setMinutes(startDate.getMinutes() + 1);

                        // Mettre à jour le compte à rebours à intervalles réguliers
                        const countdownTimer = setInterval(updateCountdown, 1000);

                        function updateCountdown() {
                            // Obtenir la date et l'heure actuelles
                            const currentDate = new Date();

                            // Calculer la différence entre la date cible et la date de départ en millisecondes
                            const difference = startDate.getTime() - currentDate.getTime();

                            // Convertir la différence en jours, heures, minutes et secondes
                            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                            // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
                            const countdownElement = document.getElementById('countdown');
                            countdownElement.innerHTML = `
                      <div>${hours}h</div>:
                      <div>${minutes}m</div>:
                      <div>${seconds}s</div>
                       `;

                            // Arrêter le compte à rebours lorsque la date cible est atteinte
                            if (difference <= 0) {
                                clearInterval(countdownTimer);
                                countdownElement.innerHTML = "Temps écoulé !";
                                document.getElementById('submitBtn').hidden = true;
                                qteInput.disabled = true;
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\NegosTerminer')
        @if (session('success'))
            <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex items-center justify-center h-screen bg-gray-100">
            <div class="w-full max-w-md p-6 bg-white shadow-md rounded-lg">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">PASSEZ A L'ACHAT DIRECT</h2>
                <h1>Le prix au quel vous negocier est {{ $prixProd }} FCFA</h1>


                <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect"
                    class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h1 class="text-xl text-center mb-3">Achat direct</h1>

                    <div class="space-y-3 mb-3 w-full">
                        <input type="number" id="quantityInput" name="quantite" wire:model.defer="quantite"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Quantité" data-min="{{ $produit->qteProd_min }}"
                            data-max="{{ $produit->qteProd_max }}" oninput="updateMontantTotalDirect()" required>
                    </div>
                    <div class="space-y-3 mb-3 w-full">
                        <input type="text" id="locationInput" name="localite" wire:model.defer="localite"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Lieu de livraison" required>
                    </div>

                    <div class="space-y-3 mb-3 w-full">
                        @if (!empty($produit->specification))
                            <div class="block">
                                <input type="radio" id="specificite_1" name="specificite"
                                    value="{{ $produit->specification }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500"
                                    required>
                                <label for="specificite_1" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification }}
                                </label>
                            </div>
                        @endif

                        @if (!empty($produit->specification2))
                            <div class="block">
                                <input type="radio" id="specificite_2" name="specificite"
                                    value="{{ $produit->specification2 }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                                <label for="specificite_2" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification2 }}
                                </label>
                            </div>
                        @endif

                        @if (!empty($produit->specification3))
                            <div class="block">
                                <input type="radio" id="specificite_3" name="specificite"
                                    value="{{ $produit->specification3 }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                                <label for="specificite_3" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification3 }}
                                </label>
                            </div>
                        @endif

                        <select wire:model="selectedOption" name="type"
                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                            <option value="" selected>Type de livraison</option>
                            @foreach ($options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="nameProd" wire:model.defer="nameProd">
                    <input type="hidden" name="userSender" wire:model.defer="userTrader">
                    <input type="hidden" name="idProd" wire:model.defer="idProd">
                    <input type="hidden" name="prix" wire:model.defer="prixProd">

                    <div class="flex justify-between px-4 mb-3 w-full">
                        <p class="font-semibold text-sm text-gray-500">Prix total:</p>
                        <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
                        <input type="hidden" name="montantTotal" id="montant_total_input">
                    </div>

                    <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

                    <div class="w-full text-center mt-3">
                        <button type="reset"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annuler</button>
                        <button type="submit" id="submitButton"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" disabled>
                            <span wire:loading.remove>Envoyer</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
        <script>
            // Fonction pour mettre à jour le montant total pour l'achat direct
            function updateMontantTotalDirect() {
                const quantityInput = document.getElementById('quantityInput');
                const price = document.querySelector('[data-price]');
                const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
                const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
                const quantity = parseInt(quantityInput.value);
                const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
                const montantTotalElement = document.getElementById('montantTotal');
                const errorMessageElement = document.getElementById('errorMessage');
                const submitButton = document.getElementById('submitButton');
                const montantTotalInput = document.getElementById('montant_total_input');

                // Exemple de solde utilisateur à adapter
                const userBalance = {{ $userWallet->balance }};

                // Validation et mise à jour du montant total
                if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
                    errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
                    errorMessageElement.classList.remove('hidden');
                    montantTotalElement.innerText = '0 FCFA';
                    submitButton.disabled = true;
                } else if (montantTotal > userBalance) {
                    errorMessageElement.innerText =
                        `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
                    errorMessageElement.classList.remove('hidden');
                    montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                    submitButton.disabled = true;
                } else {
                    errorMessageElement.classList.add('hidden');
                    montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                    montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
                    submitButton.disabled = false;
                }
            }

            // Fonction pour gérer la visibilité du contenu
            function toggleVisibility() {
                const contentDiv = document.getElementById('toggleContent');

                if (contentDiv.classList.contains('hidden')) {
                    contentDiv.classList.remove('hidden');
                    // Forcing reflow to enable transition
                    contentDiv.offsetHeight;
                    contentDiv.classList.add('show');
                } else {
                    contentDiv.classList.remove('show');
                    contentDiv.addEventListener('transitionend', () => {
                        contentDiv.classList.add('hidden');
                    }, {
                        once: true
                    });
                }
            }
        </script>
    @elseif ($notification->type === 'App\Notifications\AchatBiicf')
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande @if ($notification->type_achat == 'Take Away')
                            Take Away
                        @elseif ($notification->type_achat == 'Reservation')
                            Reservation
                        @else
                            Avec livraison
                        @endif
                    </h1>
                </div>
                <div class="p-6">
                    <!-- Détails de la Commande -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Informations de la Commande</h2>
                        <ul>
                            <li class="flex justify-between py-1"><span class="font-medium">Numéro de Commande:</span>
                                <span>{{ $notification->data['code_unique'] }}</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Date de Commande/
                                    Heure:</span>
                                <span>{{ $notification->created_at }}</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Fournisseur:</span>
                                <span>{{ Auth::user()->name }}</span>
                            </li>
                            <li class="flex justify-between py-1">
                                <span class="font-medium">Statut:</span>
                                <span
                                    class="
                                       @if ($notification->reponse == 'accepte') text-green-500
                                       @elseif($notification->reponse == 'refuser') text-red-500
                                       @else  text-yellow-500 @endif">
                                    @if ($notification->reponse == 'accepte')
                                        Validé
                                    @elseif($notification->reponse == 'refuser')
                                        Rejeté
                                    @else
                                        En attente de validation
                                    @endif
                                </span>

                            </li>

                        </ul>
                    </div>

                    <!-- Liste des Produits -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Éléments de la Commande</h2>
                        <p class="my-3 text-sm text-gray-500">Vous serez débité de 10% sur le prix de la marchandise
                        </p>

                        <!-- Tableau pour écrans moyens et plus grands -->
                        <div class="hidden md:block">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600">
                                        <th class="py-2 px-4">Produit ou Service</th>
                                        <th class="py-2 px-4">Quantité</th>
                                        <th class="py-2 px-4">Lieu de livraison</th>
                                        <th class="py-2 px-4">Spécificité</th>
                                        <th class="py-2 px-4">Prix de la commande</th>
                                        <th class="py-2 px-4">Somme finale</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-2 px-4">{{ $notification->data['nameProd'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['quantité'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['localite'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['specificite'] }}</td>
                                        <td class="py-2 px-4">
                                            {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                        </td>
                                        @php
                                            $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                            $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                        @endphp
                                        <td class="py-2 px-4">{{ number_format($sommeRecu, 2, ',', '.') }} Fcfa</td>
                                    </tr>
                                    <!-- Ajoutez d'autres lignes de produits si nécessaire -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Liste pour petits écrans -->
                        <div class="block md:hidden">
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Produit ou Service:</div>
                                    <div class="text-gray-800">{{ $notification->data['nameProd'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Quantité:</div>
                                    <div class="text-gray-800">{{ $notification->data['quantité'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Lieu de livraison:</div>
                                    <div class="text-gray-800">{{ $notification->data['localite'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Spécificité:</div>
                                    <div class="text-gray-800">{{ $notification->data['specificite'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Prix de la commande:</div>
                                    <div class="text-gray-800">
                                        {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                        Fcfa
                                    </div>

                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Somme finale:</div>
                                    @php
                                        $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                        $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                    @endphp
                                    <div class="text-gray-800">
                                        {{ number_format($sommeRecu, 2, ',', '.') }} Fcfa
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                        class="mb-3 text-blue-700 hover:underline flex items-center">
                        Voir le produit
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="ml-2 w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </a>

                    <div class="mt-6 flex flex-col md:flex-row justify-end gap-4">
                        @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                            <div class="w-full bg-gray-300 border p-2 rounded-md">
                                <p class="text-md font-medium text-center">Réponse envoyée</p>
                            </div>
                        @elseif ($notification->type_achat == 'Take Away')
                            <button wire:click="takeaway"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                            <button wire:click="refuser" id="btn-refuser" type="submit"
                                class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        @elseif ($notification->type_achat == 'Reservation')
                            <button wire:click="takeaway"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                            <button wire:click="refuser" id="btn-refuser" type="submit"
                                class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        @else
                            <div x-data="{ isOpen: false, open: false, textareaValue: 'Emballage:..., Dimension:..., Poids:..., Autre:...' }" x-cloak>
                                <!-- Buttons to open modal and refuse -->
                                <button @click="isOpen = true"
                                    class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                                <button wire:click="refuser" id="btn-refuser" type="submit"
                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>

                                <!-- Modal -->
                                <div x-show="isOpen" id="hs-basic-modal"
                                    class="hs-overlay hs-overlay-open:opacity-100 hs-overlay-open:duration-500 fixed top-0 left-0 z-50 w-full h-full bg-black bg-opacity-50 overflow-y-auto">
                                    <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                        <div
                                            class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto">
                                            <div class="flex justify-between items-center py-3 px-4 border-b">
                                                <h3 class="font-bold text-gray-800">Envoi au livreur</h3>
                                                <button @click="isOpen = false"
                                                    class="text-gray-800 hover:text-gray-600">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="p-4">
                                                <p class="text-gray-800">
                                                    @if ($nombreLivr)
                                                        <div>
                                                            {{-- <h3>Détails de la commande</h3> --}}
                                                            {{-- <p><strong>ID de client :</strong> {{ $Idsender }}</p>
                                                            <p><strong>Continent du client :</strong>
                                                                {{ $clientContinent }}</p>
                                                            <p><strong>Sous-Region du client :</strong>
                                                                {{ $clientSous_Region }}</p>
                                                            <p><strong>Pays du client :</strong> {{ $clientPays }}
                                                            </p>
                                                            <p><strong>Departement du client :</strong>
                                                                {{ $clientDepartement }}</p>
                                                            <p><strong>Commune du client :</strong>{{ $clientCommune }}
                                                            </p> --}}
                                                            <p><strong>le nombre total disponible
                                                                    :</strong>{{ $livreursCount }}</p>

                                                            <h3>Liste des livreurs disponibles</h3>
                                                            @if ($livreurs->isEmpty())
                                                                <p>Aucun livreur disponible pour ce pays et cette ville.
                                                                </p>
                                                            @else
                                                                {{-- <ul>
                                                                    @foreach ($livreurs as $livreur)
                                                                        <li>
                                                                            Livreur ID: {{ $livreur->id }} -
                                                                            Expérience: {{ $livreur->experience }} ans
                                                                            - Livreur ID: {{ $livreur->user_id }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul> --}}
                                                            @endif
                                                        </div>
                                                    @else
                                                        Aucun livreur disponible dans la zone
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex justify-end items-center py-3 px-4 border-t">
                                                <div x-data="{ open: false }">
                                                    <!-- Button to toggle textarea visibility -->
                                                    <button @click="open = !open"
                                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                        Ajouter le nouveau Conditionnement
                                                    </button>

                                                    <!-- Textarea and action buttons -->
                                                    <div x-show="open" class="mt-4">
                                                        <textarea x-model="textareaValue" class="w-full p-2 border border-gray-300 rounded" rows="6" required>
                                                    </textarea>
                                                        <div class="mt-2 flex justify-end space-x-2">
                                                            <button @click="open = false"
                                                                class="py-2 px-4 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded">
                                                                Annuler
                                                            </button>
                                                            <button @click.prevent="$wire.accepter(textareaValue)"
                                                                @if ($nombreLivr == 0) disabled @endif
                                                                class="py-2 px-4 bg-blue-600 text-white hover:bg-blue-700 rounded">
                                                                <span wire:loading.remove>Envoyer</span>
                                                                <span wire:loading>En cours...</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffre')
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-center text-xl font-semibold mb-2">Negociation de l'offre sur
                <span class="text-3xl">{{ $notification->data['productName'] }}</span>
            </h1>
        </div>
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">


                <div class="w-full gap-y-2  mt-4">

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Prix unitaire maximal</p>
                        <p class="text-md font-medium text-gray-600">
                            {{ isset($notification->data['lowestPricedProduct']) ? number_format($notification->data['lowestPricedProduct'], 2, ',', ' ') : (isset($notification->data['sumquantite']) ? number_format($notification->data['sumquantite'], 2, ',', ' ') : '') }}
                        </p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Quantité</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['quantity'] }}</p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Payement</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['payment'] }}</p>
                    </div>
                    @if ($notification->data['Livraison'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Livraison</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['Livraison'] }}
                            </p>
                        </div>
                    @endif
                    @if ($notification->data['specificity'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Specificité</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['specificity'] }}
                            </p>
                        </div>
                    @endif

                    @if ($notification->data['dateTot'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tôt</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTot'] }}</p>
                        </div>
                    @endif


                    @if ($notification->data['dateTard'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tard</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTard'] }}</p>
                        </div>
                    @endif

                </div>
                <a href="#" class="mb-3 text-blue-700 hover:underline flex">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>

            </div>
            <div class="lg:col-span-1 col-span-2">

                <div class="p-4">

                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">

                                @foreach ($comments as $comment)
                                    <div class="flex items-center gap-3 relative">
                                        <img src="{{ asset($comment['photoUser']) }}" alt=""
                                            class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">
                                        <div class="flex-1">
                                            <p class=" text-base text-black font-medium inline-block dark:text-white">
                                                {{ $comment['nameUser'] }}</p>
                                            <p class="text-sm mt-0.5">
                                                {{ number_format($comment['prix'], 2, ',', ' ') }} FCFA</p>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form wire:submit.prevent="commentForm">
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" wire:model="code_unique">
                                    <input type="hidden" wire:model="quantiteC">
                                    <input type="hidden" wire:model="difference">
                                    <input type="hidden" wire:model="idsender">
                                    <input type="hidden" wire:model="id_trader">
                                    <input type="hidden" wire:model="nameprod">
                                    <input type="hidden" wire:model="localite">
                                    <input type="hidden" wire:model="specificite">

                                    @if ($locked)
                                        discussion terminer
                                    @else
                                        <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Faire une offre..." required>
                                        @error('prixTrade')
                                            <span class="text-red-500">{{ $message }}</span>
                                        @enderror
                                        <button type="submit" id="submitBtnAppel"
                                            class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 18 20">
                                                    <path
                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                </svg>
                                            </span>
                                            <span wire:loading>
                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                </svg>
                                            </span>
                                        </button>
                                    @endif

                                </div>
                            </form>
                        </div>

                        <div class="w-full flex justify-center">
                            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                        </div>
                    </div>
                    @if ($oldestCommentDate)
                        <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">

                            <span class=" mb-2">Temps restant pour cette negociatiation</span>

                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        </div>
                    @endif
                </div>


            </div>
            <!-- Footer Section -->
            <footer class="bg-gray-800 text-white py-4 mt-8 w-full">
                <div class="container mx-auto text-center">
                    <span class="text-sm font-medium">
                        À la fin du temps, la page sera supprimée.
                    </span>
                </div>
            </footer>


            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const prixTradeInput = document.getElementById('prixTrade');
                    const submitBtn = document.getElementById('submitBtnAppel');
                    const prixTradeError = document.getElementById('prixTradeError');
                    const produitPrix = parseFloat('{{ $notification->data['lowestPricedProduct'] }}');

                    // Convertir les dates en temps UNIX pour faciliter les calculs
                    const startDate = new Date("{{ $oldestCommentDate }}").getTime();
                    const serverTime = new Date("{{ $serverTime }}").getTime();

                    // Calculer la date de fin du compte à rebours
                    const countdownDuration = 2 * 60 * 1000; // 2 minutes en millisecondes
                    const endDate = startDate + countdownDuration;

                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);

                        if (prixTradeValue > produitPrix) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = `Le prix doit être inférieur à ${produitPrix} FCFA`;
                            prixTradeError.classList.remove('hidden');
                            submitBtn.classList.add('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                            submitBtn.classList.remove('hidden');
                        }
                    });

                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        const currentDate = new Date().getTime();
                        const difference = endDate - currentDate;

                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            const countdownElement = document.getElementById('countdown');
                            if (countdownElement) {
                                countdownElement.innerHTML = "Temps écoulé !";
                            }

                            // Trouver le commentaire avec le prix le plus bas
                            const lowestPricedComment = @json($comments).reduce((min, comment) => comment.prix <
                                min.prix ? comment : min, {
                                    prix: Infinity
                                });

                            if (lowestPricedComment && lowestPricedComment.nameUser) {
                                prixTradeError.textContent =
                                    `L'utilisateur avec le prix le plus bas est ${lowestPricedComment.nameUser} avec ${lowestPricedComment.prix} FCFA !`;
                            } else {
                                prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                            }

                            prixTradeError.classList.remove('hidden');
                            // Vous pouvez également désactiver le champ de prix et le bouton si nécessaire
                            // prixTradeInput.disabled = true;
                            // submitBtn.classList.add('hidden');

                            // Émettre l'événement Livewire si nécessaire
                            // Livewire.dispatch('timeExpired');
                        } else {
                            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                            const countdownElement = document.getElementById('countdown');
                            if (countdownElement) {
                                countdownElement.innerHTML = `
                                  <div>${hours}h</div>:
                                  <div>${minutes}m</div>:
                                  <div>${seconds}s</div>
                                `;
                            }
                        }
                    }
                });
            </script>


        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
        <h1 class="text-center text-3xl font-semibold mb-2">Negociations des Fournisseurs</h1>
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">

                <h2 class="text-3xl font-semibold mb-2">{{ $notification->data['productName'] }}</h2>

                <div class="w-full gap-y-2  mt-4">

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Prix unitaire maximal</p>
                        <p class="text-md font-medium text-gray-600">
                            {{ isset($notification->data['lowestPricedProduct']) ? number_format($notification->data['lowestPricedProduct'], 2, ',', ' ') : (isset($notification->data['sumquantite']) ? number_format($notification->data['sumquantite'], 2, ',', ' ') : '') }}
                        </p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Quantité</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['quantity'] }}</p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Payement</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['payment'] }}</p>
                    </div>
                    @if ($notification->data['Livraison'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Livraison</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['Livraison'] }}</p>
                        </div>
                    @endif
                    @if ($notification->data['specificity'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Specificité</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['specificity'] }}</p>
                        </div>
                    @endif

                    @if ($notification->data['dateTot'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tôt</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTot'] }}</p>
                        </div>
                    @endif


                    @if ($notification->data['dateTard'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tard</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTard'] }}</p>
                        </div>
                    @endif

                </div>
            </div>
            <div class="lg:col-span-1 col-span-2">

                <div class="p-4">

                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">

                            <!-- comments -->
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">

                                @foreach ($comments as $comment)
                                    <div class="flex items-center gap-3 relative">
                                        <img src="{{ asset($comment['photoUser']) }}" alt=""
                                            class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">
                                        <div class="flex-1">
                                            <p class=" text-base text-black font-medium inline-block dark:text-white">
                                                {{ $comment['nameUser'] }}</p>
                                            <p class="text-sm mt-0.5">
                                                {{ number_format($comment['prix'], 2, ',', ' ') }} FCFA</p>

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <!-- add comment -->
                            <form wire:submit.prevent="commentFormGroupe">
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="code_unique" wire:model="code_unique"
                                        value="{{ $notification->data['code_unique'] }}">
                                    <input type="hidden" name="quantiteC" wire:model="quantiteC"
                                        value="{{ $notification->data['quantity'] }}">
                                    <input type="hidden" name="difference" wire:model="difference"
                                        value="{{ $notification->data['difference'] }}">

                                    <input type="hidden" name="id_trader" wire:model="id_trader">
                                    <input type="hidden" name="nameprod" wire:model="nameprod"
                                        value="{{ $notification->data['productName'] }}">
                                    {{--  --}}
                                    @if (is_array($id_sender))
                                        @foreach ($id_sender as $userId)
                                            <input type="hidden" name="id_sender[]"
                                                wire:model="id_sender.{{ $loop->index }}"
                                                value="{{ $userId }}">
                                        @endforeach
                                    @endif
                                    <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>
                                    @error('prixTrade')
                                        <span class="text-red-500">{{ $message }}</span>
                                    @enderror

                                    <input type="hidden" name="localite" id="localite" wire:model="localite"
                                        value="{{ $notification->data['localite'] }}">
                                    @error('localite')
                                        <span class="text-red-500">{{ $message }}</span>
                                    @enderror

                                    {{-- <input type="text" name="specificite" id="specificite"
                                        wire:model="specificite" value="{{ $notification->data['specificity'] }}"> --}}


                                    <button type="submit" id="submitBtnAppel"
                                        class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                        <span wire:loading.remove>
                                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                            </svg>
                                        </span>
                                        <span wire:loading>
                                            <svg class="w-5 h-5 animate-spin inline-block"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </form>


                        </div>

                        <div class="w-full flex justify-center">
                            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                        </div>
                    </div>

                    <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">
                        @if ($oldestCommentDate)
                            <span class=" mb-2">Temps restant pour cette negociatiation</span>

                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const prixTradeInput = document.getElementById('prixTrade');
                    const submitBtn = document.getElementById('submitBtnAppel');
                    const prixTradeError = document.getElementById('prixTradeError');
                    const produitPrix = parseFloat('{{ $notification->data['lowestPricedProduct'] }}');



                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);

                        if (prixTradeValue > produitPrix) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = `Le prix doit être inférieur à ${produitPrix} FCFA`;
                            prixTradeError.classList.remove('hidden');
                            submitBtn.classList.add('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                            submitBtn.classList.remove('hidden');
                        }
                    });

                    const startDate = new Date("{{ $oldestCommentDate }}");
                    startDate.setMinutes(startDate.getMinutes() + 1);

                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        const currentDate = new Date();
                        const difference = startDate.getTime() - currentDate.getTime();

                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        const countdownElement = document.getElementById('countdown');
                        if (countdownElement) {
                            countdownElement.innerHTML = `
                          <div>${hours}h</div>:
                          <div>${minutes}m</div>:
                          <div>${seconds}s</div>
                        `;
                        }

                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            if (countdownElement) {
                                countdownElement.innerHTML = "Temps écoulé !";
                            }
                            prixTradeInput.disabled = true;
                            submitBtn.classList.add('hidden');



                            const highestPricedComment = @json($comments).reduce((max, comment) => comment
                                .prix > max.prix ? comment : max, {
                                    prix: -Infinity
                                });

                            if (highestPricedComment && highestPricedComment.nameUser) {
                                prixTradeError.textContent =
                                    `L'utilisateur avec le prix le plus bas est ${highestPricedComment.nameUser} avec ${highestPricedComment.prix} FCFA !`;
                            } else {
                                prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                            }
                            prixTradeError.classList.remove('hidden');
                        }
                    }
                });
            </script>
        </div>
    @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
        <h1 class="text-center text-3xl font-semibold mb-2">L'Enchere Des Clients </h1>
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">

                <h2 class="text-3xl font-semibold mb-2">{{ $notification->data['produit_name'] }}</h2>

                <div class="w-full gap-y-2  my-4">

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Prix unitaire minimum</p>
                        <p class="text-md font-medium text-gray-600">
                            {{ number_format($notification->data['produit_prix'], 2, ',', ' ') }}
                        </p>
                    </div>

                    @if ($notification->data['produit_livraison'])
                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Livraison</p>
                            <p class="text-md font-medium text-gray-600">
                                {{ $notification->data['produit_livraison'] }}</p>
                        </div>
                    @endif


                </div>

                <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}"
                    class=" bg-blue-500 text-white p-2 rounded font-medium hover:bg-blue-600  mt-10">
                    Voir le produit
                </a>




            </div>

            <div class="lg:col-span-1 col-span-2">

                <div class="p-4">
                    <div class="tempsecoule"></div>
                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">



                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">



                            <!-- comments -->
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">



                                @foreach ($comments as $comment)
                                    <div class="flex items-center gap-3 relative">
                                        <img src="{{ asset($comment['photoUser']) }}" alt=""
                                            class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">
                                        <div class="flex-1">
                                            <p class=" text-base text-black font-medium inline-block dark:text-white">
                                                {{ $comment['nameUser'] }}</p>
                                            <p class="text-sm mt-0.5">
                                                {{ number_format($comment['prix'], 2, ',', ' ') }} FCFA</p>

                                        </div>
                                    </div>
                                @endforeach

                            </div>



                            <!-- add comment -->
                            <form wire:submit.prevent="commentoffgroup">
                                @csrf
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">

                                    <input type="hidden" wire:model="idProd">
                                    <input type="hidden" wire:model="code_unique">
                                    <input type="hidden" wire:model="id_trader">
                                    <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>


                                    <button type="submit" id="submitBtnAppel"
                                        class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                        <span wire:loading.remove>
                                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                            </svg>
                                        </span>
                                        <span wire:loading>
                                            <svg class="w-5 h-5 animate-spin inline-block"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>



                            </form>


                        </div>
                        <div class="w-full flex justify-center ">

                            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>

                        </div>

                    </div>

                    <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">


                        @if ($oldestCommentDate)
                            <span class=" mb-2">Temps restant pour cette negociatiation</span>

                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const prixTradeInput = document.getElementById('prixTrade');
                    const submitBtn = document.getElementById(
                        'submitBtnAppel'); // Assurez-vous que cet identifiant est correct
                    const prixTradeError = document.getElementById('prixTradeError');
                    const produitPrix = parseFloat('{{ $notification->data['produit_prix'] }}');

                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);

                        if (prixTradeValue < produitPrix) {
                            // Si le prix est invalide
                            submitBtn.disabled = true; // Désactiver le bouton
                            prixTradeError.textContent = `Le prix doit être supérieur à ${produitPrix} FCFA`;
                            prixTradeError.classList.remove('hidden');
                            submitBtn.classList.add('hidden'); // Masquer le bouton
                        } else {
                            // Si le prix est valide
                            submitBtn.disabled = false; // Activer le bouton
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                            submitBtn.classList.remove('hidden'); // Afficher le bouton
                        }
                    });

                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date("{{ $oldestCommentDate }}");
                    startDate.setMinutes(startDate.getMinutes() + 1); // Ajouter 1 minute pour la date de départ

                    // Mettre à jour le compte à rebours à intervalles réguliers
                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        // Obtenir la date et l'heure actuelles
                        const currentDate = new Date();

                        // Calculer la différence entre la date cible et la date de départ en millisecondes
                        const difference = startDate.getTime() - currentDate.getTime();

                        // Convertir la différence en jours, heures, minutes et secondes
                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
                        const countdownElement = document.getElementById('countdown');
                        countdownElement.innerHTML = `
                            <div>${hours}h</div>:
                            <div>${minutes}m</div>:
                            <div>${seconds}s</div>
                        `;

                        // Arrêter le compte à rebours lorsque la date cible est atteinte
                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            countdownElement.innerHTML = "Temps écoulé !";
                            prixTradeInput.disabled = true;
                            submitBtn.classList.add('hidden'); // Masquer le bouton lorsque le temps est écoulé

                            // Obtenir le commentaire avec le prix le plus élevé
                            const highestPricedComment = @json($comments).reduce((max, comment) => comment
                                .prix > max.prix ? comment : max, {
                                    prix: -Infinity
                                });

                            if (highestPricedComment && highestPricedComment.nameUser) {
                                prixTradeError.textContent =
                                    `L'utilisateur avec le prix le plus élevé est ${highestPricedComment.nameUser} avec ${highestPricedComment.prix} FCFA !`;
                            } else {
                                prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                            }
                            prixTradeError.classList.remove('hidden');
                        }
                    }
                });
            </script>




        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande @if ($notification->type_achat == 'Take Away')
                            Take Away
                        @elseif ($notification->type_achat == 'Reservation')
                            Reservation
                        @else
                            Avec livraison
                        @endif
                    </h1>
                </div>
                <div class="p-6">
                    <!-- Détails de la Commande -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Informations de la Commande</h2>
                        <ul>
                            <li class="flex justify-between py-1"><span class="font-medium">Numéro de Commande:</span>
                                <span>{{ $notification->data['code_unique'] }}</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Date de Commande/
                                    Heure:</span>
                                <span>{{ $notification->created_at }}</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Fournisseur:</span>
                                <span>{{ Auth::user()->name }}</span>
                            </li>
                            <li class="flex justify-between py-1">
                                <span class="font-medium">Statut:</span>
                                <span
                                    class="
                                   @if ($notification->reponse == 'accepte') text-green-500
                                   @elseif($notification->reponse == 'refuser') text-red-500
                                   @else  text-yellow-500 @endif">
                                    @if ($notification->reponse == 'accepte')
                                        Validé
                                    @elseif($notification->reponse == 'refuser')
                                        Rejeté
                                    @else
                                        En attente de validation
                                    @endif
                                </span>

                            </li>

                        </ul>
                    </div>

                    <!-- Liste des Produits -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Éléments de la Commande</h2>
                        <p class="my-3 text-sm text-gray-500">Vous serez débité de 10% sur le prix de la marchandise
                        </p>

                        <!-- Tableau pour écrans moyens et plus grands -->
                        <div class="hidden md:block">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600">
                                        <th class="py-2 px-4">Produit ou Service</th>
                                        <th class="py-2 px-4">Quantité</th>
                                        <th class="py-2 px-4">Lieu de livraison</th>
                                        <th class="py-2 px-4">Spécificité</th>
                                        <th class="py-2 px-4">Prix de la commande</th>
                                        <th class="py-2 px-4">Somme finale</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-2 px-4">{{ $notification->data['nameprod'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['quantiteC'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['localite'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['specificite'] }}</td>
                                        <td class="py-2 px-4">
                                            {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                        </td>
                                        @php
                                            $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                            $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                        @endphp
                                        <td class="py-2 px-4">{{ number_format($sommeRecu, 2, ',', '.') }} Fcfa</td>
                                    </tr>
                                    <!-- Ajoutez d'autres lignes de produits si nécessaire -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Liste pour petits écrans -->
                        <div class="block md:hidden">
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Produit ou Service:</div>
                                    <div class="text-gray-800">{{ $notification->data['nameprod'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Quantité:</div>
                                    <div class="text-gray-800">{{ $notification->data['quantiteC'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Lieu de livraison:</div>
                                    <div class="text-gray-800">{{ $notification->data['localite'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Spécificité:</div>
                                    <div class="text-gray-800">{{ $notification->data['specificite'] }}</div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Prix de la commande:</div>
                                    <div class="text-gray-800">
                                        {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                        Fcfa
                                    </div>

                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Somme finale:</div>
                                    @php
                                        $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                        $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                    @endphp
                                    <div class="text-gray-800">
                                        {{ number_format($sommeRecu, 2, ',', '.') }} Fcfa
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                        class="mb-3 text-blue-700 hover:underline flex items-center">
                        Voir le produit
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="ml-2 w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </a> --}}

                    <div class="mt-6 flex flex-col md:flex-row justify-end gap-4">
                        @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                            <div class="w-full bg-gray-300 border p-2 rounded-md">
                                <p class="text-md font-medium text-center">Réponse envoyée</p>
                            </div>
                        @elseif ($notification->type_achat == 'Take Away')
                            <button wire:click="takeaway"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                            <button wire:click="refuser" id="btn-refuser" type="submit"
                                class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        @elseif ($notification->type_achat == 'Reservation')
                            <button wire:click="takeaway"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                            <button wire:click="refuser" id="btn-refuser" type="submit"
                                class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        @else
                            <div x-data="{ isOpen: false, open: false, textareaValue: 'Emballage:..., Dimension:..., Poids:..., Autre:...' }" x-cloak>
                                <!-- Buttons to open modal and refuse -->
                                <button @click="isOpen = true"
                                    class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                                <button wire:click="refuser" id="btn-refuser" type="submit"
                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>

                                <!-- Modal -->
                                <div x-show="isOpen" id="hs-basic-modal"
                                    class="hs-overlay hs-overlay-open:opacity-100 hs-overlay-open:duration-500 fixed top-0 left-0 z-50 w-full h-full bg-black bg-opacity-50 overflow-y-auto">
                                    <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                        <div
                                            class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto">
                                            <div class="flex justify-between items-center py-3 px-4 border-b">
                                                <h3 class="font-bold text-gray-800">Envoi au livreur</h3>
                                                <button @click="isOpen = false"
                                                    class="text-gray-800 hover:text-gray-600">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="p-4">
                                                <p class="text-gray-800">
                                                    @if ($nombreLivr)
                                                        <div>
                                                            {{-- <h3>Détails de la commande</h3> --}}
                                                            {{-- <p><strong>ID de client :</strong> {{ $Idsender }}</p>
                                                            <p><strong>Continent du client :</strong>
                                                                {{ $clientContinent }}</p>
                                                            <p><strong>Sous-Region du client :</strong>
                                                                {{ $clientSous_Region }}</p>
                                                            <p><strong>Pays du client :</strong> {{ $clientPays }}
                                                            </p>
                                                            <p><strong>Departement du client :</strong>
                                                                {{ $clientDepartement }}</p>
                                                            <p><strong>Commune du client :</strong>{{ $clientCommune }}
                                                            </p> --}}
                                                            <p><strong>le nombre total disponible
                                                                    :</strong>{{ $livreursCount }}</p>

                                                            <h3>Liste des livreurs disponibles</h3>
                                                            @if ($livreurs->isEmpty())
                                                                <p>Aucun livreur disponible pour ce pays et cette ville.
                                                                </p>
                                                            @else
                                                                {{-- <ul>
                                                                    @foreach ($livreurs as $livreur)
                                                                        <li>
                                                                            Livreur ID: {{ $livreur->id }} -
                                                                            Expérience: {{ $livreur->experience }} ans
                                                                            - Livreur ID: {{ $livreur->user_id }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul> --}}
                                                            @endif
                                                        </div>
                                                    @else
                                                        Aucun livreur disponible dans la zone
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex justify-end items-center py-3 px-4 border-t">
                                                <div x-data="{ open: false }">
                                                    <!-- Button to toggle textarea visibility -->
                                                    <button @click="open = !open"
                                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                        Ajouter le nouveau Conditionnement
                                                    </button>

                                                    <!-- Textarea and action buttons -->
                                                    <div x-show="open" class="mt-4">
                                                        <textarea x-model="textareaValue" class="w-full p-2 border border-gray-300 rounded" rows="6" required>
                                                </textarea>
                                                        <div class="mt-2 flex justify-end space-x-2">
                                                            <button @click="open = false"
                                                                class="py-2 px-4 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded">
                                                                Annuler
                                                            </button>
                                                            <button @click.prevent="$wire.accepter(textareaValue)"
                                                                @if ($nombreLivr == 0) disabled @endif
                                                                class="py-2 px-4 bg-blue-600 text-white hover:bg-blue-700 rounded">
                                                                <span wire:loading.remove>Envoyer</span>
                                                                <span wire:loading>En cours...</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
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
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
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
                    startDate.setMinutes(startDate.getMinutes() + 1);


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
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
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
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">

                <h2 class="text-3xl font-semibold mb-2">{{ $produitfat->name }}</h2>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Quantité</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['quantite'] }}</p>
                </div>


                @php
                    $idProd = App\Models\ProduitService::find($notification->data['idProd']);
                    $continent = $idProd ? $idProd->continent : null;
                    $sous_region = $idProd ? $idProd->sous_region : null;
                    $pays = $idProd ? $idProd->pays : null;
                    $departement = $idProd ? $idProd->zonecoServ : null;
                    $ville = $idProd ? $idProd->villeServ : null;
                    $commune = $idProd ? $idProd->comnServ : null;
                @endphp

                <div class="w-full py-4 border-b-2">
                    <p class="text-md font-semibold mb-2">Lieu de récuperation / position geographique du produit</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-md font-medium text-gray-600 text-underline">Continent :</p>
                            <p class="text-md">{{ $continent }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Sous-région :</p>
                            <p class="text-md">{{ $sous_region }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Pays :</p>
                            <p class="text-md">{{ $pays }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Département :</p>
                            <p class="text-md">{{ $departement }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Ville :</p>
                            <p class="text-md">{{ $ville }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Commune :</p>
                            <p class="text-md">{{ $commune }}</p>
                        </div>
                    </div>
                </div>
                @php
                    $userSender = App\Models\User::find($notification->data['userSender']);
                    $continent = $userSender ? $userSender->continent : null;
                    $sous_region = $userSender ? $userSender->sous_region : null;
                    $pays = $userSender ? $userSender->country : null;
                    $departement = $userSender ? $userSender->departe : null;
                    $ville = $userSender ? $userSender->ville : null;
                    $commune = $userSender ? $userSender->commune : null;
                @endphp

                <div class="w-full py-4 border-b-2">
                    <p class="text-md font-semibold mb-2">Position geographique du client</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-md font-medium text-gray-600 text-underline">Continent :</p>
                            <p class="text-md">{{ $continent }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Sous-région :</p>
                            <p class="text-md">{{ $sous_region }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Pays :</p>
                            <p class="text-md">{{ $pays }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Département :</p>
                            <p class="text-md">{{ $departement }}</p>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-600">Ville :</p>
                            <p class="text-md">{{ $ville }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-md font-medium text-gray-600">Commune :</p>
                            <p class="text-md">{{ $commune }}</p>
                        </div> --}}
                    </div>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Lieu de livraison</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['localite'] }}</p>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Contact fournisseur</p>
                    <p class="text-md font-medium text-gray-600">{{ $userFour->phone }}</p>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Conditionnement du colis</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['textareaContent'] }}</p>
                </div>

                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                    class="mb-3 text-blue-700 hover:underline flex items-center">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="ml-2 w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="lg:col-span-1 col-span-2">
                <div id="prixTradeError" class="hidden text-red-500 mt-2"></div>

                <div class="p-4">

                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">

                            <!-- comments -->
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">

                                @foreach ($comments as $comment)
                                    <div class="flex items-center gap-3 relative">
                                        <img src="{{ asset($comment['photoUser']) }}" alt=""
                                            class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">
                                        <div class="flex-1">
                                            <p class=" text-base text-black font-medium inline-block dark:text-white">
                                                {{ $comment['nameUser'] }}</p>
                                            <p class="text-sm mt-0.5">
                                                {{ number_format($comment['prix'], 2, ',', ' ') }} FCFA</p>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- deux form  --}}
                            @if (is_array($nameSender))
                                <div id="prixTradeError" class="hidden text-red-500 mt-2"></div>

                                <form wire:submit.prevent="commentFormLivrGroup">

                                    <div
                                        class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                        <input type="hidden" name="code_livr" wire:model="code_livr"
                                            value="{{ $notification->data['code_livr'] }}">
                                        <input type="hidden" name="quantiteC" wire:model="quantiteC"
                                            value="{{ $notification->data['quantite'] }}">
                                        <input type="hidden" name="idProd" wire:model="idProd"
                                            value="{{ $notification->data['idProd'] }}">

                                        <input type="hidden" name="id_trader" wire:model="id_trader"
                                            value="{{ $notification->data['id_trader'] }}">
                                        <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Faire une offre..." required>
                                        {{--  --}}
                                        @foreach ($nameSender as $userId)
                                            <input type="hidden" name="nameSender[]"
                                                wire:model="nameSender.{{ $loop->index }}"
                                                value="{{ $userId }}">
                                        @endforeach

                                        <button type="submit" id="submitBtnAppel"
                                            class=" justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                            <!-- Button Text and Icon -->
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 18 20">
                                                    <path
                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                </svg>
                                            </span>
                                            <!-- Loading Spinner -->
                                            <span wire:loading>
                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                </svg>
                                                </svg>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <form wire:submit.prevent="commentFormLivr">

                                    <div
                                        class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                        <input type="hidden" name="code_livr" wire:model="code_livr"
                                            value="{{ $notification->data['code_livr'] }}">
                                        <input type="hidden" name="quantiteC" wire:model="quantiteC"
                                            value="{{ $notification->data['quantite'] }}">
                                        <input type="hidden" name="idProd" wire:model="idProd"
                                            value="{{ $notification->data['idProd'] }}">
                                        <input type="hidden" name="nameSender" wire:model="namesender"
                                            value="{{ $notification->data['userSender'] }}">
                                        <input type="hidden" name="id_trader" wire:model="id_trader"
                                            value="{{ $notification->data['id_trader'] }}">
                                        <input type="hidden" name="prixProd" id="prixProd"
                                            wire:model="prixProd" value="{{ $notification->data['prixProd'] }}">
                                        <input type="number" name="prixTrade" id="prixTrade"
                                            wire:model="prixTrade"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Faire une offre..." required>

                                        <button type="submit" id="submitBtnAppel"
                                            class=" justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                            <!-- Button Text and Icon -->
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 18 20">
                                                    <path
                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                </svg>
                                            </span>
                                            <!-- Loading Spinner -->
                                            <span wire:loading>
                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                </svg>
                                                </svg>
                                        </button>
                                    </div>
                                </form>


                            @endif

                        </div>

                    </div>

                    <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">
                        @if ($oldestCommentDate)
                            <span class=" mb-2">Temps restant pour cette negociatiation</span>

                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        @endif
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const prixTradeInput = document.getElementById('prixTrade');
                            const submitBtn = document.getElementById('submitBtnAppel');
                            const prixTradeError = document.getElementById('prixTradeError');


                            const startDate = new Date("{{ $oldestCommentDate }}");
                            startDate.setMinutes(startDate.getMinutes() + 1);

                            const countdownTimer = setInterval(updateCountdown, 1000);

                            function updateCountdown() {
                                const currentDate = new Date();
                                const difference = startDate.getTime() - currentDate.getTime();

                                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                                const countdownElement = document.getElementById('countdown');
                                if (countdownElement) {
                                    countdownElement.innerHTML = `
                                        <div>${hours}h</div>:
                                        <div>${minutes}m</div>:
                                        <div>${seconds}s</div>
                                    `;
                                }

                                if (difference <= 0) {
                                    clearInterval(countdownTimer);
                                    if (countdownElement) {
                                        countdownElement.innerHTML = "Temps écoulé !";
                                    }
                                    prixTradeInput.disabled = true;
                                    submitBtn.classList.add('hidden');


                                    const highestPricedComment = @json($comments).reduce((max, comment) => comment
                                        .prix > max.prix ? comment : max, {
                                            prix: -Infinity
                                        });

                                    if (highestPricedComment && highestPricedComment.nameUser) {
                                        prixTradeError.textContent =
                                            `Le livreur avec le meilleur prix  est ${highestPricedComment.nameUser} avec ${highestPricedComment.prix} FCFA !`;
                                    } else {
                                        prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                                    }
                                    prixTradeError.classList.remove('hidden');
                                }
                            }
                        });
                    </script>



                </div>

            </div>
        </div>
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
    @elseif ($notification->type === 'App\Notifications\mainleve')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-3">

            <h2 class="text-xl font-semibold mb-2">Information sur le produit à enlevé et livré</h2>

            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="mb-2">Nom du produit: <span class="font-semibold">{{ $produitfat->name }}</span></p>
                <p class="mb-2">Quantité: <span
                        class="font-semibold">{{ $notification->data['quantite'] }}</span>
                </p>
                <p class="mb-2">Code de livraison: <span
                        class="font-semibold">{{ $notification->data['code_unique'] }}</span></p>
                <p class="mb-2">Téléphone founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->phone }}</span>
                </p>
                <p class="mb-2">Email founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->email }}</span>
                </p>
                <p class="mb-2">Lieu d'enlevement: <span
                        class="font-semibold">{{ $namefourlivr->user->address }}</span>
                </p>
                <p class="mb-2">Lieu de livraison: <span
                        class="font-semibold">{{ $notification->data['localité'] }}</span></p>
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
                </h2>

                <div class="lg:w-1/2 w-full mr-2 relative">
                    <input type="date" id="datePickerStart" name="dateLivr" wire:model.defer="dateLivr"
                        required
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
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
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

        @if (session()->has('succes'))
            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-4">Information sur le livreur</h2>

                <div class=" w-full flex-col">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 mr-4 mb-6">

                        <img src="{{ asset($livreur->photo) }}" alt="photo" class="">

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
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
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
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
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
