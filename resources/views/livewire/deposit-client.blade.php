<div class="flex justify-center mt-4">
    {{-- The best athlete wants his opponent at his best. --}}


    <div class="w-full md:w-1/2 bg-white border border-gray-300 rounded-xl p-4">
        <div x-data="{ deposit_type: @entangle('deposit_type').defer , amount: 0, roi: 0 }"
        x-init="deposit_type = 'Virement Bancaire'"
        x-effect="roi = Math.round((amount * 1.10) / 5) * 5">
            <!-- Menus de type de financement -->
            <div class="flex space-x-4 mb-4">
                <button type="button" @click="deposit_type = 'Virement Bancaire'"
                    :class="{ 'bg-purple-600 text-white': deposit_type === 'Virement Bancaire', 'bg-gray-200 text-gray-700': deposit_type !== 'Virement Bancaire' }"
                    class="px-4 py-2 rounded-md focus:outline-none transition duration-200">
                    Virement Bancaire
                </button>
                <button type="button" @click="deposit_type = 'Rechargement SOS'"
                    :class="{ 'bg-purple-600 text-white': deposit_type === 'Rechargement SOS', 'bg-gray-200 text-gray-700': deposit_type !== 'Rechargement SOS' }"
                    class="px-4 py-2 rounded-md focus:outline-none transition duration-200">
                    Rechargement SOS
                </button>
            </div>

            <!-- Affichage du formulaire de Virement Bancaire -->
            <template x-if="deposit_type === 'Virement Bancaire'">
                <form wire:submit.prevent="submitDeposit" enctype="multipart/form-data">
                    <!-- Messages de succès ou d'erreur -->
                    @if (session()->has('message'))
                        <div class="bg-green-400 text-white p-4 rounded-md mb-4">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-400 text-white p-4 rounded-md mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Contenu du formulaire pour Virement Bancaire -->
                    <h2 class="text-center font-semibold text-xl">Virement Bancaire</h2>
                    <p class="text-center font-semibold italic text-sm text-gray-600 my-2">RIB: 20990019909</p>

                    <div class="mt-4">
                        <input type="number" id="amount" wire:model="amount"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Entrez le montant" required>
                        @error('amount')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <div class="relative">
                            <label for="receipt" class="block text-sm font-medium text-gray-700">Télécharger le
                                reçu</label>
                            @if (!$receipt)
                                <!-- Zone de téléchargement stylisée -->
                                <label for="receipt"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez le reçu</span>
                                </label>
                            @else
                                <!-- Affichage de l'image téléchargée et bouton de suppression -->
                                <div class="relative">
                                    <img src="{{ $receipt->temporaryUrl() }}" alt="Aperçu du reçu"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <button wire:click="$set('receipt', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <input wire:model="receipt" type="file" id="receipt" class="hidden" accept="image/*">
                            @error('receipt')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 relative">
                        <button type="submit"
                            class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Soumettre</span>
                            <span wire:loading>Traitement en cours...</span>
                        </button>
                    </div>
                </form>
            </template>

            <!-- Affichage du formulaire de Rechargement SOS -->
            <template x-if="deposit_type === 'Rechargement SOS'">
                <form wire:submit.prevent="submitSOSRecharge">

                    @if (session()->has('message'))
                        <div class="bg-green-400 text-white p-4 rounded-md mb-4">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-400 text-white p-4 rounded-md mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h2 class="text-center font-semibold text-xl mb-4">Rechargement SOS</h2>

                    <div class="mb-4">
                        <label for="amount">Entrez le montant</label>
                        <input type="number" id="amount" wire:model="amount" x-model="amount"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Entrez le montant">
                        @error('amount')
                            <span class="text-red-500 mt-2">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="roi">Montant à envoyer</label>
                        <input type="number" wire:model="roi" id="roi" x-model="roi"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Montant à envoyer" disabled>
                    </div>

                    <div class="mt-6 relative">
                        <button type="submit"
                            class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Soumettre</span>
                            <span wire:loading>Traitement en cours...</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>



</div>
