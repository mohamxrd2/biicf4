<div>
    <!-- Image du projet -->
    <div class="flex flex-col justify-center items-center text-center bg-gray-200 p-4 rounded-lg mb-6">
        <h1 class="text-lg font-bold">DETAILS DE LA DEMANDE DE CREDIT</h1>
    </div>

    <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
        <!-- Images Section -->
        <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
            <div class="bg-white rounded-lg shadow-lg p-6 ">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
                    sur le client</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-gray-600 font-medium">Nom du client:</p>
                        <p class="text-gray-800">{{ $userDetails->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Email:</p>
                        <p class="text-gray-800">{{ $userDetails->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Numéro de
                            téléphone:</p>
                        <p class="text-gray-800">{{ $userDetails->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Cote de Crédit</p>
                        <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Adresse:</p>
                        <p class="text-gray-800">
                            {{ $userDetails->country }},{{ $userDetails->ville }},{{ $userDetails->departe }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 ">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
                    sur la demande de crédit</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-gray-600 font-medium">Montant demandé:
                        </p>
                        <p class="text-gray-800">
                            {{ $notification->data['montant'] }} FCFA</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Durée du crédit:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->duree }}
                            mois</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Taux du crédit:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->taux }} %
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Date debut:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->date_debut }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Date fin:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->date_fin }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Type de crédit:
                        </p>
                        <p class="text-gray-800">
                            {{ $demandeCredit->type_financement }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">heure debut:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->heure_debut }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">heure fin:
                        </p>
                        <p class="text-gray-800">{{ $demandeCredit->heure_fin }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600 font-medium">Motif du crédit:
                        </p>
                        <p class="text-gray-800">
                            {{ $demandeCredit->objet_financement }}</p>
                    </div>
                </div>
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
                <span class="ml-2 text-sm text-gray-500">{{ $demandeCredit->objet_financement }}</span>
            </div>



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
                            <span class="font-semibold text-lg">
                                {{ number_format($notification->data['montant'], 0, ',', ' ') }}
                                FCFA</span>
                            <span class="text-gray-500 text-sm">Reçu de
                                {{ number_format($demandeCredit->montant, 0, ',', ' ') }} FCFA </span>
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
                            src="{{ asset($userDetails->photo) }}" alt="">
                    </div>
                    <div class="ml-2 text-sm font-semibold">
                        <span class="font-medium text-gray-500 mr-2">De</span>{{ $userDetails->name }}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @if (session()->has('success'))
                    <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
                @endif
                @if (session()->has('error'))
                    <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
                @endif
                <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        Participer a la demande crédit
                    </h3>
                    <p class="text-gray-600 text-md mb-6">
                        Contribuez a la demande crédit pour l'aider à atteindre la somme souhaitée.
                    </p>
                    @if (isset($demandeCredit) && $demandeCredit->objet_financement === 'demande-directe')
                        <!-- Afficher un message si l'objet du financement est 'demande-directe' -->
                        <button id="showInputButton"
                            class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                            Ajouter un montant
                        </button>
                    @elseif ($notification->reponse == 'refuser')
                        <div class="text-red-600 font-bold">
                            Demande de crédit refusée.
                        </div>
                    @else
                        <div class="flex space-x-4">
                            @if ($pourcentageInvesti < 100)
                                <!-- Bouton Approuver -->
                                <button id="approveButton" wire:click="approuver({{ $notification->data['montant'] }})"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Approuver</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 text-white inline-block"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z"></path>
                                        </svg>
                                    </span>
                                </button>

                                <!-- Bouton Refuser -->
                                <button id="rejectButton" wire:click="refuser"
                                    class="w-full py-3 bg-red-600 hover:bg-red-700 transition-colors rounded-md text-white font-medium">
                                    Refuser
                                </button>
                            @else
                                <div class="text-green-600 font-bold">
                                    Demande de crédit approuvée.
                                </div>
                            @endif

                        </div>

                    @endif

                </div>
                <div id="inputDiv" class="mt-6 hidden">
                    <input type="number" id="montantInput"
                        class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                    <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est insuffisant</p>
                    <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le montant doit être
                        supérieur
                        ou égal à la somme restante</p>

                    <button id="confirmerButton"
                        class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4 relative"
                        wire:click="confirmer" wire:loading.attr="disabled">
                        <span wire:loading.remove>Confirmer le montant</span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5 text-white inline-block"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z"></path>
                            </svg>
                        </span>
                    </button>

                </div>
            </div>

        </div>

    </div>
    @if (isset($demandeCredit) && $demandeCredit->objet_financement === 'demande-directe')
        <script>
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

                //Récupérer la valeur directement sans modifications
                const montant = montantInput.value;

                //Vérifie si la valeur est vide
                if (montant.trim() === '') {
                    messageSolde.classList.add('hidden');
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true; // Désactiver le bouton si le montant est vide
                    return;
                }

                //Convertir en nombre flottant
                const montantFloat = parseFloat(montant);

                //Vérifiez si la conversion a fonctionné (montant est NaN si non valide)
                if (isNaN(montantFloat)) {
                    messageSolde.classList.remove('hidden');
                    messageSolde.innerText = 'Le montant saisi n\'est pas valide';
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true;
                    return;
                }

                //Vérifie si le montant saisi dépasse le solde
                if (montantFloat > solde) {
                    messageSolde.classList.remove('hidden');
                    messageSolde.innerText = 'Votre solde est insuffisant';
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true; // Désactive le bouton si le solde est insuffisant
                } else {
                    messageSolde.classList.add('hidden');

                    //Vérifie si le montant est supérieur à la somme restante
                    if (montantFloat > sommeRestante) {
                        messageSommeRestante.classList.remove('hidden');
                        messageSommeRestante.innerText = 'Le montant doit être inférieur ou égal à la somme restante';
                        confirmerButton.disabled = true; // Désactive le bouton si le montant est supérieur à la somme restante
                    } else {
                        messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante

                        //Vérifie si le montant est supérieur à zéro pour activer le bouton
                        confirmerButton.disabled = montantFloat <= 0; // Désactive le bouton si le montant est négatif ou zéro
                    }
                }
            }
        </script>
    @endif
</div>
