<div>
    <div class="p-4 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Transactions</h2>
            <div class="items-center hidden space-x-2 lg:flex">
                <div class="relative">
                    <input type="text" placeholder="Rechercher..." class="px-4 py-2 border border-gray-300 rounded-lg">
                    <svg class="absolute w-5 h-5 text-gray-400 top-3 right-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1116.65 4.35a7.5 7.5 0 010 10.3z"></path>
                    </svg>
                </div>
                <button class="px-4 py-2 border border-gray-300 rounded-lg">Filter</button>
            </div>
        </div>

        <div>
            @forelse ($transactions as $transaction)
                <x-transaction-item :transaction="$transaction" :userId="$userId" />
            @empty
                <x-empty-transactions />
            @endforelse
        </div>

        @if ($hasMoreTransactions)
            <div class="mt-6 text-center">
                <button wire:click="loadMoreTransactions" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Voir plus
                </button>
            </div>
        @endif
    </div>
</div>
