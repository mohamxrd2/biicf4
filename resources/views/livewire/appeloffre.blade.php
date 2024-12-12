<div>
    <div class="max-w-5xl mx-auto">

        <!-- Barre du haut avec timer -->
        <div class="flex justify-between items-center bg-gray-200 p-4 rounded-lg mb-6">
            <h1 class="text-lg font-bold">NEGOCIATION POUR LA LIVRAISON</h1>

            <div id="countdown-container" x-data="countdownTimer({{ json_encode($oldestCommentDate) }}, {{ json_encode($time) }})" class="flex items-center space-x-2">
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
            <div class="flex gap-8">
                <!-- Carte appeloffre -->
                <div class="bg-white flex-none rounded-lg shadow-md p-6 w-96 h-fit">


                    <h1 class="text-2xl font-bold mb-1">{{ $appeloffre->product_name }}</h1>
                    <!-- Nom du appeloffre -->
                    {{-- <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                        class="text-blue-700 hover:underline flex items-center">
                        Voir le appeloffre
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
                            <span>Prix Marchant : {{ $appeloffre->lowestPricedProduct }} FCFA</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Quantité : {{ $appeloffre->quantity }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>specification du produit : {{ $appeloffre->specification }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>reference du produit : {{ $appeloffre->reference }} unités </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Mode payement: {{ $appeloffre->payment }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Achat Avec: {{ $appeloffre->livraison }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                                📦
                            </div>
                            <span>Lieu de livraison: {{ $appeloffre->localite }}</span>
                        </div>
                        <span class="font-semibold">Date prévue de récupération :</span>
                        @if (isset($appeloffre->dateTot) && isset($appeloffre->dateTard))
                            {{ $appeloffre->dateTot }} - {{ $appeloffre->dateTard }}
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
                                {{ $appeloffre->continent }},
                                {{ $appeloffre->sous_region }},
                                {{ $appeloffre->pays }},
                                {{ $appeloffre->zonecoServ }},
                                {{ $appeloffre->villeServ }},
                                {{ $appeloffre->comnServ }}
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- Discussion -->
                <div class="flex-1 w-64">
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
                            <div class="flex flex-row items-center space-x-2 relative">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coc"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="text-white w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>

                                <!-- Tooltip -->
                                <div id="tooltip-coc" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Le prix le plus bas remportera la négociation. <br />
                                    Le gagnant recevra une notification.
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                <!-- Texte des participants -->
                                <p class="text-sm font-medium text-gray-100">{{ $nombreParticipants }} participants</p>
                            </div>
                        </div>

                        <!-- Section des commentaires -->
                        <div
                            class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-200 font-normal space-y-3 relative dark:border-slate-700/40">
                            <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                            @php
                                // Trouver le plus petit taux dans la liste des commentaires
                                $minPrice = $comments->min('prixTrade');

                                // Trouver le commentaire le plus ancien avec le taux minimal
                                $oldestMinPriceComment = $comments
                                    ->where('prixTrade', $minPrice)
                                    ->sortBy('created_at')
                                    ->first();
                            @endphp
                            @foreach ($comments as $comment)
                                <!-- Message -->
                                <div
                                    class="bg-gray-100 p-4 rounded-lg shadow-sm transition-transform transform hover:scale-105">
                                    <div class="flex items-start gap-3">
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
                                                Je peux descendre le prix a <span class="text-green-500 font-semibold">
                                                    {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                            </span>.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Offre et bouton -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                        </span>
                                        @if ($comment->id == $oldestMinPriceComment->id)
                                            <button
                                                class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium py-2 px-4 bg-green-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.588 4.89a1 1 0 00.95.69h5.127c.969 0 1.371 1.24.588 1.81l-4.15 3.02a1 1 0 00-.364 1.118l1.588 4.89c.3.921-.755 1.688-1.54 1.118l-4.15-3.02a1 1 0 00-1.176 0l-4.15 3.02c-.785.57-1.838-.197-1.539-1.118l1.588-4.89a1 1 0 00-.364-1.118L2.792 9.317c-.783-.57-.38-1.81.588-1.81h5.127a1 1 0 00.95-.69l1.588-4.89z" />
                                                </svg>
                                                Meilleure offre
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Champ pour proposer un nouveau prix -->
                        <div class="bg-gray-100 p-4 rounded-lg mt-4 shadow-sm">
                            <h4 class="text-sm font-bold mb-2 text-gray-800">Proposer un nouveau prix</h4>
                            <form wire:submit.prevent="commentFormLivr" id="commentForm">
                                @if (!$appeloffre->count)
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                            class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500"
                                            placeholder="Faire une offre..." required oninput="checkPrice()">
                                        <p id="errorMessage" class="text-red-500 text-sm mt-2 hidden">Votre offre
                                            dépasse le prix minimal acceptable.</p>

                                        <button type="submit" id="submitBtnAppel"
                                            class="p-3 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition duration-200">
                                            <span wire:loading.remove>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                            </span>

                                            <span wire:loading>
                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <script>
            function checkPrice() {
                const prixTrade = parseFloat(document.getElementById('prixTrade').value);
                const lowestPrice = parseFloat({{ $appeloffre->lowestPricedProduct }});
                const submitBtn = document.getElementById('submitBtnAppel');
                const errorMessage = document.getElementById('errorMessage');

                if (prixTrade > lowestPrice) {
                    submitBtn.style.display = 'none';
                    errorMessage.classList.remove('hidden');

                } else {
                    submitBtn.style.display = 'block';
                    errorMessage.classList.add('hidden');

                }
            }
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('countdownTimer', (oldestCommentDate, serverTime) => ({
                    oldestCommentDate: oldestCommentDate ? new Date(oldestCommentDate) : null,
                    serverTime: serverTime ? new Date(serverTime).getTime() :
                    null, // Heure du serveur initiale
                    localTimeAtLoad: Date.now(), // Temps local au moment du chargement
                    hours: '--',
                    minutes: '--',
                    seconds: '--',
                    endDate: null,
                    interval: null,
                    isCountdownActive: false, // Suivi de l'état du compte à rebours
                    hasSubmitted: false, // Évite les soumissions multiples

                    init() {


                        if (this.oldestCommentDate) {
                            this.endDate = new Date(this.oldestCommentDate);
                            this.endDate.setMinutes(this.endDate.getMinutes() + 5);
                            this.startCountdown();
                        }

                        Echo.channel('oldest-comment')
                            .listen('OldestCommentUpdated', (e) => {
                                console.log('Événement OldestCommentUpdated reçu', e);
                                if (e.oldestCommentDate) {
                                    const newDate = new Date(e.oldestCommentDate);

                                    if (!this.oldestCommentDate || this.oldestCommentDate.getTime() !==
                                        newDate.getTime()) {
                                        this.oldestCommentDate = newDate;
                                        this.endDate = new Date(this.oldestCommentDate);
                                        this.endDate.setMinutes(this.endDate.getMinutes() + 5);
                                        this.startCountdown();
                                        location.reload();
                                    } else {
                                        console.log('Le compte à rebours est déjà à jour.');
                                    }
                                } else {
                                    console.error('oldestCommentDate est null ou incorrect !', e);
                                }
                            });

                        console.log('Initialisation du oldestCommentDate', this.oldestCommentDate);
                        console.log('Initialisation du endDate', this.endDate);
                        console.log('Initialisation du serverTime', this.serverTime);
                    },

                    startCountdown() {
                        if (this.isCountdownActive) {
                            console.log('Le compte à rebours est déjà actif, pas de redémarrage.');
                            return;
                        }

                        if (this.interval) {
                            clearInterval(this.interval);
                        }

                        this.updateCountdown();
                        this.interval = setInterval(this.updateCountdown.bind(this), 1000);
                        this.isCountdownActive = true;
                    },

                    updateCountdown() {
                        const serverTime = this.serverTime; // Heure du serveur initiale
                        const elapsedTime = Date.now() - this.localTimeAtLoad; // Temps écoulé depuis le chargement
                        const currentDate = new Date(serverTime + elapsedTime); // Heure actuelle basée sur le serveur

                        const difference = this.endDate.getTime() - currentDate.getTime();
                        console.log('Initialisation du elapsedTime', elapsedTime);
                        console.log('Initialisation du currentDate', currentDate);
                        console.log('Initialisation du difference', difference);

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
                                this.hasSubmitted = true; // Empêcher la soumission multiple
                            }, 100);
                        }
                    },
                }));
            });
        </script>


    </div>
</div>
