<div>
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
                startDate.setMinutes(startDate.getMinutes() + 2); // Ajouter 1 minute pour la date de départ

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
</div>
