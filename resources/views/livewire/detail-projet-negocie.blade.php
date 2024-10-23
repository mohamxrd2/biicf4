<div>
    @if ($projet->type_financement == 'négocié' && isset($projet->montant) && !isset($projet->Portion_action) && !isset($projet->Portion_obligt))
        <h1>projet negocie avec obligation..</h1>

        <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
            <!-- Images Section -->
            <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>

                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Contenu du projet -->
            <div class="md:px-4 flex flex-col w-full md:w-1/2 ">
                @if ($projet->id_user == Auth::id())
                    <!-- Vérification si l'utilisateur a déjà contribué -->

                    @include('finance.components.Obligation')
                @endif
                {{-- cette partie est dedié a la partie de negociation --}}
                @if ($projet->id_user != Auth::id())
                    @include('finance.components.NegociationTaux')
                @endif
            </div>
        </div>
        <div class="flex flex-col md:flex-row">
            <div class="flex flex-col w-full md:w-1/2">
                <h3 class="text-xl font-semibold text-gray-600 mt-2 mb-6">
                    Description du projet
                </h3>
                <p class="text-gray-500">
                    {{ $projet->description }}
                </p>
            </div>
            @if ($projet->id_user != Auth::id())
                <div class="w-full md:w-1/2 mt-4 px-6">
                    @include('finance.components.Obligation')
                </div>
            @endif

        </div>
    @elseif ($projet->type_financement == 'négocié' && isset($projet->Portion_action) && isset($projet->Portion_obligt))
        <h1>projet negocie avec obligation & obigation</h1>

        <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
            <!-- Images Section -->
            <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>

                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>
            <!-- Contenu du projet -->
            <div class="md:px-4 flex flex-col w-full md:w-1/2 ">
                @if ($projet->id_user == Auth::id())
                    <!-- Vérification si l'utilisateur a déjà contribué -->
                        <!-- Partie permettant d'ajouter un montant -->
                        @include('finance.components.Action&Obligation')
                @endif

                {{-- cette partie est dedié a la partie de negociation --}}
                @if ($projet->id_user != Auth::id())
                    @include('finance.components.NegociationTaux')
                @endif
            </div>
        </div>
        <div class="flex flex-col md:flex-row">
            <div class="flex flex-col w-full md:w-1/2">
                <h3 class="text-xl font-semibold text-gray-600 mt-2 mb-6">
                    Description du projet
                </h3>
                <p class="text-gray-500">
                    {{ $projet->description }}
                </p>
            </div>
            @if ($projet->id_user != Auth::id())
                <div class="w-full md:w-1/2 mt-4 px-6">
                    @include('finance.components.Action&Obligation')
                </div>
            @endif

        </div>

    @endif

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }

        document.getElementById('showMontantInputButton')?.addEventListener('click', function() {
            var montantInputDiv = document.getElementById('montantInputDiv');

            if (montantInputDiv) {
                montantInputDiv.classList.toggle('hidden'); // Basculer l'affichage de la section action
            } else {
                console.warn('L\'élément montantInputDiv n\'existe pas.');
            }
        });

        document.getElementById('showActionInputButton')?.addEventListener('click', function() {
            var actionInputDiv = document.getElementById('actionInputDiv');

            if (actionInputDiv) {
                actionInputDiv.classList.toggle('hidden'); // Basculer l'affichage de la section action
            } else {
                console.warn('L\'élément actionInputDiv n\'existe pas.');
            }
        });

        const solde = @json($solde);
        const sommeRestante = @json($sommeRestante); // Récupérer sommeRestante depuis le composant Livewire

        function verifierSolde() {
            const montantInput = document.getElementById('montantInput');
            const messageSolde = document.getElementById('messageSolde');
            const messageSommeRestante = document.getElementById('messageSommeRestante');
            const confirmerButton = document.getElementById('confirmerButton');

            // Désactive le bouton de confirmation si sommeRestante est égale à 0
            if (sommeRestante === 0) {
                messageSommeRestante.classList.remove('hidden');
                messageSommeRestante.innerText = 'La somme demandé est totalement collecté';
                confirmerButton.disabled = true;
                return;
            }

            // Récupérer la valeur directement sans modifications
            const montant = montantInput.value;

            // Vérifie si la valeur est vide
            if (montant.trim() === '') {
                messageSolde.classList.add('hidden');
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true; // Désactiver le bouton si le montant est vide
                return;
            }

            // Convertir en nombre flottant
            const montantFloat = parseFloat(montant);

            // Vérifiez si la conversion a fonctionné (montant est NaN si non valide)
            if (isNaN(montantFloat)) {
                messageSolde.classList.remove('hidden');
                messageSolde.innerText = 'Le montant saisi n\'est pas valide';
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true;
                return;
            }

            // Vérifie si le montant saisi dépasse le solde
            if (montantFloat > solde) {
                messageSolde.classList.remove('hidden');
                messageSolde.innerText = 'Votre solde est insuffisant';
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true; // Désactive le bouton si le solde est insuffisant
            } else {
                messageSolde.classList.add('hidden');

                // Vérifie si le montant est supérieur à la somme restante
                if (montantFloat > sommeRestante) {
                    messageSommeRestante.classList.remove('hidden');
                    messageSommeRestante.innerText = 'Le montant doit être inférieur ou égal à la somme restante';
                    confirmerButton.disabled = true; // Désactive le bouton si le montant est supérieur à la somme restante
                } else {
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante

                    // Vérifie si le montant est supérieur à zéro pour activer le bouton
                    confirmerButton.disabled = montantFloat <= 0; // Désactive le bouton si le montant est négatif ou zéro
                }
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
</div>
