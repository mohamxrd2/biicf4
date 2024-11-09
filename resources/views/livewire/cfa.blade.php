<div>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Gestion des Crédits et Remboursements</h1>
        <div class="flex items-center justify-between bg-white shadow-md p-6 rounded-lg">
            <div class="flex items-center space-x-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Listes Des Crédits</h2>
                    <p class="text-gray-500 text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 11 7 11s7-5.75 7-11c0-3.866-3.134-7-7-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                        New York, United States
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <div class="text-right">
                    <p class="text-gray-500 text-sm">Montant des crédits Accordés</p>
                    <p class="text-green-500 font-semibold text-lg">{{ $totalCredits }} FCFA</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 text-sm">Montant des crédits Remboursés</p>
                    <p class="text-red-500 font-semibold text-lg">{{ $totalCreditsRembourses }} FCFA</p>
                </div>
            </div>
        </div>

        <!-- Credits Section -->
        <div class="space-y-4 mt-5">

            <div class="grid gap-4">
                <!-- Credit Card -->
                @foreach ($credits as $credit)
                    <div class="p-4 border rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        d="M3 11a1 1 0 011-1h16a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm1-6h16a1 1 0 011 1v3H3V6a1 1 0 011-1z" />
                                </svg>
                                <h3 class="font-semibold text-gray-700">{{ $credit->description }} achat de bannane</h3>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 font-semibold rounded">ID Credit :
                                00{{ $credit->id }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Date Debut :
                                {{ $credit->date_debut->format('d/m/Y') }}</span>
                            <span class="bg-blue-50 text-red-600 text-xs px-2 py-1 rounded">Date limite :
                                {{ $credit->date_fin->format('d/m/Y') }}</span>
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded">Statut:
                                {{ $credit->statut }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Montant</span>
                                <span>{{ $credit->montant }} FCFA</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $pourcentageRemboursement == 100 ? 'bg-green-600' : 'bg-blue-600' }} h-2 rounded-full"
                                    style="width: {{ $pourcentageRemboursement }}%;"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span></span>
                                <span>Montant restant: {{ $credit->montant_restant }} FCFA</span>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- Transactions Section -->
        <div class="space-y-4 mt-5">
            {{-- @if ($transacCount == 0)
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
            @else --}}
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
                            <div data-modal-target="static-modal-{{ $credit->id }}"
                                data-modal-toggle="static-modal-{{ $credit->id }}"
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
                        @endforeach

                        <!-- Main modal -->
                        <div id="static-modal-{{ $credit->id }}" data-modal-backdrop="static-{{ $credit->id }}"
                            tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">

                            <!-- Modal body -->
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                <div class="flex justify-between items-center border-b pb-3">
                                    <h2 class="text-lg font-semibold">Transaction Details</h2>
                                    <button class="text-gray-400 hover:text-gray-600"
                                        data-modal-hide="static-modal-{{ $credit->id }}">&times;</button>
                                </div>
                                <div class="py-4 space-y-4">
                                    <div class="flex justify-center">
                                        <div class="p-3 rounded-full bg-green-100">
                                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <p class="text-3xl font-bold">+$500.00</p>
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
                                                <p>20/03/2024</p>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="font-medium">Description</p>
                                            <p>Loan disbursement</p>
                                        </div>

                                        <div>
                                            <p class="font-medium">Reference Number</p>
                                            <p class="font-mono">REF123456789</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            {{-- @endif --}}
        </div>



    </div>



</div>
