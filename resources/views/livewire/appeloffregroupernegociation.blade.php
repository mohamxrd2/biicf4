<div>


    <div class="max-w-5xl mx-auto">
        <!-- Barre du haut avec timer -->
        <div class="flex justify-between items-center bg-gray-200 p-4 rounded-lg mb-6">
            <h1 class="text-lg font-bold">NEGOCIATION POUR LA LIVRAISON</h1>

            <div id="countdown-container" x-data="countdownTimer({{ json_encode($oldestCommentDate) }}, {{ json_encode($comments) }})" class="flex items-center space-x-2">
                <span x-show="oldestCommentDate" class="text-sm">Temps restant pour cette négociation:</span>

                <div id="countdown" x-show="oldestCommentDate"
                    class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                    <div x-text="hours">--</div>j
                    <span>:</span>
                    <div x-text="minutes">--</div>m
                    <span>:</span>
                    <div x-text="seconds">--</div>s
                </div>
            </div>
        </div>

        <div class="bg-gray-100 min-h-screen">
            <div class="flex flex-wrap gap-8 p-4">
                <!-- Carte appeloffregrp -->
                <div class="bg-white flex-none rounded-lg shadow-md p-6 w-full md:w-96 h-fit">
                    {{-- <div class="mb-4">
                        <img src="{{ asset('post/all/' . $notification->data['photoProd']) }}" alt="Smart Watch Pro X1"
                            class="w-full h-48 object-cover rounded-lg bg-gray-100" />
                    </div> --}}

                    <h1 class="text-2xl font-bold mb-1">{{ $appeloffregrp->product_name }}</h1>
                    <!-- Nom du appeloffregrp -->
                    {{-- <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                        class="text-blue-700 hover:underline flex items-center">
                        Voir le appeloffregrp
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="ml-2 w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </a> --}}

                    <!-- Détails principaux -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Prix Marchant : {{ $appeloffregrp->lowestPricedProduct }} FCFA</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Quantité : {{ $appeloffregrp->quantity }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>specification du produit : {{ $appeloffregrp->specification }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>reference du produit : {{ $appeloffregrp->reference }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Mode payement: {{ $appeloffregrp->payment }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Achat Avec: {{ $appeloffregrp->livraison }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Lieu de livraison: {{ $appeloffregrp->localite }}</span>
                        </div>
                        <span class="font-semibold">Date prévue de récupération :</span>
                        @if (isset($appeloffregrp->dateTot) && isset($appeloffregrp->dateTard))
                            {{ $appeloffregrp->dateTot }} - {{ $appeloffregrp->dateTard }}
                        @else
                            Non spécifiée
                        @endif


                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                ⏱️
                            </div>
                            <span>Délai de livraison: 10 jours</span>
                        </div>
                    </div>

                    <!-- Spécifications -->
                    <div class="mt-6">
                        <h2 class="font-semibold mb-2">Lieu de récupération:</h2>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <li>
                                {{ $appeloffregrp->continent }},
                                {{ $appeloffregrp->sous_region }},
                                {{ $appeloffregrp->pays }},
                                {{ $appeloffregrp->zonecoServ }},
                                {{ $appeloffregrp->villeServ }},
                                {{ $appeloffregrp->comnServ }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Discussion -->
                <div class="flex-1 w-full md:w-auto">
                    <!-- Discussion de négociation -->
                    <div class="bg-white shadow-lg rounded-lg p-2">
                        <!-- En-tête -->
                        <div
                            class="bg-gradient-to-r from-purple-500 to-blue-500 shadow-lg rounded-lg p-4 text-white mb-4">
                            <h3 class="text-2xl font-bold flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                </svg>
                                Discussion de négociation
                            </h3>
                        </div>

                        <!-- Section des commentaires -->
                        <div
                            class="h-[400px] overflow-y-auto sm:p-4 p-2 border-t border-gray-200 font-normal space-y-3 relative dark:border-slate-700/40">
                            <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                            @foreach ($comments as $comment)
                                <!-- Message -->
                                <div
                                    class="bg-gray-100 p-4 rounded-lg shadow-sm transition-transform transform hover:scale-105">
                                    <div class="flex flex-wrap items-start gap-3">
                                        <!-- Photo utilisateur -->
                                        <img src="{{ asset($comment->user->photo) }}" alt="Profile Picture"
                                            class="w-10 h-10 rounded-full object-cover shadow-md" />
                                        <div class="flex-1">
                                            <!-- Informations utilisateur -->
                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="font-semibold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                                <span class="text-xs text-gray-400">{{ $comment->created_at }}</span>
                                            </div>
                                            <!-- Message -->
                                            <p class="text-sm text-gray-600">
                                                Je peux faire <span class="text-green-500 font-semibold">
                                                    {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</span>
                                            </p> la livraison.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Offre et bouton -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
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
                                            wire:model="id_sender.{{ $loop->index }}" value="{{ $userId }}">
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
                                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 18 20">
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
                startDate.setMinutes(startDate.getMinutes() + 2);

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
</div>
