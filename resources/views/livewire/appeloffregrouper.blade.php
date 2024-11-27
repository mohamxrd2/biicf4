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


        <body class="bg-gray-100 text-gray-800">
            <div class="max-w-4xl mx-auto p-6">
                <!-- Card Container -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <!-- Header -->
                    <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Ajout de Quantité</h1>

                    <!-- Information Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <p><strong>Prix unitaire plus trouvé :</strong> {{ $appelOffreGroup->lowestPricedProduct }}
                            </p>
                            <p><strong>Quantité totale du groupage :</strong> {{ $sumquantite }}</p>
                            <p><strong>Mode de paiement choisi :</strong> {{ $appelOffreGroup->payment }}</p>
                            <p><strong>Livraison :</strong> Oui</p>
                            <p><strong>Spécificité :</strong> {{ $appelOffreGroup->specificity }}</p>
                            <p><strong>Date au plus tôt :</strong> {{ $appelOffreGroup->dateTot }}</p>
                            <p><strong>Date au plus tard :</strong> {{ $appelOffreGroup->dateTard }}</p>
                        </div>
                        <!-- Right Column (Form) -->
                        <div class="space-y-4">

                            <div class="flex flex-col space-y-4">
                                <input type="number" wire:model.defer="quantite"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    placeholder="Ajouter une quantité..." required>

                                <input type="text" wire:model.defer="localite"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    placeholder="Lieu De Livraison" required>
                            </div>


                            <button type="submit" id="submitBtn"
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
                    </div>

                    <!-- Time Remaining -->
                    @if ($datePlusAncienne)
                        <div id="countdown"
                            class="flex items-center gap-2 font-semibold mt-6 text-center text-red-600  bg-red-100 p-3 rounded-xl w-auto">
                            <div>-</div>:
                            <div>-</div>:
                            <div>-</div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="mt-6 text-center">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Participants : {{ $appelOffreGroupcount }}
                        </button>
                    </div>
                </div>

                <script>
                    const qteInput = document.getElementById('quantite');
                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date("{{ $datePlusAncienne }}");

                    // Ajout d'une minute à la date de départ
                    startDate.setMinutes(startDate.getMinutes() + 5);

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
