<div>
    @if (isset($projet->montant) && !isset($projet->Portion_action) && !isset($projet->Portion_obligt))
        <h1>projet groupe avec obligation</h1>

        <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
            <!-- Images Section -->
            <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>

                <!-- Titre du projet -->
                <a href="details.html">
                    <h3 class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $projet->name }}
                    </h3>
                </a>
                <!-- Informations de progression -->
                <div class="mt-4">
                    @if (!$projet->Portion_action & !$projet->Portion_obligt)
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvesti }}%">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
                            <!-- Montant Reçu -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg"> {{ number_format($sommeInvestie, 0, ',', ' ') }}
                                    FCFA</span>
                                <span class="text-gray-500 text-sm">Reçu de
                                    {{ number_format($projet->montant, 0, ',', ' ') }} FCFA </span>
                            </div>

                            <!-- Nombre d'Investisseurs -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span>
                                <span class="text-gray-500 text-sm">Investisseurs</span>
                            </div>

                            <!-- Jours Restants -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg">{{ $this->joursRestants() }} /
                                    {{ $projet->taux }}%</span>
                                <span class="text-gray-500 text-sm">Jours restants/ taux de remboursement</span>
                            </div>

                            <!-- Progression -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg">{{ number_format($pourcentageInvesti, 2) }}%</span>
                                <span class="text-gray-500 text-sm">Progression</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                    Participer au financement du projet
                                </h3>
                                <p class="text-gray-600 text-md mb-6">
                                    Contribuez au financement du projet pour l'aider à atteindre la somme souhaitée.
                                </p>
                                @if ($projet->id_user != Auth::id())
                                    <button id="showMontantInputButton"
                                        class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                                        Ajouter un montant
                                    </button>
                                @else
                                    <button
                                        class="w-full py-3 bg-gray-200 hover:bg-gray-300 transition-colors rounded-md text-black font-medium"
                                        disabled>
                                        Ceci est votre projet
                                    </button>
                                @endif
                            </div>

                            <div id="montantInputDiv" class="mt-6 hidden">
                                <p class="text-md mb-3 text-gray-700">Le montant restant est de : <span
                                        class="font-bold">
                                        {{ number_format($sommeRestante, 0, ',', ' ') }} FCFA</span></p>

                                <input type="number" id="montantInput"
                                    class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                                <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est
                                    insuffisant</p>
                                <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le montant
                                    doit être supérieur ou égal à la
                                    somme restante</p>

                                <button id="confirmer"
                                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4"
                                    wire:click="confirmer" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        Confirmer le montant
                                    </span>
                                    <span wire:loading>
                                        Chargement...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    @elseif (isset($projet->Portion_action) && isset($projet->Portion_obligt))
        <h1>projet groupe avec obligation & action</h1>

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
                @if ($pourcentageInvesti < 100)
                    <!-- Le projet n'est pas encore entièrement financé -->
                    @include('finance.components.Action&Obligation')
                @else
                    <!-- Si le projet est entièrement financé -->
                    @if ($investisseurQuiAPayeTout)
                        <!-- Vérifier si un investisseur a payé tout le montant -->
                        @if ($projet->id_user == Auth::id())
                            <!-- Le propriétaire voit Obligation -->
                            @include('finance.components.Action&Obligation')
                        @else
                            <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                            @include('finance.components.NegociationTaux')
                        @endif
                    @else
                        <!-- Si aucun investisseur n'a payé la totalité, tous les utilisateurs voient Obligation -->
                        @include('finance.components.Action&Obligation')
                    @endif

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
            @if ($projet->id_user != Auth::id() && $montantVerifie)
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
            const confirmerButton = document.getElementById('confirmer'); // Correction ici

            // Désactive le bouton de confirmation si sommeRestante est égale à 0
            if (sommeRestante === 0) {
                messageSommeRestante.classList.remove('hidden');
                messageSommeRestante.innerText = 'La somme demandée est totalement collectée';
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
    </script>
</div>
