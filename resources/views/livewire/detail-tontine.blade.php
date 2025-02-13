<div>
    @php
    $tontineData = [
        'id' => '2989',
        'status' => 'active',
        'amount' => 278000,
        'frequency' => 'Hebdomadaire',
        'startDate' => '12 Mai 2024',
        'endDate' => '12 Mai 2025',
        'progress' => 65,
        'contributionsMade' => 15,
        'totalContributions' => 24,
        'amountCollected' => 180000,
        'nextPayment' => '19 Fév 2025',
    ];
    
    $transactions = [
        ['id' => 1, 'date' => '12 Fév 2024', 'amount' => 12000, 'status' => 'success', 'type' => 'Cotisation'],
        ['id' => 2, 'date' => '05 Fév 2024', 'amount' => 12000, 'status' => 'success', 'type' => 'Cotisation'],
        ['id' => 3, 'date' => '29 Jan 2024', 'amount' => 12000, 'status' => 'success', 'type' => 'Cotisation'],
        ['id' => 4, 'date' => '22 Jan 2024', 'amount' => 12000, 'status' => 'failed', 'type' => 'Cotisation'],
    ];
    @endphp

<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('tontine') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tontine #{{ $tontineData['id'] }}</h1>
                        <p class="text-sm text-gray-500">Créée le {{ $tontineData['startDate'] }}</p>
                    </div>
                    <span class="ml-4 px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                        Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Overview Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Aperçu de la tontine</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Montant de cotisation</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ number_format($tontineData['amount'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Fréquence</p>
                                <p class="text-xl font-bold text-gray-900">{{ $tontineData['frequency'] }}</p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-8">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Progression</span>
                                <span class="font-medium text-indigo-600">{{ $tontineData['progress'] }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"
                                     style="width: {{ $tontineData['progress'] }}%"></div>
                            </div>
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-3 gap-6 mt-8 pt-6 border-t border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Cotisations effectuées</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $tontineData['contributionsMade'] }}/{{ $tontineData['totalContributions'] }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Montant collecté</p>
                                <p class="text-lg font-bold text-indigo-600">
                                    {{ number_format($tontineData['amountCollected'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Prochain paiement</p>
                                <p class="text-lg font-bold text-gray-900">{{ $tontineData['nextPayment'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transactions History --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">Historique des transactions</h2>
                            <button class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Exporter
                            </button>
                        </div>

                        <div class="space-y-4">
                            @foreach($transactions as $transaction)
                                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction['status'] === 'success' ? 'bg-green-100' : 'bg-red-100' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $transaction['status'] === 'success' ? 'text-green-600' : 'text-red-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $transaction['type'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $transaction['date'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">
                                            {{ number_format($transaction['amount'], 0, ',', ' ') }} FCFA
                                        </p>
                                        <p class="{{ $transaction['status'] === 'success' ? 'text-green-600' : 'text-red-600' }} text-sm">
                                            {{ $transaction['status'] === 'success' ? 'Réussi' : 'Échoué' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                        <div class="space-y-3">
                            <button class="w-full py-3 px-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                                Effectuer un paiement
                            </button>
                            <button class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Important Dates --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates importantes</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Date de début</p>
                                    <p class="text-sm text-gray-500">{{ $tontineData['startDate'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Date de fin</p>
                                    <p class="text-sm text-gray-500">{{ $tontineData['endDate'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Prochain paiement</p>
                                        <p class="text-sm text-gray-500">{{ $tontineData['nextPayment'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rules & Information --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Règles et informations</h3>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Le premier paiement couvre les frais de gestion. Les paiements suivants seront automatiquement ajoutés au CEDD.
                                        </p>
                                    </div>
                                </div>
                                <button class="w-full mt-2 py-2 px-4 text-sm text-indigo-600 hover:text-indigo-700 font-medium rounded-lg hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2">
                                    Voir toutes les règles
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
