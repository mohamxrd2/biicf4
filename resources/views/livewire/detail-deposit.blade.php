<div>
    {{-- Afficher les messages de succès et d'erreur --}}
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Détails du dépôt</h2>
    
        @if ($deposit)
            <div class="space-y-4">
                <div>
                    <h3 class="font-medium text-gray-700">Nom & Prénom :</h3>
                    <p class="text-gray-600">{{ $userName }}</p> <!-- Affichage du nom de l'utilisateur -->
                </div>
    
                <div>
                    <h3 class="font-medium text-gray-700">Montant :</h3>
                    <p class="text-gray-600">{{ $deposit->data['montant'] }} FCFA</p>
                </div>
    
                <div>
                    <h3 class="font-medium text-gray-700">Reçu :</h3>
                    <img src="{{ asset($deposit->data['recu']) }}" alt="Reçu" class="max-w-full h-auto rounded-lg shadow-md">
                    <!-- Affichage de l'image du reçu -->
                </div>
    
                <div>
                    <h3 class="font-medium text-gray-700">Date :</h3>
                    <p class="text-gray-600">{{ $deposit->created_at->diffForHumans() }}</p> <!-- Affichage formaté -->
                </div>
            </div>
    
            <div class="mt-6 flex justify-end space-x-4">
                @if($deposit->reponse == 'Accepté' || $deposit->reponse == 'Refuser')
                    <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-300 cursor-not-allowed">
                        Réponse envoyée
                    </div>
                @else
                    <button wire:click="acceptDeposit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-150">Accepter</button>
                    <button wire:click="rejectDeposit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-150">Refuser</button>
                @endif
            </div>
        @else
            <p class="text-gray-600">Aucun détail trouvé pour ce dépôt.</p>
        @endif
    </div>
</div>
