<div
    class="grid grid-cols-3 gap-4 text-sm border border-gray-300 rounded-lg p-3 shadow-md text-gray-600  w-full justify-between">

    <!-- Montant de base (sans pourcentage) -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($projet->montant, 0, ',', ' ') }}
            FCFA</span>
        <span class="text-gray-500 text-sm">Capital Demandé (sans intérêt)</span>
    </div>

    <!-- Taux -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($projet->taux, 0, ',', ' ') }}%</span>
        <span class="text-gray-500 text-sm">Taux</span>
    </div>
    @php
        // Capital demandé
        $capital_demande = $projet->montant;

        // Taux d'intérêt
        $taux = $projet->taux;

        // Calcul du capital total incluant les intérêts
        $capital_total = $capital_demande * (1 + $taux / 100);
    @endphp
    <!-- Montant total (avec pourcentage) -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($capital_total, 0, ',', ' ') }}
            FCFA</span>
        <span class="text-gray-500 text-sm">Capital Total (avec intérêt)</span>
    </div>
    <!-- Investisseurs -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span>
        <span class="text-gray-500 text-sm">Investisseurs</span>
    </div>
    <!-- Periode de remboursement -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">XXXXXX</span>
        <span class="text-gray-500 text-sm">Periode de remboursement</span>
    </div>
    <!-- Jours restant -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ $this->joursRestants() }}</span>
        <span class="text-gray-500 text-sm">Jours restants</span>
    </div>

    <!-- ROI -->
    <div class="flex flex-col text-center text-gray-500">
        <span class="font-semibold text-lg">{{ $projet->name }}</span>
        <span class="text-gray-500 text-sm">Nom du Projet</span>
    </div>
    <!-- ROI -->
    <div class="flex flex-col text-center text-gray-500">
        <span class="font-semibold text-lg">{{ $projet->categorie }}</span>
        <span class="text-gray-500 text-sm">Categorie du projet</span>
    </div>
    <!-- ROI -->
    <div class="flex flex-col text-center text-red-500">
        <span class="font-semibold text-lg">{{ number_format($capital_total - $projet->montant, 0, ',', ' ') }}</span>
        <span class="text-red-500 text-sm">Retour sur Investissement (ROI)</span>
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
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Projet Details
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="static-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
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
