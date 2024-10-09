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
                {{-- <span class="ml-2 text-sm text-gray-500">{{ $projet->categorie }}</span> --}}
            </div>

            <!-- Titre du projet -->
            <a href="details.html">
                <h3 class="text-xl font-semibold text-gray-800 mt-2">
                    {{-- {{ $projet->name }} --}}
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
                            {{-- <span class="font-semibold text-lg"> {{ number_format($sommeInvestie, 0, ',', ' ') }} --}}
                                FCFA</span>
                            <span class="text-gray-500 text-sm">Reçu de
                                {{-- {{ number_format($projet->montant, 0, ',', ' ') }} FCFA </span> --}}
                        </div>

                        <!-- Nombre d'Investisseurs -->
                        <div class="flex flex-col text-center">
                            {{-- <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span> --}}
                            <span class="text-gray-500 text-sm">Investisseurs</span>
                        </div>

                        <!-- Jours Restants -->
                        <div class="flex flex-col text-center">
                            {{-- <span class="font-semibold text-lg">{{ $this->joursRestants() }}</span> --}}
                            <span class="text-gray-500 text-sm">Jours restants</span>
                        </div>

                        <!-- Progression -->
                        <div class="flex flex-col text-center">
                            {{-- <span class="font-semibold text-lg">{{ number_format($pourcentageInvesti, 2) }}%</span> --}}
                            <span class="text-gray-500 text-sm">Progression</span>
                        </div>
                    </div>
                </div>

                <div class="flex py-2 mt-2 items-center">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                        {{-- <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                            src="{{ asset($projet->demandeur->photo) }}" alt=""> --}}
                    </div>
                    <div class="ml-2 text-sm font-semibold">
                        {{-- <span class="font-medium text-gray-500 mr-2">De</span>{{ $projet->demandeur->name }} --}}
                    </div>
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
                    <button id="showInputButton"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                        Ajouter un montant
                    </button>
                    <button
                        class="w-full py-3 bg-gray-200 hover:bg-gray-300 transition-colors rounded-md text-black font-medium"
                        disabled>
                        Ceci est votre projet
                    </button>
                </div>
            </div>

        </div>

    </div>
    {{-- <div class="flex-col w-full max-w-7xl max-h-full">
        <!-- Modal content -->
        <div class=" bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="bg-gray-100">
                <div class="container mx-auto p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-semibold text-gray-800">Détails de la
                            demande de crédit</h1>
                        <button
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2"
                            data-modal-hide="extralarge-modal">Retour à la
                            liste</button>
                    </div> --}}

    <!-- Card de détails du client -->
    {{-- <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
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
                    </div> --}}

    <!-- Card de détails de la demande de crédit -->
    {{-- <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
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
                    </div> --}}

    <!-- Actions -->
    {{-- <div class="flex justify-end space-x-4">
                        <button wire:click = "sendCredit"
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5"
                            data-modal-hide="extralarge-modal">Approuver</button>
                        <button
                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5"
                            data-modal-hide="extralarge-modal">Rejeter</button>
                    </div> --}}
    {{-- </div>
            </div>
        </div>
    </div> --}}
</div>
