@props(['produit', 'nombreFournisseurs'])

<div id="medium-offreGrp{{ $produit->id }}" wire:ignore.self tabindex="-1"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-gray-900/50">
    <div class="relative w-full max-w-lg max-h-full mx-auto mt-10">
        <div class="relative bg-white rounded-xl shadow-2xl transform transition-all dark:bg-gray-800">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Offre (Groupée)
                </h3>
                <x-modal.close-button :modal-id="'medium-offreGrp' . $produit->id" />
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center p-4 bg-blue-50 rounded-lg dark:bg-blue-900/50 mb-4 w-full">
                        <p class="text-gray-700 dark:text-gray-300">
                            Le nombre de fournisseurs potentiels est
                            <span class="font-bold text-blue-600 dark:text-blue-400 text-lg ml-1">
                                ({{ $nombreFournisseurs }})
                            </span>
                        </p>
                    </div>

                    <x-offre.alert-messages />

                    <form wire:submit.prevent="sendoffreGrp" class="space-y-6">
                        @csrf

                        <!-- Quantité -->
                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantité souhaitée
                            </label>
                            <input wire:model="quantite" type="number" min="1" required
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                                placeholder="Entrez une quantité">
                            @error('quantite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Username du client
                            </label>
                            <input wire:model="username" type="text" required
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                                placeholder="Entrez le username">
                            @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <x-offre.zone-select />

                        <x-offre.form-buttons :modal-id="'medium-offreGrp' . $produit->id"
                            submit-text="Soumettre"
                            loading-target="sendoffreGrp" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
