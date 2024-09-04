
<div>
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
                                        <input type="hidden" name="id_sender[]" wire:model="id_sender.{{ $loop->index }}"
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
                                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                        </svg>
                                    </span>
                                    <span wire:loading>
                                        <svg class="w-5 h-5 animate-spin inline-block" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
