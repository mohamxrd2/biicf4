<div>

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
                @if ($notification->data['timeStart'])
                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">heure au plus tôt</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['timeStart'] }}</p>
                    </div>
                @endif
                @if ($notification->data['timeEnd'])
                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">heure au plus tard</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['timeEnd'] }}</p>
                    </div>
                @endif
                @if ($notification->data['dayPeriod'])
                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">periode</p>
                        <p class="text-md font-medium text-gray-600">{{ $notification->data['dayPeriod'] }}</p>
                    </div>
                @endif

            </div>
            <a href="#" class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
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


                                <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    placeholder="Faire une offre..." required>
                                @error('prixTrade')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                <button type="submit" id="submitBtnAppel"
                                    class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                    <span wire:loading.remove>
                                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
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
                        prixTradeInput.disabled = true;
                        submitBtn.classList.add('hidden');

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
</div>
