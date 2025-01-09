@props(['produit', 'nombreProprietaires'])


<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-3xl sm:mx-auto w-full px-4">
        <!-- Ombre en arrière-plan -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
        </div>

        <!-- Contenu principal -->
        <div class="relative px-6 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-3xl mx-auto">
                <div class="divide-y divide-gray-200">
                    <!-- Titre et section d'introduction -->
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center mb-8">
                            <h2 class="text-4xl font-bold text-gray-800 mb-2">Offre (Simple)</h2>
                            <x-offre.nombre-clients :nombre="$nombreProprietaires" />

                        </div>

                        <!-- Formulaire avec grille -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
                            <x-offre.alert-messages />

                            <form wire:submit.prevent="sendoffneg" class="space-y-6">
                                @csrf
                                <x-offre.zone-select />

                                <x-offre.form-buttons :modal-id="'medium-offreneg' . $produit->id" submit-text="Soumettre"
                                    loading-target="sendoffneg" />
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
