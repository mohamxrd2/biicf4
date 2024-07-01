<div>


    <div wire:loading.delay.longest>
        <span class="text-green-500">Sending...</span>
    </div>

    <form wire:submit.prevent="recharge">
        <div
            class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                <h3 class="font-bold text-gray-800 dark:text-white">
                    Recharger le compte
                </h3>
            </div>
            <div class="p-4 overflow-y-auto">
                <div class="w-full mb-3">
                    <div class="relative">
                        <input wire:model.live="search"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                            type="text" placeholder="Entrez le nom de l'agent">

                        @if (!empty($search))

                            @foreach ($agents as $agent)
                                <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg"
                                    wire:click="selectAgent('{{ $agent->id }}', '{{ $agent->username }}')">
                                    <div class="flex">
                                        <img class="w-5 h-5 mr-2 rounded-full" src="{{ asset($agent->photo) }}"
                                            alt="">
                                        <div class="flex justify-between items-center w-full">
                                            <span>{{ $agent->username }} ({{ $agent->name }})</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="space-y-3 w-full mb-3">
                    <input wire:model="amount" type="number" name="amount" id="floating_prix"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Entrez la somme" />
                    @error('amount')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                <button type="reset"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800">
                    Annuler
                </button>
                <button type="submit"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Envoyé</span>
                    <span wire:loading>Sending...</span>

                </button>
            </div>
        </div>
    </form>
</div>


