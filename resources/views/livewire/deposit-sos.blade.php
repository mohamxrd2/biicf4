<div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
    <!-- Messages de notification -->
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

    <!-- Affichage des erreurs de validation -->
    @if ($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Titre -->
    <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

    <!-- Informations de l'utilisateur -->
    <div class="flex items-center mb-6 space-x-4">
        <div class="flex-shrink-0">
            <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
        </div>
        <div>
            <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
            </p>
            <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
        </div>
    </div>

    <!-- Détails du dépôt -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="p-4 text-center border rounded-lg bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-700">Montant demandé</h3>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($amountDeposit ?? 0, 0, ',', ' ') }}
                CFA</p>
        </div>
        <div class="p-4 text-center border rounded-lg bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-700">R.O.I Attendu</h3>
            <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                CFA</p>
        </div>
    </div>

    <!-- Sélection de l'opérateur -->
    <label for="operator" class="block mb-2 text-gray-700">Opérateur</label>
    <select id="operator" class="w-full p-2 mb-6 border border-gray-300 rounded-md" required
        wire:model="operator">
        <option value="" disabled selected>Choisir l'opérateur où vous voulez recevoir l'argent</option>
        <option value="Wave">Wave</option>
        <option value="Orange Money">Orange Money</option>
        <option value="Moov Money">Moov Money</option>
        <option value="MTN Money">MTN Money</option>
        <option value="Tresor Pay">Tresor Pay</option>
    </select>
    @error('operator')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <!-- Numéro de réception -->
    <label for="phonenumber" class="block mb-2 text-gray-700">Le numéro de réception</label>
    <input type="text" id="phonenumber" wire:model="phonenumber"
        class="w-full p-2 border border-gray-300 rounded-md" placeholder="Entrez le numéro de réception">
    @error('phonenumber')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <!-- Boutons d'action -->
    <div class="flex justify-around mt-4">
        @if ($existingRequest)
            <p class="font-bold text-red-600 ">Demande Expiré !</p>
        @else
            @if ($notification->reponse)
                <div
                    class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                    Réponse envoyée
                </div>
            @else
                <button wire:click="acceptDeposit"
                    class="px-6 py-2 font-semibold text-white bg-green-500 rounded-md shadow-md hover:bg-green-600">
                    Accepter
                </button>
                <button wire:click="rejectDeposit"
                    class="px-6 py-2 font-semibold text-white bg-red-500 rounded-md shadow-md hover:bg-red-600">
                    Refuser
                </button>
            @endif

        @endif

    </div>
</div>
