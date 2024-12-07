<div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
            {{ session('error') }}
        </div>
    @endif
    <!-- Titre -->
    <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

    <!-- Informations de l'utilisateur -->
    <div class="flex items-center mb-6 space-x-4">
        <div class="flex-shrink-0">
            <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}" alt="Photo de l'utilisateur"
                class="w-16 h-16 border border-gray-300 rounded-full">
        </div>
        <div>
            <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
            </p>
            <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
        </div>
    </div>

    <!-- Informations sur la demande -->
    <div class="p-4 mb-4 border rounded-lg bg-gray-50">
        <h3 class="mb-2 font-medium text-gray-600 text-md">Numéro de réception de l'argent</h3>
        <p class="text-xl font-semibold text-gray-700">{{ $phonenumber }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="p-4 text-center border rounded-lg bg-gray-50">
            <h3 class="text-lg font-medium text-gray-700">Opérateur</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $operator }}</p>
        </div>
        <div class="p-4 text-center border rounded-lg bg-gray-50">
            <h3 class="text-lg font-medium text-gray-700">Montant à envoyer</h3>
            <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                CFA</p>
        </div>
    </div>

    <!-- Zone de téléchargement du reçu -->
    <div class="mt-4">
        <div class="relative">
            <label for="receipt" class="block text-sm font-medium text-gray-700">Télécharger le reçu</label>
            @if (!$receipt)
                <!-- Zone de téléchargement stylisée -->
                <label for="receipt"
                    class="flex flex-col items-center justify-center w-full h-40 mt-1 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                        <path fill-rule="evenodd"
                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="mt-2 text-sm text-gray-600">Cliquez ou déposez le reçu</span>
                </label>
            @else
                <!-- Affichage de l'image téléchargée et bouton de suppression -->
                <div class="relative">
                    <img src="{{ $receipt->temporaryUrl() }}" alt="Aperçu du reçu"
                        class="w-full h-auto border border-gray-300 rounded-md shadow-lg">
                    <button wire:click="$set('receipt', null)" type="button"
                        class="absolute p-1 text-white bg-red-600 rounded-full top-2 right-2 hover:bg-red-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
            <input wire:model="receipt" type="file" id="receipt" class="hidden" accept="image/*">
            @error('receipt')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex justify-around mt-4">
        @if ($notification->reponse)
            <div
                class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                Réponse envoyée
            </div>
        @else
            <button wire:click="sendRecu"
                class="px-6 py-2 font-semibold text-white bg-blue-500 rounded-md shadow-md hover:bg-blue-600">
                Envoyer
            </button>
            <button wire:click="resetForm"
                class="px-6 py-2 font-semibold text-black bg-gray-300 rounded-md shadow-md hover:bg-gray-400">
                Annuler
            </button>
        @endif
    </div>
</div>
