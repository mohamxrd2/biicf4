{{-- resources/views/components/tontine-card.blade.php --}}
@props([
    'id',
    'montant',
    'frequence',
    'dateDebut',
    'dateFin',
    'progression',
    'cotisationsEffectuees',
    'cotisationsTotales',
    'montantCollecte',
    'prochainPaiement',
    'status' => 'active',
])

<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 group">
    <div class="p-6">
        <!-- Titre et statut -->
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tontine.detail', $id) }}" class="text-xl font-bold text-gray-900">
                        Tontine #000{{ $id }}
                    </a>
                    <span @class([
                        'px-3 py-1 text-xs font-semibold rounded-full',
                        'text-green-700 bg-green-100' => $status === 'active',
                        'text-yellow-700 bg-yellow-100' => $status === 'pending',
                        'text-red-700 bg-red-100' => $status === 'inactive',
                    ])>
                        {{ ucfirst($status) }}
                    </span>
                </div>
                <div class="mt-2 space-y-1">
                    <p class="text-gray-600">Montant: 
                        <span class="font-semibold">
                            {{ number_format($montant, 0, ',', ' ') }} FCFA
                        </span>
                    </p>
                    <p class="text-gray-600">Fréquence: 
                        <span class="font-semibold">{{ $frequence }}</span>
                    </p>
                </div>
            </div>

            <div class="text-right sm:text-left">
                <p class="text-sm text-gray-500">Date de début</p>
                <p class="font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($dateDebut)->translatedFormat('d F Y') }}
                </p>
                <p class="text-sm text-gray-500 mt-2">Date de fin</p>
                <p class="font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($dateFin)->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="mt-4">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Progression</span>
                <span class="font-medium text-indigo-600">{{ $progression }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full relative group-hover:shadow-lg transition-all duration-300"
                    style="width: {{ $progression }}%">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques (responsive grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-500">Cotisations effectuées</p>
                <p class="text-lg font-bold text-gray-900">{{ $cotisationsEffectuees }}/{{ $cotisationsTotales }}</p>
            </div>
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-500">Montant collecté</p>
                <p class="text-lg font-bold text-indigo-600">
                    {{ number_format($montantCollecte, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-500">Prochain paiement</p>
                <p class="text-lg font-bold text-red-700">
                    {{ \Carbon\Carbon::parse($prochainPaiement)->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap justify-center md:justify-end mt-6 gap-3">
            <a href="{{ route('tontine.detail', $id) }}" 
                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center gap-2 group">
                <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Détails
            </a>
            <button
                class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-2 group">
                <svg class="w-4 h-4 text-indigo-500 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Effectuer un paiement
            </button>
        </div>
    </div>
</div>

