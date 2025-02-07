<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Initiation d'une Tontine</h2>
    <form wire:submit.prevent="initiateTontine" class="space-y-4">
        <!-- Montant de cotisation -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Montant de cotisation (FCFA)</label>
            <input type="number" id="amount" wire:model.defer="amount" placeholder="Entrez le montant"
                class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-purple-500 focus:border-purple-500" required>
            @error('amount') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Fréquence de cotisation -->
        <div>
            <label for="frequency" class="block text-sm font-medium text-gray-700">Fréquence de cotisation</label>
            <select id="frequency" wire:model.defer="frequency"
                class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                <option value="">-- Choisissez la fréquence --</option>
                <option value="quotidienne">Quotidienne</option>
                <option value="hebdomadaire">Hebdomadaire</option>
                <option value="mensuelle">Mensuelle</option>
            </select>
            @error('frequency') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Date de fin de cotisation -->
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin de cotisation</label>
            <input type="date" id="end_date" wire:model.defer="end_date"
                class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-purple-500 focus:border-purple-500" required>
            @error('end_date') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Mode de paiement -->
        <div>
            <label for="payment_mode" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
            <select id="payment_mode" wire:model.defer="payment_mode"
                class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                <option value="">-- Choisissez le mode de paiement --</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="virement_bancaire">Virement Bancaire</option>
                <option value="cash">Espèces</option>
            </select>
            @error('payment_mode') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Résumé des frais de gestion -->
        <div class="p-4 bg-gray-100 rounded-md">
            <p class="text-sm text-gray-600">Le premier paiement est réservé aux frais de gestion.</p>
            <p class="text-sm text-gray-600">Les paiements suivants seront ajoutés au CEDD.</p>
        </div>

        <!-- Bouton de soumission -->
        <div>
            <button type="submit"
                class="w-full py-3 px-6 bg-gradient-to-r from-purple-600 to-blue-500 text-white font-bold rounded-md shadow-lg hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-300 ease-in-out">
                Initier la Tontine
            </button>
        </div>
    </form>
</div>

