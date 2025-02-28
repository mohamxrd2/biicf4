<div>
    @php
        $tontineData = [
            'id' => $tontine->id,
            'status' => 'active',
            'amount' => $tontine->gain_potentiel,
            'frequency' => $tontine->frequence,
            'startDate' => $tontine->date_debut,
            'endDate' => $tontine->date_fin,
            'progress' => $pourcentage,
            'contributionsMade' => $cts_reussi,
            'totalContributions' => $tontine->nombre_cotisations,
            'amountCollected' => $cts_sum, // Utilisation de la variable correcte
            'nextPayment' => $tontine->next_payment_date,
        ];

    @endphp
    <x-offre.alert-messages />

    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('tontine') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Tontine #000{{ $tontine->id }}</h1>
                            <p class="text-sm text-gray-500">Créée le
                                {{ \Carbon\Carbon::parse($tontine->date_debut)->translatedFormat('d F Y') }}</p>
                        </div>

                        <!-- Statut de la tontine -->
                        <span @class([
                            'px-3 py-1 text-xs font-semibold rounded-full',
                            'text-green-700 bg-green-100' => $tontine->statut === 'active',
                            'text-yellow-700 bg-yellow-100' => $tontine->statut === '1st',
                            'text-red-700 bg-red-100' => $tontine->statut === 'inactive',
                        ])>
                            {{ $tontine->statut === '1st' ? 'En attente' : $tontine->statut }}
                        </span>

                        <!-- Indicateur de durée -->
                        <span @class([
                            'px-3 py-1 text-xs font-semibold rounded-full',
                            'text-blue-700 bg-blue-100' => $tontine->isUnlimited,
                            'text-gray-700 bg-gray-200' => !$tontine->isUnlimited,
                        ])>
                            {{ $tontine->isUnlimited ? 'Période indéfinie' : 'Période définie' }}
                        </span>

                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    {{-- Overview Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Aperçu de la tontine</h2>

                        <!-- Grid responsive -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Montant de cotisation</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            @if(!$tontine->isUnlimited)
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Gain potentiel</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ number_format($userGain, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            @endif
                            
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Fréquence</p>
                                <p class="text-xl font-bold text-gray-900">{{ $tontine->frequence }}</p>
                            </div>
                        </div>

                        @if (!$tontine->isUnlimited)

                        <!-- Barre de progression -->
                        <div class="mt-8">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Progression</span>
                                <span
                                    class="font-medium text-indigo-600">{{ round($tontineData['progress'], 0) }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"
                                    style="width: {{ round($pourcentage ?? 0, 0) }}%"></div>
                            </div>
                        </div>
                            
                        @endif

                        

                        <!-- Stats Grid responsive -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8 pt-6 border-t border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Cotisations effectuées</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $tontineData['contributionsMade'] }}/{{ $tontine->nombre_cotisations }}
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
                                <p class="text-lg font-bold text-gray-900">
                                    @if ($tontine->statut === 'inactive')
                                        ✅ Tontine terminée
                                    @else
                                        {{ \Carbon\Carbon::parse($tontine->next_payment_date)->translatedFormat('d F Y') }}
                                    @endif
                                </p>
                            </div>

                        </div>
                    </div>


                    {{-- Transactions History --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-lg font-semibold text-gray-900">Historique des transactions</h2>
                                <button class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Exporter
                                </button>
                            </div>

                            <div class="space-y-4">
                                @foreach ($transCotisation as $transaction)
                                    <div
                                        @if ($transaction->statut !== 'payé') wire:click="toggleTransactionSelection({{ $transaction->id }})"
                                            class="flex items-center justify-between p-4 rounded-xl border cursor-pointer
                                                {{ in_array($transaction->id, $selectedTransactions ?? []) ? 'bg-blue-50 border-blue-300' : 'border-gray-100 hover:border-gray-200' }}
                                                transition-all duration-200"
                                        @else
                                            class="flex items-center justify-between p-4 rounded-xl border border-gray-100" @endif>
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div
                                                    class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->statut === 'payé' ? 'bg-green-100' : 'bg-red-100' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-5 h-5 {{ $transaction->statut === 'payé' ? 'text-green-600' : 'text-red-600' }}"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                @if ($transaction->statut !== 'payé' && in_array($transaction->id, $selectedTransactions ?? []))
                                                    <div
                                                        class="absolute -top-2 -right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-3 w-3 text-white" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Cotisation</p>
                                                <p class="text-sm text-gray-500">{{ $transaction->created_at }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">
                                                {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                                            </p>
                                            <p
                                                class="{{ $transaction->statut === 'payé' ? 'text-green-600' : 'text-red-600' }} text-sm">
                                                {{ $transaction->statut === 'payé' ? 'payé' : 'échec' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if (count($selectedTransactions ?? []) > 0)
                                    <div
                                        class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                                        <p class="text-sm text-gray-600 mb-2">
                                            {{ count($selectedTransactions) }} paiement(s) sélectionné(s)
                                        </p>

                                        <button wire:click="preventDoubleSubmission"
                                            @if ($isProcessing) disabled @endif
                                            class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors relative
                                               {{ $isProcessing ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            <span wire:loading.remove>Réessayer les paiements</span>
                                            <x-icons.spinner wire:loading
                                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                                            <span wire:loading>Traitement...</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($hasMoreTransactions)
                                    <div class="mt-6 text-center">
                                        <button wire:click="loadMoreTransactions"
                                            class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                            Voir plus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Quick Actions --}}
                    @if ($tontine->status === 'active' && $tontine->isUnlimited)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                                <div class="space-y-3">
                                    <button
                                        class="w-full py-3 px-4 bg-red-600 text-white font-medium rounded-xl flex items-center justify-center gap-2
           hover:bg-red-700 transition-all duration-300 shadow-md hover:shadow-lg active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Arrêter la tontine</span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    @endif


                    {{-- Important Dates --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates importantes</h3>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">Date de début</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($tontine->date_debut)->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">Date de fin</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($tontine->date_fin)->translatedFormat('d F Y') }}
                                        </p>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Le premier paiement couvre les frais de gestion. Les paiements suivants
                                            seront automatiquement ajoutés au CEDD.
                                        </p>
                                    </div>
                                </div>
                                <button
                                    class="w-full mt-2 py-2 px-4 text-sm text-indigo-600 hover:text-indigo-700 font-medium rounded-lg hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2">
                                    Voir toutes les règles
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
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
