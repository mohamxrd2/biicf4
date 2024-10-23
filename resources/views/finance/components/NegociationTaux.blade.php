<div x-data="countdownTimer({{ json_encode($projet->durer) }})" x-init="init" class="flex flex-col">
    <div class="border flex items-center justify-between border-gray-300 rounded-lg p-1 shadow-md">
        <div class="text-xl font-medium">Temps restant</div>
        <div id="countdown" class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg">
            @if (!$projet->count)
                <div class="flex items-center" x-show="!isFinished">
                    <div x-text="jours"></div>j
                    <span>:</span>
                    <div x-text="hours"></div>h
                    <span>:</span>
                    <div x-text="minutes"></div>m
                    <span>:</span>
                    <div x-text="seconds"></div>s
                </div>
                <div x-show="isFinished" class="text-red-600 font-bold">Temps écoulé !</div>
            @endif
        </div>
    </div>

    <!-- Afficher les messages d'erreur -->
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-2 mt-2 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
            <div
                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">


                @php
                    $tauxPresent = $projet->taux; // Récupérer le taux présent pour la comparaison

                    // Trouver le plus petit taux dans la liste des commentaires
                    $minTaux = $commentTauxList->min('taux');

                    // Trouver le commentaire le plus ancien avec le taux minimal
                    $oldestMinTauxComment = $commentTauxList->where('taux', $minTaux)->sortBy('created_at')->first();
                @endphp

                @if ($commentTauxList->isNotEmpty())
                    <div class="flex flex-col space-y-2">
                        @foreach ($commentTauxList as $comment)
                            <div class="flex items-center gap-3 relative rounded-xl bg-gray-200 p-2">
                                <img src="{{ asset($comment->investisseur->photo) }}" alt="Profile Picture"
                                    class="w-9 h-9 mt-1 rounded-full overflow-hidden object-cover">
                                <div class="flex-1">
                                    <p class="text-base text-black font-medium inline-block dark:text-white">
                                        {{ $comment->investisseur->name }}
                                        <!-- Afficher le nom de l'investisseur -->
                                    </p>
                                    <p class="text-sm mt-0.5">
                                        {{ $comment->taux }} % <!-- Afficher le taux -->
                                        @if ($comment->id == $oldestMinTauxComment->id)
                                            <!-- Afficher une étoile jaune pour le commentaire avec le plus ancien taux minimal -->
                                            <span class="text-yellow-500">★</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex h-full justify-center items-center">
                        <p class="text-md text-center text-gray-600">Aucun commentaire sur le taux
                            disponible.</p>
                    </div>

                @endif



            </div>

            <form wire:submit.prevent="commentForm" onsubmit="return validateTaux();">
                <div
                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                    @if (!$projet->count)
                        <input type="number" name="tauxTrade" id="tauxTrade" wire:model="tauxTrade"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Faire une offre..." required>
                        @error('tauxTrade')
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
                                <svg class="w-5 h-5 animate-spin inline-block" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                </svg>
                            </span>
                        </button>

                        <div id="errorMessage" class="text-red-500 mt-2 hidden"></div> <!-- Message d'erreur -->
                    @endif
                </div>
            </form>



        </div>

        <div class="w-full flex justify-center">
            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
        </div>
    </div>
</div>
<script>
    // Ajoutez un écouteur d'événements pour valider le taux lors de la saisie
    document.getElementById('tauxTrade').addEventListener('input', validateTaux);

    function validateTaux() {
        const tauxPresent = @json($tauxPresent); // Le taux déjà présent
        const tauxTradeInput = document.getElementById('tauxTrade');
        const submitBtn = document.getElementById('submitBtnAppel');
        const errorMessage = document.getElementById('errorMessage');

        // Vérifiez si le taux saisi est supérieur au taux présent
        if (parseFloat(tauxTradeInput.value) > parseFloat(tauxPresent)) {
            errorMessage.innerText = `Le taux ne peut pas être supérieur à ${tauxPresent}%.`;
            errorMessage.classList.remove('hidden');
            submitBtn.disabled = true; // Désactivez le bouton
            return false; // Empêche la soumission du formulaire
        } else {
            errorMessage.classList.add('hidden'); // Cachez le message d'erreur
            submitBtn.disabled = false; // Réactivez le bouton
            return true; // Permettre la soumission du formulaire
        }
    }



    document.addEventListener('alpine:init', () => {
        Alpine.data('countdownTimer', (projetDurer) => ({
            projetDurer: projetDurer ? new Date(projetDurer) : null,
            jours: '--',
            hours: '--',
            minutes: '--',
            seconds: '--',
            interval: null,
            isCountdownActive: false,
            isFinished: false,
            hasSubmitted: false, // Variable pour éviter la soumission multiple

            init() {
                if (this.projetDurer) {
                    this.startDate = new Date(this.projetDurer);

                    // Ajouter 5 minutes à la startDate
                    this.startDate.setMinutes(this.startDate.getMinutes() + 5);

                    this.startCountdown();
                }
            },

            startCountdown() {
                if (this.isCountdownActive) return; // Empêche le redémarrage si déjà actif

                this.clearExistingInterval();
                this.updateCountdown();
                this.interval = setInterval(this.updateCountdown.bind(this), 1000);
                this.isCountdownActive = true;
            },

            clearExistingInterval() {
                if (this.interval) {
                    clearInterval(this.interval);
                }
            },

            updateCountdown() {
                const currentDate = new Date();
                const difference = this.startDate - currentDate;

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
                this.clearExistingInterval();
                this.jours = this.hours = this.minutes = this.seconds = 0;
                this.isFinished = true;

                // Soumettre l'événement seulement une fois
                if (!this.hasSubmitted) {
                    setTimeout(() => {
                        Livewire.dispatch('compteReboursFini');
                        this.hasSubmitted = true; // Empêcher la soumission multiple
                    }, 100); // Petit délai pour laisser le temps à l'affichage de se mettre à jour
                }

                // Mettre à jour l'interface
                document.getElementById('countdown').innerText = "Temps écoulé !";
            },
        }));
    });
</script>
