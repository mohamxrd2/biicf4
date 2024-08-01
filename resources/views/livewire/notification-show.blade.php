<div>
    @if ($notification->type === 'App\Notifications\NegosTerminer')
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
                <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect"
                    class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">
                    @csrf
                    <div class="space-y-3 mb-3 w-full">
                        <input type="number" id="quantityInput" name="quantité" wire:model.defer="quantite"
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
                        <input type="text" id="specificite" name="specificite" wire:model.defer="specificite"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Specificité (Facultatif)">
                    </div>

                    <input type="hidden" name="nameProd" wire:model.defer="nameProd">
                    <input type="hidden" name="userSender" wire:model.defer="userTrader">
                    <input type="hidden" name="idProd" wire:model.defer="idProd">
                    <input type="hidden" name="prix" wire:model.defer="prix">

                    <div class="flex justify-between px-4 mb-3 w-full">
                        <p class="font-semibold text-sm text-gray-500">Prix total:</p>
                        <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
                        <input type="hidden" name="montantTotal" id="montant_total_input">
                    </div>

                    <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

                    <div class="w-full text-center mt-3">
                        <button type="reset"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé</button>
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
            document.addEventListener('DOMContentLoaded', function() {
                const btnAchatDirect = document.getElementById('btnAchatDirect');
                const formAchatDirect = document.getElementById('formAchatDirect');

                // Vous pouvez ajouter des écouteurs d'événements ou d'autres actions ici
                if (btnAchatDirect) {
                    btnAchatDirect.addEventListener('click', () => {
                        toggleVisibility();
                    });
                }
            });

            function toggleVisibility() {
                const contentDiv = document.getElementById('formAchatDirect');

                if (contentDiv.classList.contains('hidden')) {
                    contentDiv.classList.remove('hidden');
                    // Forcer le reflow pour activer la transition
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

            function updateMontantTotalDirect() {
                const quantityInput = document.getElementById('quantityInput');
                const price = parseFloat(quantityInput.getAttribute('data-price')) || 0;
                const minQuantity = parseInt(quantityInput.getAttribute('data-min'), 10);
                const maxQuantity = parseInt(quantityInput.getAttribute('data-max'), 10);
                const quantity = parseInt(quantityInput.value, 10);
                const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
                const montantTotalElement = document.getElementById('montantTotal');
                const errorMessageElement = document.getElementById('errorMessage');
                const submitButton = document.getElementById('submitButton');
                const montantTotalInput = document.getElementById('montant_total_input');

                const userBalance = parseFloat("{{ $userWallet->balance }}") || 0;

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
        </script>
    @elseif ($notification->type === 'App\Notifications\AchatBiicf')
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande</h1>
                </div>
                <div class="p-6">
                    <!-- Détails de la Commande -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Informations de la Commande</h2>
                        <ul>
                            <li class="flex justify-between py-1"><span class="font-medium">Numéro de Commande:</span>
                                <span>#12345</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Date de Commande:</span>
                                <span>2024-08-01</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Fournisseur:</span>
                                <span>XYZ Corp</span>
                            </li>
                            <li class="flex justify-between py-1"><span class="font-medium">Statut:</span> <span>En
                                    attente de validation</span></li>
                        </ul>
                    </div>

                    <!-- Liste des Produits -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Éléments de la Commande</h2>
                        <p class="my-3 text-sm text-gray-500">Vous serez débité de 10% sur le prix de la marchandise</p>

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
                                        <th class="py-2 px-4">Somme reçue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-2 px-4">{{ $notification->data['nameProd'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['quantité'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['localite'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['specificite'] }}</td>
                                        <td class="py-2 px-4">{{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
                                        </td>
                                        @php
                                            $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                            $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                        @endphp
                                        <td class="py-2 px-4">{{ number_format($sommeRecu, 2) }} Fcfa</td>
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
                                    <div class="text-gray-800">{{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-semibold text-gray-700">Somme reçue:</div>
                                    @php
                                        $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                        $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                    @endphp
                                    <div class="text-gray-800">{{ number_format($sommeRecu, 2) }} Fcfa</div>
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
                        @else
                            <div x-data="{ isOpen: false }" x-cloak>
                                <button @click="isOpen = true"
                                    class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                                <button wire:click="refuser" id="btn-refuser" type="submit"
                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>

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
                                                        Le nombre de livreurs disponibles dans cette zone est:
                                                        {{ $nombreLivr }}
                                                    @else
                                                        Aucun livreur dans la zone
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex justify-end items-center py-3 px-4 border-t">
                                                <button @click="isOpen = false"
                                                    class="py-2 px-4 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded mr-2">Annuler</button>
                                                <button @click.prevent="isOpen = false; $wire.accepter()"
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

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffre')
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
                    const submitBtn = document.getElementById(
                        'submitBtnAppel'); // Assurez-vous que l'ID du bouton correspond
                    const prixTradeError = document.getElementById('prixTradeError');

                    // Vérification du prix d'offre
                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);
                        const lowestPricedProduct = parseFloat(
                            '{{ $notification->data['lowestPricedProduct'] ?? 0 }}'
                        ); // Assurez-vous que la valeur est correcte

                        if (prixTradeValue > lowestPricedProduct) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = 'Le prix ne doit pas dépasser ' + lowestPricedProduct;
                            prixTradeError.classList.remove('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                        }
                    });

                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date(
                        "{{ $oldestCommentDate }}"); // Assurez-vous que le format de la date est correct

                    // Ajouter 1 minute à la date de départ (ou ajustez selon votre besoin)
                    startDate.setMinutes(startDate.getMinutes() + 1);

                    // Mettre à jour le compte à rebours à intervalles réguliers
                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        // Obtenir la date et l'heure actuelles
                        const currentDate = new Date();

                        // Calculer la différence entre la date cible et la date de départ en millisecondes
                        const difference = startDate.getTime() - currentDate.getTime();

                        // Convertir la différence en heures, minutes et secondes
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
                            submitBtn.hidden = true; // Cache le bouton après la fin du compte à rebours
                            prixTradeInput.hidden = true; // Cache le champ d'entrée après la fin du compte à rebours
                        }
                    }
                });
            </script>
        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
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

                                    <input type="hidden" name="specificite" id="specificite"
                                        wire:model="specificite" value="{{ $notification->data['specificity'] }}">


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

                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);
                        const lowestPricedProduct = parseFloat('{{ $notification->data['lowestPricedProduct'] }}');

                        if (prixTradeValue > lowestPricedProduct) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = 'Le prix ne doit pas dépasser ' + lowestPricedProduct;
                            prixTradeError.classList.remove('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                        }
                    });

                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date("{{ $oldestCommentDate }}");

                    // Ajouter 5 heures à la date de départ
                    startDate.setMinutes(startDate.getMinutes() + 1);

                    // Mettre à jour le compte à rebours à intervalles réguliers
                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        // Obtenir la date et l'heure actuelles
                        const currentDate = new Date();

                        // Calculer la différence entre la date cible et la date de départ en millisecondes
                        const difference = startDate.getTime() - currentDate.getTime();

                        // Convertir la différence en jours, heures, minutes et secondes
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

                        }

                    }
                });
            </script>
        </div>
    @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">

                <h2 class="text-3xl font-semibold mb-2">{{ $notification->data['produit_name'] }}</h2>

                <div class="w-full gap-y-2  my-4">

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Prix unitaire maximal</p>
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

                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
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



                                @if ($commentCount == 0)

                                    <div class="w-full h-full flex items-center justify-center">
                                        <p class="text-gray-800"> Aucune offre n'a été soumise</p>
                                    </div>
                                @else
                                    @foreach ($comments as $comment)
                                        <div class="flex items-center gap-3 relative">

                                            <img src="{{ asset($comment->user->photo) }}" alt=""
                                                class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">

                                            <div class="flex-1">
                                                <p
                                                    class=" text-base text-black font-medium inline-block dark:text-white">
                                                    {{ $comment->user->name }}</p>
                                                <p class="text-sm mt-0.5">
                                                    {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                            </div>
                                        </div>
                                    @endforeach


                                @endif

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
                    const submitBtn = document.getElementById('submitBtn');
                    const prixTradeError = document.getElementById('prixTradeError');

                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);
                        const produit_prix = parseFloat('{{ $notification->data['produit_prix'] }}');

                        if (prixTradeValue < produit_prix) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = 'Le prix  doit  etre superieur ' + produit_prix;
                            prixTradeError.classList.remove('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                        }
                    });
                });

                // Convertir la date de départ en objet Date JavaScript
                const startDate = new Date("{{ $oldestCommentDate }}");

                // Ajouter 5 jours à la date de départ
                // Ajouter 5 heures à la date de départ
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
                        document.getElementById('prixTrade').disabled = true;
                        document.getElementById('submitBtn').hidden = true;


                        // const highestPricedComment = ;

                        // if (highestPricedComment && highestPricedComment.user) {
                        //     prixTradeError.textContent =
                        //         `L'utilisateur avec le prix le plus bas est ${highestPricedComment.user.name} avec ${highestPricedComment.prixTrade} FCFA!`;
                        // } else {
                        //     prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                        // }
                        // prixTradeError.classList.remove('hidden');
                    }
                }
            </script>
        </div>
    @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['nameprod'] }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantiteC'] }}</p>
            <p class="mb-3"><strong>Localité:</strong> {{ $notification->data['localite'] }}</p>
            <p class="mb-3"><strong>Spécificité:</strong> {{ $notification->data['specificite'] }}</p>
            <p class="mb-3"><strong>Prix d'achat:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
            </p>

            @php
                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
            @endphp

            <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>
            {{-- <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a> --}}

            <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise
            </p>
            <div class="flex gap-2">
                @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                    <div class="w-full bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Reponse envoyé</p>

                    </div>
                @else
                    <div x-data="{ isOpen: false }" x-cloak>
                        <button @click="isOpen = true"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                            Accepter
                        </button>

                        <div x-show="isOpen" id="hs-basic-modal"
                            class="hs-overlay hs-overlay-open:opacity-100 hs-overlay-open:duration-500 fixed top-0 left-0 z-50 w-full h-full bg-black bg-opacity-50 overflow-y-auto">
                            <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                <div class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto">
                                    <div class="flex justify-between items-center py-3 px-4 border-b">
                                        <h3 class="font-bold text-gray-800">
                                            Envoie au livreur
                                        </h3>
                                        <button @click="isOpen = false" class="text-gray-800 hover:text-gray-600">
                                            <span class="sr-only">Close</span>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-gray-800">
                                            @if ($nombreLivr)
                                                Le nombre de livreur disponible dans cette zone est:
                                                {{ $nombreLivr }}
                                            @else
                                                Aucun livreur dans la zone
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex justify-end items-center py-3 px-4 border-t">
                                        <button @click="isOpen = false"
                                            class="py-2 px-4 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded mr-2">
                                            Annuler
                                        </button>
                                        <button @click.prevent="isOpen = false; $wire.accepter()"
                                            @if ($nombreLivr == 0) disabled @endif
                                            class="py-2 px-4 bg-blue-600 text-white hover:bg-blue-700 rounded">
                                            <span wire:loading.remove>
                                                Envoie
                                            </span>
                                            <span wire:loading>
                                                En cours...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>

                @endif
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

            <p class="mb-3"><strong>Prix de l'article:</strong>{{ number_format($prixArticleNegos, 2, ',', ' ') }}
                Fcfa
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
                    <button wire:click=''
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
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <header class="mb-9">
                <h1 class="text-3xl font-bold mb-4">Facture Proformat</h1>
                <div class="text-gray-600">
                    <p>Code la de Facture: <span
                            class="font-semibold">#{{ $notification->data['code_unique'] }}</span></p>
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
                            <td class="py-2 px-4 border-b">{{ $this->notification->data['prixProd'] }} FCFA</td>
                            <td class="py-2 px-4 border-b">
                                {{ (int) ($notification->data['quantiteC'] * $this->notification->data['prixProd']) }}
                                FCFA</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">Livraiveur: {{ $userFour->name }}</td>
                            <td class="py-2 px-4 border-b">1</td>
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
                            class="font-bold">{{ (int) ($notification->data['quantiteC'] * $this->notification->data['prixProd']) + $notification->data['prixTrade'] }}
                            FCFA</span></p>
                </div>
            </section>

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
                            class="font-semibold">#{{ $notification->data['code_unique'] }}</span></p>
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

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Lieu de recuperation</p>
                    <p class="text-md font-medium text-gray-600">{{ $userFour->address }}</p>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Lieu de livraison</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['localite'] }}</p>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Contact fournisseur</p>
                    <p class="text-md font-medium text-gray-600">{{ $userFour->phone }}</p>
                </div>

                {{-- <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Code de livrasion</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['code_livr'] }}</p>
                </div> --}}
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

                            {{-- deux form  --}}
                            @if (is_array($nameSender))
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
                                        <input type="hidden" name="prixProd" id="prixProd" wire:model="prixProd"
                                            value="{{ $notification->data['prixProd'] }}">
                                        <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
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
                    {{-- <script>
                        window.addEventListener('form-submitted', function() {
                            // Reload the page
                            location.reload();
                        });
                    </script> --}}
                    <script>
                        // Convertir la date de départ en objet Date JavaScript
                        const startDate = new Date("{{ $oldestCommentDate }}");

                        // Ajouter 5 jours à la date de départ
                        // Ajouter 5 heures à la date de départ
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
                                document.getElementById('prixTrade').disabled = true;
                                document.getElementById('submitBtn').hidden = true;
                            }
                        }
                    </script>


                </div>

            </div>
        </div>
    @elseif ($notification->type === 'App\Notifications\commandVerif')
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-2">Informations du fournisseur</h2>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="mb-2">Nom du fournisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->name }}</span></p>
                <p class="mb-2">Adresse du livreur: <span
                        class="font-semibold">{{ $namefourlivr->user->address }}</span>
                </p>
                <p class="mb-2">Email du founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->email }}</span>
                </p>
                <p class="mb-2">Téléphone founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->phone }}</span>
                </p>
            </div>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-3">
            <h2 class="text-xl font-semibold my-2">Avis de conformité</h2>

            <div class="flex mb-3">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600  disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-default-checkbox">
                <label for="hs-default-checkbox"
                    class="text-md text-gray-600 ms-3 dark:text-neutral-400">Quantité</label>
            </div>

            <div class="flex mb-3">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-checked-checkbox1">
                <label for="hs-checked-checkbox1" class="text-md text-gray-600 ms-3 dark:text-neutral-400">Qualité
                    apparante</label>
            </div>

            <div class="flex">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-checked-checkbox">
                <label for="hs-checked-checkbox"
                    class="text-md text-gray-600 ms-3 dark:text-neutral-400">Diversité</label>
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
                <p class="mb-2">Quantité: <span class="font-semibold">{{ $notification->data['quantite'] }}</span>
                </p>
                <p class="mb-2">Code de livraison: <span
                        class="font-semibold">{{ $notification->data['code_unique'] }}</span></p>
                <p class="mb-2">Téléphone founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->phone }}</span>
                </p>
                <p class="mb-2">Email founisseur: <span
                        class="font-semibold">{{ $namefourlivr->user->email }}</span></p>
                <p class="mb-2">Lieu d'enlevement: <span
                        class="font-semibold">{{ $namefourlivr->user->address }}</span>
                </p>
                <p class="mb-2">Lieu de livraison: <span
                        class="font-semibold">{{ $notification->data['localité'] }}</span></p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">

            <h2 class="text-xl font-semibold mb-2">Avis de conformité</h2>

            <div class="flex mb-3">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600  disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-default-checkbox">
                <label for="hs-default-checkbox"
                    class="text-md text-gray-600 ms-3 dark:text-neutral-400">Quantité</label>
            </div>

            <div class="flex mb-3">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-checked-checkbox1">
                <label for="hs-checked-checkbox1" class="text-md text-gray-600 ms-3 dark:text-neutral-400">Qualité
                    apparante</label>
            </div>

            <div class="flex">
                <input type="checkbox"
                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                    id="hs-checked-checkbox">
                <label for="hs-checked-checkbox"
                    class="text-md text-gray-600 ms-3 dark:text-neutral-400">Diversité</label>
            </div>

        </div>

        <form wire:submit.prevent="departlivr" method="POST">
            @csrf

            <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
                <h2 class="text-xl font-semibold mb-2">Estimation de date de livraison <span
                        class="text-red-700">*</span>
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
                        <option selected disabled>Choisir matin ou le soir</option>
                        <option value="Matin">Matin</option>
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

                    <button wire:click='refuseVerif'
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
                                class=" font-semibold">{{ $livreur->name }}</span></p>
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

                <div class="flex mb-3">
                    <input type="checkbox"
                        class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600  disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                        id="hs-default-checkbox">
                    <label for="hs-default-checkbox"
                        class="text-md text-gray-600 ms-3 dark:text-neutral-400">Quantité</label>
                </div>

                <div class="flex mb-3">
                    <input type="checkbox"
                        class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                        id="hs-checked-checkbox1">
                    <label for="hs-checked-checkbox1"
                        class="text-md text-gray-600 ms-3 dark:text-neutral-400">Qualité
                        apparante</label>
                </div>

                <div class="flex">
                    <input type="checkbox"
                        class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                        id="hs-checked-checkbox">
                    <label for="hs-checked-checkbox"
                        class="text-md text-gray-600 ms-3 dark:text-neutral-400">Diversité</label>
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


    @endif


</div>
