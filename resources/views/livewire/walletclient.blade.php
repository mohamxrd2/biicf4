<div>
    <div class="p-4">
        @if (session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Balance Summary -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="lg:text-3xl text-xl font-semibold">{{ number_format($userWallet->balance, 2, ',', ' ') }} FCFA
                </h1>
                <p class="text-gray-500">Revenus total</p>
            </div>
            <div class="space-x-2">




            </div>
        </div>

        <!-- Currency Cards -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="lg:col-span-1 col-span-3 flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 w-10 h-10 rounded-full mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>

                <div>
                    <h2 class="text-lg font-medium">{{ number_format($userWallet->balance, 2, ',', ' ') }} </h2>
                    <p class="text-sm text-gray-500">Compte d'operation courant</p>
                </div>
            </div>
            <div class="lg:col-span-1 col-span-3  flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 w-10 h-10 rounded-full mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>

                <div>
                    <h2 class="text-lg font-medium">0.00</h2>
                    <p class="text-sm text-gray-500">Compte d'operation boursiere</p>
                </div>
            </div>
            <a  href="#" wire:click="retrait"
                class="lg:col-span-1 col-span-3  flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 w-10 h-10 rounded-full mr-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>

                <div >
                    <p class="text-sm text-gray-500">
                        {{-- <a wire:navigate href="{{ route('biicf.retrait') }}">Retrait</a> --}}
                        Retrait
                    </p>
                </div>
            </a>
            <a href="#" wire:click="transfert"
                class="lg:col-span-1 col-span-3 flex items-center p-4 bg-white border rounded-xl shadow-sm">
                {{-- <a wire:navigate href="{{ route('biicf.retrait') }}" class="flex items-center"> --}}
                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full mr-4">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transfert d'un compte à un autre</p>
                </div>
                {{-- </a> --}}

            </a>
        </div>
        @if ($currentPage === 'transaction')
            <!-- Transactions Section -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Transactions</h2>
                    <div class="lg:flex items-center space-x-2  hidden">

                        <div class="relative">
                            <input type="text" placeholder="Rechercher..."
                                class="px-4 py-2 border border-gray-300 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400 absolute top-3 right-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1116.65 4.35a7.5 7.5 0 010 10.3z"></path>
                            </svg>
                        </div>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg">Filter</button>
                    </div>
                </div>

                <!-- Transaction List -->
                <div>

                    @if ($transacCount == 0)
                        <div class="text-center w-full h-80 flex-col justify-center items-center">
                            <div class="flex justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold mb-2">Aucune transaction</h2>
                            <p class="text-gray-500">Vous verez les historiques des transactions ici !</p>
                        </div>
                    @else
                        @foreach ($transactions as $transaction)
                            @if (
                                ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId) ||
                                    ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId) ||
                                    ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId) ||
                                    ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId) ||
                                    ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId))
                                <div
                                    class="flex justify-between items-center hover:bg-gray-100 p-4 rounded-xl cursor-pointer">
                                    <div class="flex items-center">
                                        @if ($transaction->type == 'Depot' || ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                                </svg>
                                            </div>
                                        @elseif($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 4.5L4.5 19.5M19.5 4.5H12M19.5 4.5v11.25" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div>
                                            @if ($transaction->type == 'Depot')
                                                <h3 class="text-sm font-medium">Rechargement</h3>
                                            @elseif ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId)
                                                <h3 class="text-sm font-medium">
                                                    @if ($transaction->senderAdmin)
                                                        {{ $transaction->senderAdmin->name }}
                                                    @elseif($transaction->senderUser)
                                                        {{ $transaction->senderUser->name }}
                                                    @endif
                                                </h3>
                                            @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">
                                                    @if ($transaction->receiverUser)
                                                        {{ $transaction->receiverUser->name }}
                                                    @endif
                                                </h3>
                                            @elseif ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId)
                                                <h3 class="text-sm font-medium">Commission</h3>
                                            @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">Gele</h3>
                                            @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">Retrait</h3>
                                            @endif
                                            <p class="text-sm text-gray-500">
                                                @if ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                                    Envoie
                                                @elseif (
                                                    ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId) ||
                                                        $transaction->type == 'Depot' ||
                                                        ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                                    Reception
                                                @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                                    Gele pour achat
                                                @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                                    Retrait
                                                @endif
                                                • {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        @if (
                                            ($transaction->type == 'Depot' || $transaction->type == 'Reception' || $transaction->type == 'Commission') &&
                                                $transaction->receiver_user_id == $userId)
                                            <div class="text-sm font-medium text-green-500">
                                                +{{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-blue-600 text-end">
                                                {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-red-500">
                                                {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-red-500">
                                                {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @endif
                    <!-- More transactions... -->
                </div>
            </div>
        @elseif ($currentPage === 'transfert')
            @livewire('transfert-client')
        @elseif ($currentPage === 'retrait')
            @livewire('withdrawal-component')
        @endif

    </div>
</div>

