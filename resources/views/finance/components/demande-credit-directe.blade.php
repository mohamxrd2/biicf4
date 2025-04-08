<div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
    <div class="flex items-center mb-2">
        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
        </svg>
        <span class="ml-2 text-sm text-gray-500">{{ $demandeCredit->objet_financement }}</span>
    </div>
    @php
        // Récupérer les données actuelles
        $montantTotal = $notification->data['montant'];
        $taux = $demandeCredit->taux ?? 0;

        // Calculer le montant sans pourcentage (montant de base)
        $montantDeBase = $montantTotal / (1 + $taux / 100);
    @endphp
    <div
        class="grid grid-cols-2 gap-4 text-sm border border-gray-300 rounded-lg p-3 shadow-md text-gray-600 mt-4 w-full justify-between">

        <!-- Montant de base (sans pourcentage) -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ number_format($montantDeBase, 0, ',', ' ') }}
                FCFA</span>
            <span class="text-gray-500 text-sm">Capital Demandé (sans intérêt)</span>
        </div>

        <!-- Taux -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ $taux }}%</span>
            <span class="text-gray-500 text-sm">Taux</span>
        </div>

        <!-- Montant total (avec pourcentage) -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ number_format($montantTotal, 0, ',', ' ') }}
                FCFA</span>
            <span class="text-gray-500 text-sm">Capital Total (avec intérêt)</span>
        </div>
        <!-- Montant total (avec pourcentage) -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ $demandeCredit->duree }}</span>
            <span class="text-gray-500 text-sm"> Fin de Remboursement</span>
        </div>
        <div class="sm:col-span-2">

            <!-- Montant total (avec pourcentage) -->
            <div class="flex flex-col text-center text-red-500">
                <span
                    class="font-semibold text-lg">{{ number_format($montantTotal - $montantDeBase, 0, ',', ' ') }}</span>
                <span class="text-red-500 text-sm">Retour sur Investissement (ROI)</span>
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
                Approuver la demande crédit
            </h3>
            <p class="text-gray-600 text-md mb-6">
                Contribuez a la finalisation de l'achat d'un produit.
            </p>

            <!-- Afficher un message si l'objet du financement est 'demande-directe' -->
            <div class="flex space-x-4">
                @if ($notification->reponse == 'approved')
                    <div class="text-green-600 font-bold">
                        Demande de crédit approuvée.
                    </div>
                @elseif ($notification->reponse == 'refuser')
                    <div class="text-red-600 font-bold">
                        Demande de crédit refusée.
                    </div>
                @elseif ($pourcentageInvesti < 100)
                    <!-- Bouton Approuver -->
                    <button id="approveButton" wire:click="approuver({{ $montantDeBase }})"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Approuver</span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
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
                        Demande de crédit terminé.
                    </div>
                @endif

            </div>
            <div id="inputDiv" class="mt-6 hidden">
                <input type="number" id="montantInput"
                    class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est
                    insuffisant</p>
                <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le
                    montant doit être
                    supérieur
                    ou égal à la somme restante</p>

                <button id="confirmerButton"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4 relative"
                    wire:click="confirmer" wire:loading.attr="disabled">
                    <span wire:loading.remove>Confirmer le montant</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z">
                            </path>
                        </svg>
                    </span>
                </button>

            </div>

        </div>
    </div>
</div>
