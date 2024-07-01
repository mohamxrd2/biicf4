<div  wire:poll.10ms>
    <div class="flex justify-between items-center my-6">
        <p class="text-2xl font-bold">Transactions</p>

        <select name="" id="" class="px-3 py-1 rounded-2xl">
            <option value="">Mois</option>
            <option value="Année">Année</option>
            <option value="">Depuis toujour</option>
        </select>

    </div>
    <hr>
    @if ($transacCount == 0)
        <hr>
        <div class="w-full h-32 flex justify-center items-center">

            <div class="flex flex-col justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-12 h-12 text-gray-500 dark:text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucune transaction</h1>
            </div>
        </div>
    @else
        @foreach ($transactions as $transaction)
            @if (
                $transaction->type == 'Depot' ||
                    ($transaction->type == 'Reception' && $transaction->receiver_admin_id == $adminId) ||
                    ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId))
                <div
                    class="w-full flex items-center hover:bg-gray-100 rounded-xl p-4 focus:outline-none focus:bg-gray-100 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">

                    @if ($transaction->type == 'Depot')
                        <div
                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                            </svg>

                        </div>
                    @elseif ($transaction->type == 'Reception' && $transaction->receiver_admin_id == $adminId)
                        <div
                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                            </svg>

                        </div>
                    @elseif ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId)
                        <div
                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>

                        </div>
                    @endif


                    <div class="w-full">
                        <div class="flex justify-between items-center">
                            <div>
                                @if ($transaction->type == 'Depot')
                                    <h4 class="font-medium dark:text-white">Rechargement</h4>
                                @elseif ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId)
                                    <h4 class="font-medium dark:text-white">Envoyé à
                                        @if ($transaction->receiverAdmin)
                                            {{ $transaction->receiverAdmin->name }}
                                        @endif

                                        @if ($transaction->receiverUser)
                                            {{ $transaction->receiverUser->name }}
                                        @endif
                                    </h4>
                                @elseif ($transaction->type == 'Reception' && $transaction->receiver_admin_id == $adminId)
                                    <h4 class="font-medium dark:text-white">Reçu de
                                        {{ $transaction->senderAdmin->name }}</h4>
                                @endif
                                <ul class="flex">
                                    @if ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId)
                                        <li class="mr-2 dark:text-neutral-500">Envoyé le</li>
                                        <li class="dark:text-neutral-500">
                                            {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                        </li>
                                    @elseif (($transaction->type == 'Reception' && $transaction->receiver_admin_id == $adminId) || $transaction->type == 'Depot')
                                        <li class="mr-2 dark:text-neutral-500">Reçu le</li>
                                        <li class="dark:text-neutral-500">
                                            {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                        </li>
                                    @endif

                                </ul>
                            </div>
                            <div>
                                @if (($transaction->type == 'Depot' || $transaction->type == 'Reception') && $transaction->receiver_admin_id == $adminId)
                                    <p class="text-md text-green-400 font-bold dark:text-white">+
                                        {{ $transaction->amount }} FCFA</p>
                                @elseif ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId)
                                    <p class="text-md text-red-600  font-bold dark:text-white">-
                                        {{ $transaction->amount }} FCFA</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        @endforeach

        <div class="my-5 flex justify-end">
            {{ $transactions->links('vendor.livewire.tailwind') }}
        </div>

    @endif

    

</div>
