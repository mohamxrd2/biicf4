<!-- resources/views/components/negociation-info-card.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
    <!-- Offre initiale -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-sm text-gray-600">Offre initiale</div>
        @if ($offreIniatiale)
            <div class="text-lg font-bold">{{ $offreIniatiale->prixTrade . ' FCFA' }}</div>
        @else
            <div class="flex items-center space-x-2 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="text-sm text-gray-500">Aucune offre initiale</span>
            </div>
        @endif
    </div>

    <!-- Meilleure offre -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-sm text-gray-600">Meilleure offre</div>
        @if ($prixLePlusBas)
            <div class="text-lg font-bold">{{ $prixLePlusBas . ' FCFA' }}</div>
        @else
            <div class="flex items-center space-x-2 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-gray-500">Aucune offre soumise</span>
            </div>
        @endif
    </div>

    <!-- Compte à rebours -->
    <div class="bg-gray-50 rounded-lg p-4">
        <livewire:countdown :id="$id" />
    </div>
</div>
