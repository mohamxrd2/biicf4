<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

    <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
        <!-- Image du projet -->

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
        <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
            <!-- Catégorie du projet -->
            <div class="flex items-center mb-2">
                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
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

            <!-- Informations de progression -->
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvesti }}%"></div>
                </div>

                <div class="mt-4">


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
                            <span class="font-semibold text-lg">{{ $this->joursRestants() }}</span>
                            <span class="text-gray-500 text-sm">Jours restants</span>
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
                </div>
            </div>
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
        <div class="w-full md:w-1/2 mt-4 px-6">

            @if (session()->has('success'))
                <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
            @endif
            @if (session()->has('error'))
                <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
            @endif
            <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    Participer au financement du projet
                </h3>
                <p class="text-gray-600 text-md mb-6">
                    Contribuez au financement du projet pour l'aider à atteindre la somme souhaitée.
                </p>
                @if ($projet->id_user != Auth::id())
                    <button id="showInputButton"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                        Ajouter un montant
                    </button>
                @else

                <button 
                        class="w-full py-3 bg-gray-200 hover:bg-gray-300 transition-colors rounded-md text-black font-medium" disabled>
                        Ceci est votre projet
                    </button>

                @endif
            </div>

            <div id="inputDiv" class="mt-6 hidden">
                <input type="number" id="montantInput"
                    class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est insuffisant</p>
                <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le montant doit être supérieur
                    ou égal à la somme restante</p>

                <button id="confirmerButton" disabled
                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4"
                    wire:click="confirmer">
                    Confirmer le montant
                </button>
            </div>


        </div>
    </div>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
        document.getElementById('showInputButton').addEventListener('click', function() {
            var inputDiv = document.getElementById('inputDiv');
            inputDiv.classList.toggle('hidden'); // Basculer l'affichage
        });
        const solde = @json($solde);
        const sommeRestante = @json($sommeRestante); // Récupérer sommeRestante depuis le composant Livewire

        function verifierSolde() {
            const montantInput = document.getElementById('montantInput');
            const messageSolde = document.getElementById('messageSolde');
            const messageSommeRestante = document.getElementById('messageSommeRestante');
            const confirmerButton = document.getElementById('confirmerButton');

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
