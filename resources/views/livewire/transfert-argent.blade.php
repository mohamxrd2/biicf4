<div>
    <form wire:submit.prevent="submit">
        <div class="flex items-center justify-center">
            <div class="w-full md:w-1/2 bg-white rounded-lg shadow-md p-6">
                @if ($errorMessage)
                    <div class="alert alert-danger mb-3"
                        style="color: red; border: 1px solid red; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                        {{ $errorMessage }}
                    </div>
                @endif


                <h2 class="text-xl font-bold text-center mb-6 text-gray-700">Transférer de l'Argent</h2>

                <!-- Champ de recherche utilisateur -->
                <div class="mb-4">
                    <label for="recipient" class="block text-sm font-medium text-gray-600 mb-1">Destinataire</label>
                    <input type="text" wire:model.live="search" id="recipient" placeholder="Recherchez un utilisateur"
                        class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">

                    @if (!empty($search))
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
                    @endif
                </div>

                <!-- Champ de saisie du montant -->
                <div x-data="{ amount: @entangle('amount').defer }">
                    <!-- Champ de saisie -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-600 mb-1">Montant Envoyé</label>
                        <input type="number" x-model="amount" id="amount" placeholder="Entrez le montant"
                            class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Affichage du montant total -->
                    <p class="text-sm text-gray-700 my-2">
                        Montant(avec 1%):
                        <span class="font-bold text-gray-900"
                            x-text="(amount ? (parseFloat(amount) + parseFloat(amount) * 0.01).toFixed(2) : '0.00') + ' CFA'">
                        </span>
                    </p>
                </div>


                <!-- Boutons de soumission et annulation -->
                <div class="flex justify-end items-center space-x-3">
                    <button type="reset" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
