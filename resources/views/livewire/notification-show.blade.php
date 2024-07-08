<div>
    @if ($notification->type === 'App\Notifications\AchatGroupBiicf')

        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">

            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['nameProd'] }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
            <p class="mb-3"><strong>Prix totale:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa</p>
            @php
                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
            @endphp

            <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>

            <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
            <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise</p>


            <div class="flex gap-2">


                @if ($notification->reponse)
                    <div class="w-full bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <form wire:submit.prevent="accepterAGrouper">
                        @csrf
                        @foreach ($notification->data['userSender'] as $index => $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}"
                                wire:model="userSender.{{ $index }}">
                        @endforeach

                        <input type="text" name="montantTotal" value="{{ $notification->data['montantTotal'] }}"
                            wire:model="montantTotal">
                        <input type="text" name="idProd" value="{{ $notification->data['idProd'] }}"
                            wire:model="idProd">
                        <input type="text" name="message"
                            value="commande de produit en cours /Préparation à la livraison" wire:model="messageA">
                        <input type="text" name="notifId" value="{{ $notification->id }}" wire:model="notifId">

                        <!-- Bouton accepter -->
                        <button id="btn-accepter" type="submit"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                            Accepter
                        </button>
                    </form>

                    <form wire:submit.prevent="refuserAGrouper">
                        @csrf

                        @foreach ($notification->data['userSender'] as $index => $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}"
                                wire:model="userSender.{{ $index }}">
                        @endforeach

                        <input type="text" name="montantTotal" value="{{ $notification->data['montantTotal'] }}"
                            wire:model="montantTotal">
                        <input type="text" name="idProd" value="{{ $notification->data['idProd'] }}"
                            wire:model="idProd">
                        <input type="text" name="message" value="Achat refuser / Produits plus disponible"
                            wire:model="messageR">
                        <input type="text" name="notifId" value="{{ $notification->id }}" wire:model="notifId">

                        <button id="btn-refuser" type="submit"
                            class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">
                            Refuser
                        </button>
                    </form>

                @endif

            </div>

        </div>
    @elseif ($notification->type === 'App\Notifications\AchatBiicf')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['nameProd'] }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
            <p class="mb-3"><strong>Localité:</strong> {{ $notification->data['localite'] }}</p>
            <p class="mb-3"><strong>Spécificité:</strong> {{ $notification->data['specificite'] }}</p>
            <p class="mb-3"><strong>Prix d'artiche:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
            </p>

            @php
                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
            @endphp

            <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>
            <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

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
                            <form wire:submit.prevent="commentForm">
                                @csrf
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="code_unique" wire:model="code_unique"
                                        value="{{ $userComment->code_unique }}">
                                    <input type="hidden" name="id_trader" wire:model="id_trader"
                                        value="{{ $user->id }}">
                                    <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitBtnAppel"
                                        class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
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

                        <span class=" mb-2">Temps restant pour cette negociatiation</span>

                        <div id="countdown"
                            class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                            <div>-</div>:
                            <div>-</div>:
                            <div>-</div>
                        </div>

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
                            prixTradeInput.disabled = true; // Désactiver le champ input
                            document.getElementById('submitBtnAppel').hidden = true;
                            prixTradeError.textContent =
                                `Le fournisseur avec le prix le plus bas est {{ $lowPriceUserName }} avec {{ $lowPriceAmount }} FCFA!`;
                            prixTradeError.classList.remove('hidden');
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
                            <form action="{{ route('biicf.offgrpcomment') }}" method="post" id="commentForm">
                                @csrf
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="code_unique" value="{{ $codeUnique }}">
                                    <input type="hidden" name="id_trader" value="{{ $user->id }}">
                                    <input type="number" name="prixTrade" id="prixTrade"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>


                                    <button type="submit" id="submitBtn"
                                        class=" justify-center p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 18 20">
                                            <path
                                                d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                        </svg>
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


                    const highestPricedComment = @json($highestPricedComment);

                    if (highestPricedComment && highestPricedComment.user) {
                        prixTradeError.textContent =
                            `L'utilisateur avec le prix le plus bas est ${highestPricedComment.user.name} avec ${highestPricedComment.prixTrade} FCFA!`;
                    } else {
                        prixTradeError.textContent = "Aucun commentaire avec un prix trouvé.";
                    }
                    prixTradeError.classList.remove('hidden');
                }
            }
        </script>
    @elseif($notification->type === 'App\Notifications\AppelOffreTerminer')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['name'] }}</h2>

            <p class="mb-3"><strong>Quantité demander:</strong> {{ $notification->data['quantite'] }}
            </p>

            @php
                $prixArticle = $notification->data['quantite'] * $notification->data['prix_trade'];

            @endphp


            <p class="mb-3"><strong>Prix d'artiche:</strong> {{ $prixArticle }} Fcfa
            </p>

            <p class=" font-medium text-sm text-green-600 mb-4">Payement effectué avec succès :)</p>

            <a href="{{ route('biicf.wallet') }}"
                class="mb-3 text-white bg-purple-600 hover:bg-purple-800 text-center py-2 rounded-xl flex justify-center">
                Voir le porte-feuille
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>


        </div>
    @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">

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

            <form action="{{ route('biicf.addquantity') }}" method="POST">
                @csrf
                <div class="flex">
                    <input type="number"
                        class="py-3 px-4 block w-full mr-3 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Ajouter une quantité" name="quantite" id="quantiteInput" required>
                    <input type="hidden" name="name" value="{{ $notification->data['produit_name'] }}">
                    <input type="hidden" name="produit_id" value="{{ $notification->data['produit_id'] }}">

                    <input type="hidden" name="code_unique" value="{{ $notification->data['code_unique'] }}">

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

            <p class="mb-3"><strong>Prix d'artiche:</strong>{{ number_format($prixArticleNegos, 2, ',', ' ') }} Fcfa
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
                    <form id="form-accepter" action="{{ route('biicf.offNAccept') }}" method="POST">
                        @csrf

                        <input type="hidden" value="{{ $prixArticleNegos }}" name="prixarticle">
                        <input type="hidden" value="{{ $notification->data['code_unique'] }}" name="code_unique">
                        <input type="hidden" value="{{ $notification->id }}" name="notifId">


                        <!-- Bouton accepter -->
                        <button id="btn-accepter" type="submit"
                            class="px-4 py-1 w-full text-white bg-green-500 rounded-xl hover:bg-green-700">
                            Accepter
                        </button>
                    </form>
                @endif

            </div>


        </div>
    @elseif ($notification->type === 'App\Notifications\livraisonVerif')
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">

                <h2 class="text-3xl font-semibold mb-2">{{ $produit->name }}</h2>

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

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Code de livrasion</p>
                    <p class="text-md font-medium text-gray-600">{{ $notification->data['code_livr'] }}</p>
                </div>

                <div class="w-full flex justify-between items-center py-4  border-b-2">
                    <p class="text-md font-semibold">Client a livrer </p>
                    <p class="text-md font-medium text-gray-600">{{ $nameSender->name }}</p>
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



                                @if ($commentCount == 0)

                                    <div class="w-full h-full flex items-center justify-center">
                                        <p class="text-gray-800"> Aucune offre n'a été soumise</p>
                                    </div>{{ $commentCount }}
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
                            <form wire:submit.prevent="commentFormLivr">

                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="code_livr" wire:model="code_livr"
                                        value="{{ $notification->data['code_livr'] }}">
                                    <input type="hidden" name="id_trader" wire:model="id_trader"
                                        value="{{ $user->id }}">
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
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                            </svg>
                                            </svg>
                                    </button>
                                </div>
                            </form>

                        </div>

                        <div class="w-full flex justify-center">
                            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                        </div>
                    </div>

                    {{-- <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">
                        @if ($oldestCommentDate)
                            <span class=" mb-2">Temps restant pour cette negociatiation</span>

                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100  p-3 rounded-xl w-auto">

                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        @endif
                    </div> --}}
                    <div>
                        @if (!$countdownStarted)
                            nada
                        @else
                            <div>
                                <p>Le compte à rebours a commencé !</p>
                                <p>Temps restant : <span id="time-remaining">{{ $timeRemaining }}</span></p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <script>
                document.addEventListener('livewire:load', function () {
                    @if($countdownStarted)
                        setInterval(function() {
                            @this.call('updateTimeRemaining');
                        }, 1000);
                    @endif
                });
            </script>

            {{-- <script>
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
                        prixTradeInput.disabled = true; // Désactiver le champ input
                        document.getElementById('submitBtnAppel').hidden = true;
                        prixTradeError.textContent =
                            `Le fournisseur avec le prix le plus bas est {{ $lowPriceUserName }} avec {{ $lowPriceAmount }} FCFA!`;
                        prixTradeError.classList.remove('hidden');
                    }

                }
            });
        </script> --}}
        </div>

    @endif


</div>
