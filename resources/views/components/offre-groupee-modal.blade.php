@props(['produit', 'nombreFournisseurs', 'users'])


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
                            <h2 class="text-4xl font-bold text-gray-800 mb-2">Offre Groupée</h2>
                            <div class="inline-flex items-center justify-center px-6 py-3 bg-blue-100 rounded-full">
                                <span class="text-lg text-blue-800">
                                    <span class="font-normal">Fournisseurs potentiels:</span>
                                    <span class="font-bold ml-1">({{ $nombreFournisseurs }})</span>
                                </span>
                            </div>
                        </div>

                        <x-offre.alert-messages />

                        <form wire:submit.prevent="sendoffreGrp" class="space-y-6">
                            @csrf
                            <!-- Formulaire avec grille -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Champ 1 -->
                                <div class="relative">
                                    <label class="text-gray-600 mb-2 block text-lg">Quantité que vous pouvez
                                        fournir</label>
                                    <input wire:model="quantite" type="number"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-400 focus:outline-none transition-colors text-lg"
                                        placeholder="Entrez une quantité">
                                </div>

                                <!-- Champ 2 -->
                                <div class="relative">
                                    <label class="text-gray-600 mb-2 block text-lg">Quantité totale souhaitée</label>
                                    <input wire:model="quantiteTotal" type="number"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-400 focus:outline-none transition-colors text-lg"
                                        placeholder="Entrez la quantité totale">
                                </div>

                                <!-- Champ 3 -->
                                <div class="relative">
                                    <label class="text-gray-600 mb-2 block text-lg">Username du client ciblé</label>
                                    <input wire:model.live="search" type="text"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-400 focus:outline-none transition-colors text-lg"
                                        placeholder="Entrez le username">
                                </div>
                                @foreach ($users as $user)
                                    <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg"
                                        wire:click="selectUser('{{ $user->id }}', '{{ $user->username }}')">
                                        <div class="flex">
                                            <img class="w-5 h-5 mr-2 rounded-full" src="{{ asset($user->photo) }}"
                                                alt="">
                                            <div class="flex justify-between items-center w-full">
                                                <span>{{ $user->username }} ({{ $user->name }})</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-offre.zone-select />

                                <x-offre.form-buttons :modal-id="'medium-offreGrp' . $produit->id" submit-text="Soumettre"
                                    loading-target="sendoffreGrp" />
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
