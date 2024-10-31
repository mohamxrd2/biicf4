<div class="container mx-auto p-6">
    {{-- Affichage des messages de succès et d'erreur --}}
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg shadow-md" role="alert">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg shadow-md" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Détails du dépôt --}}
    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Détails du dépôt</h2>

        @if ($deposit)
            <div class="space-y-6">
                {{-- Nom de l'utilisateur --}}
                <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                    <h3 class="font-semibold text-gray-700 text-lg">Nom & Prénom :</h3>
                    <p class="text-gray-600 text-base">{{ $userName }}</p>
                </div>

                {{-- Montant --}}
                <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                    <h3 class="font-semibold text-gray-700 text-lg">Montant :</h3>
                    <p class="text-gray-600 text-base">{{ number_format($deposit->montant, 0, ',', ' ') }}FCFA</p>
                </div>

                {{-- Reçu --}}
                <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                    <h3 class="font-semibold text-gray-700 text-lg">Reçu :</h3>
                    @if (!empty($deposit->recu)) <!-- Vérifiez si la clé 'recu' est présente dans les données -->
                        <img src="{{ asset('storage/' . $deposit->recu) }}" alt="Reçu" class="w-full h-auto rounded-lg shadow-md">
                    @else
                        <p class="text-gray-600 text-base">Aucun reçu fourni.</p>
                    @endif
                </div>
                

                {{-- Date de création --}}
                <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                    <h3 class="font-semibold text-gray-700 text-lg">Date :</h3>
                    <p class="text-gray-600 text-base">{{ $deposit->created_at->format('d M Y, H:i') }} ({{ $deposit->created_at->diffForHumans() }})</p>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="mt-8 flex justify-end space-x-4">
                @if ($deposit->statut === 'Accepté' || $deposit->statut === 'Refusé')
                    <div class="py-2 px-4 border border-gray-300 rounded-md shadow text-sm font-medium text-gray-700 bg-gray-100 cursor-not-allowed">
                        Réponse envoyée
                    </div>
                @else
                    <button wire:click="acceptDeposit" class="px-6 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition duration-150">
                        Accepter
                    </button>
                    <button wire:click="rejectDeposit" class="px-6 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition duration-150">
                        Refuser
                    </button>
                @endif
            </div>
        @else
            <p class="text-gray-600">Aucun détail trouvé pour ce dépôt.</p>
        @endif
    </div>
</div>
