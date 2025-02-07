<div class="max-w-2xl mx-auto p-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 p-6">
            <h2 class="text-3xl font-bold text-white">Nouvelle Tontine</h2>
            <p class="text-purple-100 mt-2">Configurez votre tontine en quelques étapes simples</p>
        </div>

        <form wire:submit.prevent="initiateTontine" class="p-6 space-y-6">
            <!-- Montant avec icône -->
            <div class="relative">
                <label for="amount" class="text-sm font-semibold text-gray-700 mb-1 block">
                    Montant de cotisation
                </label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    
                    <input type="number" id="amount" wire:model.defer="amount"
                        class="block w-full pl-16 pr-4 py-3 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Montant en FCFA"
                        required>
                </div>
                @error('amount') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Fréquence avec badges -->
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-3 block">Fréquence de cotisation</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative">
                        <input type="radio" wire:model.defer="frequency" value="quotidienne" class="peer sr-only">
                        <div class="w-full text-center p-3 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            Quotidienne
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" wire:model.defer="frequency" value="hebdomadaire" class="peer sr-only">
                        <div class="w-full text-center p-3 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            Hebdomadaire
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" wire:model.defer="frequency" value="mensuelle" class="peer sr-only">
                        <div class="w-full text-center p-3 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            Mensuelle
                        </div>
                    </label>
                </div>
                @error('frequency') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Date de fin avec calendrier moderne -->
            <div>
                <label for="end_date" class="text-sm font-semibold text-gray-700 mb-1 block">
                    Date de fin
                </label>
                <input type="date" id="end_date" wire:model.defer="end_date"
                    class="block w-full px-4 py-3 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                    min="{{ date('Y-m-d') }}"
                    required>
                @error('end_date') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Mode de paiement avec icônes -->
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-3 block">Mode de paiement</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative">
                        <input type="radio" wire:model.defer="payment_mode" value="mobile_money" class="peer sr-only">
                        <div class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            Mobile Money
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" wire:model.defer="payment_mode" value="virement_bancaire" class="peer sr-only">
                        <div class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                            </svg>
                            Virement
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" wire:model.defer="payment_mode" value="cash" class="peer sr-only">
                        <div class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-all peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-600 hover:bg-gray-50">
                            <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Espèces
                        </div>
                    </label>
                </div>
                @error('payment_mode') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Information Box -->
            <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-purple-800">Information importante</h3>
                        <div class="mt-2 text-sm text-purple-700">
                            <p>Le premier paiement couvre les frais de gestion.</p>
                            <p>Les paiements suivants seront automatiquement ajoutés au CEDD.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition-all duration-300 ease-in-out hover:-translate-y-1">
                Lancer la Tontine
                <span class="ml-2">→</span>
            </button>
        </form>
    </div>
</div>