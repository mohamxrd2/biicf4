<div class="max-w-5xl mx-auto">


    <!-- Barre du haut avec timer -->
    <div class="flex justify-between items-center bg-gray-200 p-4 rounded-lg mb-6">
        <h1 class="text-lg font-bold">NEGOCIATION POUR LA LIVRAISON</h1>

        <div x-data="countdownTimer({{ json_encode($oldestCommentDate) }})" class="flex items-center space-x-2">
            <div class="flex items-center justify-between p-1 border border-gray-300 rounded-lg shadow-md">
                <div  class="text-xl font-medium">Temps restant</div>
                <div id="countdown"
                    class="flex items-center px-4 py-2 font-bold text-red-600 bg-red-200 rounded-lg">
                    <div x-text="jours">--</div>j
                    <span>:</span>
                    <div x-text="hours">--</div>h
                    <span>:</span>
                    <div x-text="minutes">--</div>m
                    <span>:</span>
                    <div x-text="seconds">--</div>s
                </div>
            </div>
        </div>
    </div>
    <div class="flex">
        @php
            $idProd = App\Models\ProduitService::find($notification->data['idProd']);
            $continent = $idProd ? $idProd->continent : null;
            $sous_region = $idProd ? $idProd->sous_region : null;
            $pays = $idProd ? $idProd->pays : null;
            $departement = $idProd ? $idProd->zonecoServ : null;
            $ville = $idProd ? $idProd->villeServ : null;
            $commune = $idProd ? $idProd->comnServ : null;
        @endphp
        <div class="flex-none gap-4 w-96">
            <!-- Informations sur le produit -->
            <div class="p-6 border-r bg-white border-gray-200">
                <!-- Image -->
                <div class="mb-6">
                    <img src="{{ asset('post/all/' . $notification->data['photoProd1']) }}" alt="Smart Watch Pro X1"
                        class="w-full rounded-lg object-cover" />
                </div>

                <!-- Nom du produit -->
                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                    class="text-blue-700 hover:underline flex items-center">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="ml-2 w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold mb-4">{{ $idProd->name }}</h1>

                <!-- Détails principaux -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                            📦
                        </div>
                        <span>Quantité : {{ $notification->data['quantite'] }} unités </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                            📦
                        </div>
                        <span>Conditionnement du colis: {{ $notification->data['textareaContent'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                            📦
                        </div>
                        <span>Lieu de livraison: {{ $notification->data['localite'] }}</span>
                    </div>
                    <span class="font-semibold">Date prévue de récupération :</span>
                    @if (isset($notification->data['dateTot']) && isset($notification->data['dateTard']))
                        {{ $notification->data['dateTot'] }} - {{ $notification->data['dateTard'] }}
                    @else
                        Non spécifiée
                    @endif
                    {{-- <div class="flex items-center gap-2">
                        <div class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                            💶
                        </div>
                        <span>Prix de référence: 900€/unité</span>
                    </div> --}}

                    <div class="flex items-center gap-2">
                        <div class="h-5 w-5 text-gray-600 bg-gray-200 rounded-full flex items-center justify-center">
                            ⏱️
                        </div>
                        <span>Délai de livraison: 10 jours</span>
                    </div>
                </div>
                @php
                    $userSenderId = $notification->data['userSender'];

                    if ($userSenderId) {
                        $userSender = App\Models\User::find($userSenderId);
                    } else {
                        // Gestion de l'erreur si aucun ID utilisateur n'est trouvé
                        Log::error('ID de l\'utilisateur manquant dans la notification.', $notification->data);
                        $userSender = null;
                    }
                    $continent = $userSender ? $userSender->continent : null;
                    $sous_region = $userSender ? $userSender->sous_region : null;
                    $pays = $userSender ? $userSender->country : null;
                    $departement = $userSender ? $userSender->departe : null;
                    $ville = $userSender ? $userSender->ville : null;
                    $commune = $userSender ? $userSender->commune : null;
                @endphp
                <!-- Spécifications -->
                <div class="mt-6">
                    <h2 class="font-semibold mb-2">Lieu de récupération:</h2>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>{{ $continent }}, {{ $sous_region }}, {{ $pays }}, {{ $departement }},
                            {{ $ville }}, {{ $commune }}</li>
                        <li>{{ $departement }},
                            {{ $ville }}, {{ $commune }}</li>
                    </ul>
                </div>
                <!-- Spécifications -->
                <div class="mt-6">
                    <h2 class="font-semibold mb-2">Position géographique du client:</h2>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>{{ $continent }}, {{ $sous_region }}, {{ $pays }}</li>
                        <li> {{ $departement }},
                            {{ $ville }}, {{ $commune }}
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="flex-1  w-64">
            <!-- Discussion de négociation -->
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">Discussion de négociation</h3>
                <p class="text-sm text-gray-500 mb-4">3 participants</p>
                <!-- comments -->
                <div
                    class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">
                    @foreach ($comments as $comment)
                        <!-- Message du Fournisseur C -->
                        <div class="bg-gray-50 p-3 rounded-lg mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-sm">Fournisseur C</span>
                                <span class="text-xs text-gray-400">10:30</span>
                            </div>
                            <p class="text-sm mb-2">Je peux faire <span>800€ </span> la livraison.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">850€</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="flex items-center gap-2 text-green-500 hover:text-green-600 font-medium py-2 px-4 bg-green-50 rounded-lg shadow-sm hover:shadow-md transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.588 4.89a1 1 0 00.95.69h5.127c.969 0 1.371 1.24.588 1.81l-4.15 3.02a1 1 0 00-.364 1.118l1.588 4.89c.3.921-.755 1.688-1.54 1.118l-4.15-3.02a1 1 0 00-1.176 0l-4.15 3.02c-.785.57-1.838-.197-1.539-1.118l1.588-4.89a1 1 0 00-.364-1.118L2.792 9.317c-.783-.57-.38-1.81.588-1.81h5.127a1 1 0 00.95-.69l1.588-4.89z" />
                                        </svg>
                                        Meilleure offre
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


                <!-- Champ pour Proposer un Nouveau Prix -->
                <div class="bg-gray-100 p-4 rounded-lg mt-4">
                    <h4 class="text-sm font-bold mb-2">Proposer un nouveau prix</h4>
                    <form wire:submit.prevent="commentFormLivr">

                        <div
                            class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                            <input type="hidden" name="code_livr" wire:model="code_livr">
                            <input type="hidden" name="quantite" wire:model="quantite">
                            <input type="hidden" name="idProd" wire:model="idProd">
                            <input type="hidden" name="userSender" wire:model="userSender">
                            <input type="hidden" name="id_trader" wire:model="id_trader">
                            <input type="hidden" name="prixProd" id="prixProd" wire:model="prixProd">
                            <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                placeholder="Faire une offre..." required>

                            <button type="submit" id="submitBtnAppel"
                                class=" justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                <!-- Button Text and Icon -->
                                <span wire:loading.remove>
                                    <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                        <path
                                            d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                    </svg>
                                </span>
                                <!-- Loading Spinner -->
                                <span wire:loading>
                                    <svg class="w-5 h-5 animate-spin inline-block" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                    </svg>
                                    </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdownTimer', (oldestcomment) => ({
                oldestcomment: oldestcomment ? new Date(oldestcomment) : null,
                jours: '--',
                hours: '--',
                minutes: '--',
                seconds: '--',
                interval: null,

                init() {
                    if (this.oldestcomment) {
                        this.updateCountdown();
                        this.startCountdown();
                    }
                },

                startCountdown() {
                    this.interval = setInterval(() => {
                        this.updateCountdown();
                    }, 1000);
                },

                updateCountdown() {
                    const now = new Date();
                    const difference = this.oldestcomment - now;

                    if (difference <= 0) {
                        this.endCountdown();
                        return;
                    }

                    this.jours = Math.floor(difference / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((difference % (1000 * 60)) / 1000);
                },

                endCountdown() {
                    clearInterval(this.interval);
                    this.jours = this.hours = this.minutes = this.seconds = '00';
                    document.getElementById('countdown').innerText = "Temps écoulé !";
                },
            }));
        });
    </script>
</div>
