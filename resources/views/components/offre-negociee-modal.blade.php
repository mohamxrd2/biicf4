@props(['produit', 'nombreProprietaires'])

<div id="medium-offreneg{{ $produit->id }}" wire:ignore.self tabindex="-1"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-gray-900/50">
    <div class="relative w-full max-w-lg max-h-full mx-auto mt-10">
        <div class="relative bg-white rounded-xl shadow-2xl transform transition-all dark:bg-gray-800">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Offre (Négociée)
                </h3>
                <x-modal.close-button :modal-id="'medium-offreneg' . $produit->id" />
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <x-offre.nombre-clients :nombre="$nombreProprietaires" />

                    <x-offre.alert-messages />

                    <form wire:submit.prevent="sendoffneg" class="space-y-6">
                        @csrf
                        <x-offre.zone-select />

                        <x-offre.form-buttons :modal-id="'medium-offreneg' . $produit->id"
                            submit-text="Soumettre"
                            loading-target="sendoffneg" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
