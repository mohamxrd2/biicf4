<div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">

    <div class="max-w-md bg-white shadow-md rounded-lg p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h11M9 21V3M21 21H9m12-11H9" />
            </svg>
            Informations
            sur le client
        </h2>
        <div class="space-y-4">
            <!-- Montant demandé -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c1.656 0 3-1.567 3-3.5S13.656 1 12 1s-3 1.567-3 3.5 1.344 3.5 3 3.5zm0 0v5m6 4c0-2.5-1.5-4-3-4s-3 1.5-3 4m6 0c0 1.657-1.791 3-4 3s-4-1.343-4-3" />
                    </svg>
                    <p class="text-gray-700 font-medium">Demandeur</p>
                </div>
                <p class="text-gray-800 font-semibold">
                    {{ $userDetails->name }}</p>
            </div>

            <!-- Email -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5V10H2v10h5M2 10l8-8m12 8l-8-8M8 21a4 4 0 004-4m0 4v-1a4 4 0 00-4-4H7M5 9h14" />
                    </svg>
                    <p class="text-gray-700 font-medium">Email</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $userDetails->email }}</p>
            </div>
            <!-- Email -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5V10H2v10h5M2 10l8-8m12 8l-8-8M8 21a4 4 0 004-4m0 4v-1a4 4 0 00-4-4H7M5 9h14" />
                    </svg>
                    <p class="text-gray-700 font-medium">Email</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $userDetails->email }}</p>
            </div>
            <!-- cote de credit -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5V10H2v10h5M2 10l8-8m12 8l-8-8M8 21a4 4 0 004-4m0 4v-1a4 4 0 00-4-4H7M5 9h14" />
                    </svg>
                    <p class="text-gray-700 font-medium">Cote de Crédit</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $crediScore->ccc }}</p>
            </div>
            <!-- Objectif -->
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16V9m8 8v-4m4-4H4m16 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-700 font-medium">Numéro de
                        téléphone</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $userDetails->phone }}</p>
            </div>
        </div>
    </div>


    <div class="max-w-md bg-white shadow-md rounded-lg p-6">
        @php
            use Carbon\Carbon;
            $date = Carbon::parse($demandeCredit->duree ?? $projet->durer); // Assurez-vous que duree est une date valide
            $date2 = Carbon::parse($demandeCredit->date_debut ?? $projet->created_at); // Assurez-vous que duree est une date valide
            $date3 = Carbon::parse($demandeCredit->date_fin ?? $projet->date_fin); // Assurez-vous que duree est une date valide
        @endphp
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h11M9 21V3M21 21H9m12-11H9" />
            </svg>
            Détails du Financement
        </h2>
        <div class="space-y-4">
            <!-- Montant demandé -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c1.656 0 3-1.567 3-3.5S13.656 1 12 1s-3 1.567-3 3.5 1.344 3.5 3 3.5zm0 0v5m6 4c0-2.5-1.5-4-3-4s-3 1.5-3 4m6 0c0 1.657-1.791 3-4 3s-4-1.343-4-3" />
                    </svg>
                    <p class="text-gray-700 font-medium">Montant demandé</p>
                </div>
                <p class="text-gray-800 font-semibold">
                    {{ number_format($demandeCredit->montant ?? $projet->montant, 0, ',', ' ') }} FCFA</p>
            </div>
            <!-- Durée -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16v1a4 4 0 004 4h4a4 4 0 004-4v-1M5 8h14m-3-3a4 4 0 11-8 0M5 8v11a4 4 0 004 4h6a4 4 0 004-4V8m-8 3v4m0 0H7m5 0h5" />
                    </svg>
                    <p class="text-gray-700 font-medium">Delai de remboursement</p>
                </div>
                <p class="text-gray-800 font-semibold"> {{ $this->joursRestants() }} jour(s)</p>
            </div>
            <!-- date de remboursement -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16v1a4 4 0 004 4h4a4 4 0 004-4v-1M5 8h14m-3-3a4 4 0 11-8 0M5 8v11a4 4 0 004 4h6a4 4 0 004-4V8m-8 3v4m0 0H7m5 0h5" />
                    </svg>
                    <p class="text-gray-700 font-medium">Date de remboursement</p>
                </div>
                <p class="text-gray-800 font-semibold"> {{ $date->isoFormat(' DD MMMM YYYY') }}</p>
            </div>
            <!-- Delai d'attente -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16v1a4 4 0 004 4h4a4 4 0 004-4v-1M5 8h14m-3-3a4 4 0 11-8 0M5 8v11a4 4 0 004 4h6a4 4 0 004-4V8m-8 3v4m0 0H7m5 0h5" />
                    </svg>
                    <p class="text-gray-700 font-medium">Delai d'attente</p>
                </div>
                <p class="text-gray-800 text-sm"> {{ $date2->isoFormat(' DD MMMM YYYY') }} -
                    {{ $date3->isoFormat(' DD MMMM YYYY') }}</p>
            </div>
            <!-- Taux d'intérêt -->
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5V10H2v10h5M2 10l8-8m12 8l-8-8M8 21a4 4 0 004-4m0 4v-1a4 4 0 00-4-4H7M5 9h14" />
                    </svg>
                    <p class="text-gray-700 font-medium">Taux d'intérêt</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $demandeCredit->taux ?? $projet->taux }}%</p>
            </div>
            <!-- Objectif -->
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16V9m8 8v-4m4-4H4m16 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-700 font-medium">Objectif</p>
                </div>
                <p class="text-gray-800 font-semibold">{{ $demandeCredit->objet_financement ?? $projet->name }}</p>
            </div>
        </div>
    </div>


</div>
