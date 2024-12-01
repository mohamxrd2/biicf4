<div>
    <!-- Conteneur principal -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajout de Quantités</h2>

        <!-- Compte à rebours -->
        <div id="countdown-container" x-data="countdownTimer({{ json_encode($datePlusAncienne) }})" class="flex items-center justify-center mb-6">
            <div id="countdown" x-show="oldestCommentDate"
                class="bg-red-200 text-red-600 font-bold px-6 py-3 rounded-lg flex items-center space-x-3">
                <div class="flex items-center space-x-1">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h3m0 0h3m-3 0v3m0-3V9"></path>
                    </svg>
                    <div x-text="hours" class="text-xl">--</div><span class="text-sm">h</span>
                </div>
                <span>:</span>
                <div class="flex items-center space-x-1">
                    <div x-text="minutes" class="text-xl">--</div><span class="text-sm">m</span>
                </div>
                <span>:</span>
                <div class="flex items-center space-x-1">
                    <div x-text="seconds" class="text-xl">--</div><span class="text-sm">s</span>
                </div>
            </div>
        </div>

        <!-- Détails -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de participants</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroupcount }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre total</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $sumquantite }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Mode de paiement choisi</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->payment }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Produit</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->specificity }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date début</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->dateTot }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date fin</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->dateTard }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Prix unitaire Max trouvé</label>
                <p class="mt-2 text-lg font-semibold text-indigo-600">{{ $appelOffreGroup->lowestPricedProduct }}</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form wire:submit.prevent="storeoffre" class="mb-8">
            <div class="mb-6">
                <label for="quantite" class="block text-sm font-medium text-gray-700">Ajouter une quantité</label>
                <input type="number" wire:model.defer="quantite"
                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Ajouter une quantité..." required>
                <input type="text" wire:model.defer="localite"
                    class="py-3 px-4 mt-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Lieu De Livraison" required>
            </div>
            <div class="flex justify-start">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>+ Ajouter le groupage</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Chargement...
                    </span>
                </button>
            </div>
        </form>

        <!-- Groupages existants -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Groupages existants</h3>
            <div class="space-y-4">
                @foreach ($groupages as $groupage)
                    <div class="bg-gray-50 p-6 rounded-md shadow flex items-center justify-between hover:bg-gray-100 transition-all">
                        <span class="text-gray-700 font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12H9m4 8h-4m5-16H5m5 16h-1"></path>
                            </svg>
                            <span>{{ $groupage->user->name ?? 'Utilisateur inconnu' }} - {{ $groupage->quantite }}
                                ()</span>
                        </span>
                        <button type="button" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdownTimer', (oldestCommentDate) => ({
                oldestCommentDate: oldestCommentDate ? new Date(oldestCommentDate) : null,
                hours: '--',
                minutes: '--',
                seconds: '--',
                startDate: null,
                interval: null,
                isCountdownActive: false,
                hasSubmitted: false,

                init() {
                    if (this.oldestCommentDate) {
                        this.startDate = new Date(this.oldestCommentDate);
                        this.startDate.setMinutes(this.startDate.getMinutes() + 40);
                        this.startCountdown();
                    }
                },

                startCountdown() {
                    if (this.isCountdownActive) return;
                    if (this.interval) clearInterval(this.interval);
                    this.updateCountdown();
                    this.interval = setInterval(this.updateCountdown.bind(this), 1000);
                    this.isCountdownActive = true;
                },

                updateCountdown() {
                    const currentDate = new Date();
                    const difference = this.startDate.getTime() - currentDate.getTime();

                    if (difference <= 0) {
                        clearInterval(this.interval);
                        this.endCountdown();
                        return;
                    }

                    this.hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((difference % (1000 * 60)) / 1000);
                },

                endCountdown() {
                    document.getElementById('countdown').innerText = "Temps écoulé !";
                    if (!this.hasSubmitted) {
                        setTimeout(() => {
                            Livewire.dispatch('compteReboursFini');
                            this.hasSubmitted = true;
                        }, 100);
                    }
                },
            }));
        });
    </script>
</div>
