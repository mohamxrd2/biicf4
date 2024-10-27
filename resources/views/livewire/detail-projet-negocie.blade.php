<div>
    @if (
        $projet->type_financement == 'négocié' &&
            isset($projet->montant) &&
            !isset($projet->Portion_action) &&
            !isset($projet->Portion_obligt))
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
                    <!-- Le propriétaire voit toujours Obligation -->
                    @include('finance.components.Action&Obligation')
                @else
                    @if ($pourcentageInvesti <= 100)
                        <!-- Non financé à 100 % : Tous les utilisateurs (autres que le propriétaire) voient la partie de négociation -->
                        @include('finance.components.NegociationTaux')
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
            @if ($projet->id_user != Auth::id())
                <div class="w-full md:w-1/2 mt-4 px-6">
                    <!-- Catégorie du projet -->
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                        <span class="ml-2 text-sm text-gray-500">{{ $projet->categorie }}</span>
                    </div>

                    <!-- Titre du projet -->
                    <a href="details.html">
                        <h3 class="text-xl font-semibold text-gray-800 mt-2">
                            {{ $projet->name }}
                        </h3>
                    </a>

                    <div class="mt-4">
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
                    </div>

                    <div class="flex py-2 mt-2 items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                            <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                                src="{{ asset($projet->demandeur->photo) }}" alt="">
                        </div>
                        <div class="ml-2 text-sm font-semibold">
                            <span class="font-medium text-gray-500 mr-2">De</span>{{ $projet->demandeur->name }}
                        </div>
                        <!-- Bouton de déclenchement du modal -->
                        <button data-modal-target="static-modal" data-modal-toggle="static-modal"
                            class="block bg-gray-200 mt-2 hover:bg-blue-800 text-blue-800 hover:text-white font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button">
                            Plus d'informations
                        </button>
                    </div>

                    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <!-- Modal header -->
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Projet Details
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="static-modal">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-4 md:p-5 space-y-4">
                                    <!-- Client Information -->
                                    <div class="bg-white rounded-lg shadow-lg p-8">
                                        <h2 class="text-2xl font-bold mb-6 text-gray-800">Client Information</h2>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <p class="text-gray-600 font-medium">Nom du client:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Email:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->email }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Numéro de
                                                    téléphone:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->phone }}</p>
                                            </div>
                                            {{-- <div>
                                        <p class="text-gray-600 font-medium">Cote de Crédit</p>
                                        <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                                    </div> --}}
                                            <div>
                                                <p class="text-gray-600 font-medium">Adresse:</p>
                                                <p class="text-gray-800">
                                                    {{ $projet->demandeur->country }},{{ $projet->demandeur->ville }},{{ $projet->demandeur->departe }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit Request Information -->
                                    <div class="bg-white rounded-lg shadow-lg p-8">
                                        <h2 class="text-2xl font-bold mb-6 text-gray-800">Credit Request Information
                                        </h2>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <p class="text-gray-600 font-medium">Montant demandé:
                                                </p>
                                                <p class="text-gray-800">
                                                    {{ $projet->montant }} FCFA</p>
                                            </div>
                                            {{-- <div>
                                        <p class="text-gray-600 font-medium">Durée du crédit:
                                        </p>
                                        <p class="text-gray-800">{{ $demandeCredit->duree }}
                                            mois</p>
                                    </div> --}}
                                            <div>
                                                <p class="text-gray-600 font-medium">Taux du crédit:
                                                </p>
                                                <p class="text-gray-800">{{ $projet->taux }} %
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-gray-600 font-medium">Date fin:
                                                </p>
                                                <p class="text-gray-800">{{ $projet->durer }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Type de crédit:
                                                </p>
                                                <p class="text-gray-800">
                                                    {{ $projet->type_financement }}</p>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    @elseif ($projet->type_financement == 'négocié' && isset($projet->Portion_action) && isset($projet->Portion_obligt))
        <h1>projet negocie avec obligation & action</h1>

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
                    <!-- Le propriétaire voit toujours Obligation -->
                    @include('finance.components.Action&Obligation')
                @else
                    @if ($pourcentageInvesti <= 100)
                        <!-- Non financé à 100 % : Tous les utilisateurs (autres que le propriétaire) voient la partie de négociation -->
                        @include('finance.components.NegociationTaux')
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

        
    </script>
</div>
