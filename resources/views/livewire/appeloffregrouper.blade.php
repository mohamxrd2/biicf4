<div>
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


                                    <button type="submit" id="submitBtn"
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
                    startDate.setMinutes(startDate.getMinutes() + 2);

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

                        /// Arrêter le compte à rebours lorsque la date cible est atteinte
                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            if (countdownElement) {
                                countdownElement.innerHTML = "Temps écoulé !";
                            }
                            const submitBtn = document.getElementById('submitBtn');
                            if (submitBtn) {
                                submitBtn.hidden = true;
                            }
                            if (qteInput) {
                                qteInput.disabled = true;
                            }
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</div>
