<div class="flex justify-center mt-4">


    <div class="w-full md:w-1/2 bg-white border border-gray-300 rounded-xl p-4">
        <div x-data="{ withdrawal_type: 'Retrait PSAP' }" class="space-y-4">
            <!-- Menus de type de retrait -->
            <div class="flex space-x-4 mb-4">
                <button type="button" @click="withdrawal_type = 'Retrait PSAP'"
                    :class="{ 'bg-purple-600 text-white': withdrawal_type === 'Retrait PSAP', 'bg-gray-200 text-gray-700': withdrawal_type !== 'Retrait PSAP' }"
                    class="px-4 py-2 rounded-md focus:outline-none transition duration-200">
                    Retrait PSAP
                </button>
                <button type="button" @click="withdrawal_type = 'Retrait par virement'"
                    :class="{ 'bg-purple-600 text-white': withdrawal_type === 'Retrait par virement', 'bg-gray-200 text-gray-700': withdrawal_type !== 'Retrait par virement' }"
                    class="px-4 py-2 rounded-md focus:outline-none transition duration-200">
                    Retrait par virement
                </button>
            </div>

            <!-- Formulaire de Retrait PSAP -->
            @if ($user && $user->actor_type == 'Institution' && $user->user_joint == null)
                <div class="p-6 text-gray-700 bg-gray-100 font-medium text-sm rounded-xl flex text-center ">
                    <p>Vous êtes une institution, veillez joindre un utilisateur dans la page profile pour le retrait à
                        double vérification</p>
                </div>
            @else
                <template x-if="withdrawal_type === 'Retrait PSAP'">

                    <form wire:submit.prevent="initiateWithdrawal" wire:loading.class="opacity-50"
                        class="w-full p-4 bg-white border border-gray-300 rounded-xl">
                        @if (session()->has('message'))
                            <div class="p-2 mb-4 text-white bg-green-500 rounded">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="p-2 mb-4 text-white bg-red-500 rounded">
                                {{ session('error') }}
                            </div>
                        @endif
                        <h2 class="mb-4 text-xl font-semibold text-center">Retrait PSAP</h2>
                        <div class="mb-4">
                            <input type="number" id="amount" wire:model="amount" min="1"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Montant à retirer">
                            @error('amount')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <input type="text" id="psap" wire:model="psap"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="ID ou Username du PSAP">
                            @error('psap')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="relative mt-6">
                            <button type="submit"
                                class="w-full py-3 font-medium text-white transition-colors bg-purple-600 rounded-md hover:bg-purple-700"
                                wire:loading.attr="disabled">
                                Confirmer Retrait
                            </button>
                            <div wire:loading wire:target="initiateWithdrawal"
                                class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                                <span class="font-semibold text-gray-700">Traitement en cours...</span>
                            </div>
                        </div>
                    </form>



                </template>

                <!-- Formulaire de Retrait par virement -->
                <template x-if="withdrawal_type === 'Retrait par virement'">
                    <form wire:submit.prevent="initiateBankWithdrawal" wire:loading.class="opacity-50"
                        class="w-full p-4 bg-white border border-gray-300 rounded-xl">
                        @if (session()->has('message'))
                            <div class="p-2 mb-4 text-white bg-green-500 rounded">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="p-2 mb-4 text-white bg-red-500 rounded">
                                {{ session('error') }}
                            </div>
                        @endif
                        <h2 class="mb-4 text-xl font-semibold text-center">Retrait par Virement</h2>
                        <div class="mb-4">
                            <input type="number" id="amountBank" wire:model="amountBank" min="1"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Montant à retirer">
                            @error('amountBank')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <input type="number" id="bank_account" wire:model="bank_account"
                                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Numéro de compte bancaire">
                            @error('bank_account')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="relative mt-6">
                            <button type="submit"
                                class="w-full py-3 font-medium text-white transition-colors bg-purple-600 rounded-md hover:bg-purple-700"
                                wire:loading.attr="disabled">
                                Confirmer Retrait
                            </button>
                            <div wire:loading wire:target="initiateBankWithdrawal"
                                class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                                <span class="font-semibold text-gray-700">Traitement en cours...</span>
                            </div>
                        </div>
                    </form>
                </template>
            @endif
        </div>
    </div>


</div>
