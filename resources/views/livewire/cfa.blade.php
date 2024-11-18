<div>
    <div class="container mx-auto py-8 bg-gray-100 p-8 min-h-screen">
        <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord des Crédits et Remboursements</h1>
        <p class="text-gray-600 mb-6">Suivez vos Crédits et Remboursements en attente et leur statut</p>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <div class="bg-white shadow rounded-lg p-6 flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1s1-.448 1-1V9c0-.552-.448-1-1-1zm0-5a1.5 1.5 0 000 3h.01A1.5 1.5 0 0012 3zM12 18a1.5 1.5 0 00-1.5 1.5c0 .74.54 1.4 1.28 1.5h.44c.74-.1 1.28-.76 1.28-1.5A1.5 1.5 0 0012 18zM7.757 7.757a1 1 0 00-1.414 0L3.586 10.5a1 1 0 000 1.415l3.757 3.757a1 1 0 001.414-1.414L5.414 12l2.343-2.343a1 1 0 000-1.415zM16.243 7.757a1 1 0 011.414 0L21.414 10.5a1 1 0 010 1.415l-3.757 3.757a1 1 0 11-1.414-1.414L18.586 12l-2.343-2.343a1 1 0 010-1.415z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600">Total Crédits Accordés</p>
                    {{-- <p class="text-2xl font-semibold text-green-500">{{ $totalCredits }} FCFA</p> --}}
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 17.25l5-5 3 3 5-5 5.25 5.25" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600"> Crédits Remboursés
                    </p>
                    {{-- <p class="text-2xl font-semibold text-red-500">{{ $totalCreditsRembourses }} FCFA</p> --}}
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a2 2 0 00-2-2h-3v-4H7v4H4a2 2 0 00-2 2v2h5M10 4V3a1 1 0 011-1h2a1 1 0 011 1v1m-6 4v9h8V8" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600">Crédits Actifs</p>
                    <p class="text-2xl font-semibold text-gray-800">3</p>
                </div>
            </div>
        </div>




        <!-- credit accordes Section -->

        <div class="space-y-4 mt-5">

            <div class="grid gap-4">
                <!-- Credit  -->
                @foreach ($credits as $credit)
                    @php
                        $pourcentageRemboursement =
                            $credit->montant > 0
                                ? (($credit->montant - $credit->montan_restantt) / $credit->montant) *
                                    100
                                : 0;
                    @endphp
                    <div class="p-4 border rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between space-x-4 mb-4">
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">ID Credit :
                                00{{ $credit->id }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Date Debut :
                                {{ $credit->date_debut ? $credit->date_debut->format('d/m/Y') : 'N/A' }}</span>
                            <span class="bg-blue-50 text-red-600 text-xs px-2 py-1 rounded">Date limite :
                                {{ $credit->date_fin ? $credit->date_fin->format('d/m/Y') : 'N/A' }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Statut:
                                {{ $credit->statut }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Montant</span>
                                <span>{{ $credit->montant ?? 'N/A' }} FCFA</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $pourcentageRemboursement == 100 ? 'bg-green-600' : 'bg-blue-600' }} h-2 rounded-full"
                                    style="width: {{ $pourcentageRemboursement }}%;"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Pourcentage remboursé: {{ number_format($pourcentageRemboursement, 2) }}%</span>
                                <span>Montant restant: {{ $credit->montan_restantt ?? 'N/A' }} FCFA</span>
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>


        <!-- Projet accordes Section -->
        <div class="space-y-4 mt-5">

            <div class="grid gap-4">
                <!-- Credit Card -->
                {{-- @foreach ($projets as $projet)
                    @php
                        // Calculer le pourcentage de remboursement pour chaque crédit
                        $pourcentageRemboursementprojets =
                            $projet->montant > 0
                                ? (($projet->montant - $projet->montan_restantt) / $projet->montant) * 100
                                : 0;
                    @endphp
                    <div class="p-4 border rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        d="M3 11a1 1 0 011-1h16a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm1-6h16a1 1 0 011 1v3H3V6a1 1 0 011-1z" />
                                </svg>
                                <h3 class="font-semibold text-gray-700">{{ $projet->description }} XXXXXXX
                                </h3>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 font-semibold rounded">ID Projet :
                                00{{ $projet->id }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Date Debut :
                                {{ $projet->date_debut->format('d/m/Y') }}</span>
                            <span class="bg-blue-50 text-red-600 text-xs px-2 py-1 rounded">Date limite :
                                {{ $projet->date_fin->format('d/m/Y') }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Statut:
                                {{ $projet->statut }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Montant</span>
                                <span>{{ $projet->montant }} FCFA</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $pourcentageRemboursementprojets == 100 ? 'bg-green-600' : 'bg-blue-600' }} h-2 rounded-full"
                                    style="width: {{ $pourcentageRemboursementprojets }}%;"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Pourcentage remboursé:
                                    {{ number_format($pourcentageRemboursementprojets, 2) }}%</span>
                                <span>Montant restant: {{ $projet->montan_restantt }} FCFA</span>
                            </div>
                        </div>
                    </div>
                @endforeach --}}

            </div>
        </div>

        <!-- Transactions Section -->
        <div class="space-y-4 mt-5">
            @if ($transacCount == 0)
                <div class="flex-col items-center justify-center w-full text-center h-80">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-12 h-12 text-gray-500 dark:text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h2 class="mb-2 text-2xl font-semibold">Aucune transaction</h2>
                    <p class="text-gray-500">Vous verrez les historiques des transactions ici !</p>
                </div>
            @else
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M15 12h3m-3 4h2m-2-8h2M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800 py-4 px-6">Transactions de Remboursements</h2>
                </div>
                <div class="border rounded-lg shadow overflow-y-auto h-80 p-4">
                    <div class="space-y-4">
                        <!-- Transaction -->
                        @foreach ($transactions as $transaction)
                            <div data-modal-target="static-modal-{{ $transaction->id }}"
                                data-modal-toggle="static-modal-{{ $transaction->id }}"
                                class="flex items-center justify-between p-3 border-b hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $transaction->type ?? 'Type de transaction' }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $transaction->date_transaction->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="font-semibold text-green-600">{{ $transaction->montant }} FCFA</div>
                            </div>
                            <!-- Main modal -->
                            <div id="static-modal-{{ $transaction->id }}"
                                data-modal-backdrop="static-{{ $transaction->id }}" tabindex="-1"
                                aria-hidden="true"
                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">

                                <!-- Modal body -->
                                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                    <div class="flex justify-between items-center border-b pb-3">
                                        <h2 class="text-lg font-semibold">Transaction Details</h2>
                                        <button class="text-gray-400 hover:text-gray-600"
                                            data-modal-hide="static-modal-{{ $transaction->id }}">&times;</button>
                                    </div>
                                    <div class="py-4 space-y-4">
                                        <div class="flex justify-center">
                                            <div class="p-3 rounded-full bg-green-100">
                                                <svg class="w-6 h-6 text-green-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <p class="text-3xl font-bold">+{{ $transaction->montant }} FCFA</p>
                                            <span
                                                class="px-2 py-1 text-sm rounded bg-green-100 text-green-800">Completed</span>
                                        </div>

                                        <div class="space-y-4 text-sm text-gray-700">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path d="M8 7v10m4-10v10m4-10v10M5 10h14M5 14h14" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium">Date</p>
                                                    <p>{{ $transaction->created_at }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <p class="font-medium">Description</p>
                                                <p>Loan disbursement</p>
                                            </div>

                                            <div>
                                                <p class="font-medium">Reference Number</p>
                                                <p class="font-mono">REF{{ $transaction->reference_id }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>
            @endif
        </div>
    </div>



</div>
