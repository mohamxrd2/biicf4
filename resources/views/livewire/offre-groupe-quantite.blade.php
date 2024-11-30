<div>
    <!-- Conteneur principal -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Gestion des groupages de produits</h2>

        <div id="countdown-container" x-data="countdownTimer({{ json_encode($oldestCommentDate) }})" class="flex items-center space-x-2 justify-center">
            <!-- Timer avec un fond rouge clair et texte rouge -->
            <div id="countdown" x-show="oldestCommentDate"
                class="bg-red-200 text-red-600 font-bold px-6 py-3 rounded-lg flex items-center space-x-2">
                <!-- Affichage des heures, minutes et secondes -->
                <div x-text="hours" class="text-xl">--</div><span class="text-lg">j</span>
                <span>:</span>
                <div x-text="minutes" class="text-xl">--</div><span class="text-lg">m</span>
                <span>:</span>
                <div x-text="seconds" class="text-xl">--</div><span class="text-lg">s</span>
            </div>
        </div>

        <!-- Lien vers le produit -->
        <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
            class="text-blue-600 hover:text-blue-800 flex items-center mb-6">
            <span>Voir le produit</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="ml-2 w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
            </svg>
        </a>

        <!-- Détails -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <!-- Nombre de participants -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de participants</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $participants }}</p>
            </div>

            <!-- Premier fournisseur -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Premier Fournisseur</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $premierFournisseur->user->name }}</p>
            </div>

            <!-- Produit associé -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Produit</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $produit->name }} ({{ $produit->condProd }})</p>
            </div>

            <!-- Quantité totale ajoutée -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantité totale ajoutée</label>
                <p class="mt-2 text-lg font-semibold text-indigo-600">{{ $quantiteTotale }}</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form wire:submit.prevent="storeoffre" class="mb-8">
            <!-- Champ Quantité -->
            <div class="mb-6">
                <label for="quantite" class="block text-sm font-medium text-gray-700">Ajouter une quantité</label>
                <input type="number" id="quantite" name="quantite" wire:model="quantite"
                    class="mt-2 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Entrez la quantité" required min="1">
            </div>

            <!-- Bouton Ajouter le Groupage -->
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
                <!-- Exemple de groupage -->
                @foreach ($groupages as $groupage)
                    <div class="bg-gray-50 p-4 rounded-md shadow flex items-center justify-between">
                        <span class="text-gray-700 font-medium">
                            {{ $groupage->user->name ?? 'Utilisateur inconnu' }} - {{ $groupage->quantite }}
                            ({{ $produit->condProd }})
                        </span>
                        <button type="button" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
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
                    console.log('Initialisation du compteur', this.oldestCommentDate);

                    if (this.oldestCommentDate) {
                        this.startDate = new Date(this.oldestCommentDate);
                        this.startDate.setMinutes(this.startDate.getMinutes() +
                            40); // Ajout de 2 minutes pour le timer
                        this.startCountdown();
                    }
                },

                startCountdown() {
                    if (this.isCountdownActive) {
                        console.log('Le compte à rebours est déjà actif, pas de redémarrage.');
                        return; // Ne démarre pas un nouveau compte à rebours si un est déjà en cours
                    }

                    if (this.interval) {
                        clearInterval(this.interval);
                    }
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
