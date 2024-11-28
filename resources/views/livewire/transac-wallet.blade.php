<div wire:poll.10ms>
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
        {{-- @foreach ($transactions as $transaction)
            @if ($transaction->type == 'Depot' || ($transaction->type == 'Reception' && $transaction->receiver_admin_id == $adminId) || ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminId))
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
        @endforeach --}}

        @foreach ($transactions as $transaction)
            @php
                $isRelevantTransaction =
                    ($transaction->type == 'Réception' && $transaction->receiver_admin_id == $adminID) ||
                    ($transaction->type == 'Envoie' && $transaction->sender_admin_id == $adminID) ||
                    ($transaction->type == 'Commission' && $transaction->receiver_admin_id == $adminID) ||
                    ($transaction->type == 'Depot' && $transaction->receiver_admin_id == $adminID);
            @endphp

            @if ($isRelevantTransaction)
                <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-100 rounded-xl"
                    data-modal-target="static-modal-{{ $transaction->id }}"
                    data-modal-toggle="static-modal-{{ $transaction->id }}">
                    <div class="flex items-center">
                        @php
                            $iconSvg = '';
                            $transactionLabel = '';
                            if ($transaction->type == 'Depot') {
                                $iconSvg =
                                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25"/>';
                                $transactionLabel = 'Rechargement';
                            } elseif ($transaction->type == 'Réception') {
                                $iconSvg =
                                    '<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25"/>';
                                $transactionLabel = $transaction->description ?? ($transaction->description ?? '');
                            } elseif ($transaction->type == 'Envoie') {
                                $iconSvg =
                                    '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/>';
                                $transactionLabel = $transaction->description ?? '';
                            } elseif ($transaction->type == 'Commission') {
                                $iconSvg =
                                    '<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5L4.5 19.5M19.5 4.5H12M19.5 4.5v11.25"/>';
                                $transactionLabel = 'Commission';
                            } elseif ($transaction->type == 'Gele') {
                                $iconSvg =
                                    '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>';
                                $transactionLabel = $transaction->description ?? '';
                            } 
                        @endphp
                        <div
                            class="items-center justify-center hidden w-8 h-8 mr-4 bg-gray-200 rounded-full lg:flex lg:w-10 lg:h-10">
                            <svg class="w-4 h-4 font-bold text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                {!! $iconSvg !!}
                            </svg>
                        </div>

                        <div data-modal-target="static-modal-{{ $transaction->id }}"
                            data-modal-toggle="static-modal-{{ $transaction->id }}">
                            <div class="flex">

                                <h3 class="text-sm font-medium">{{ $transactionLabel }}
                                </h3>
                                <span
                                    class="bg-blue-100 ml-2 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $transaction->type_compte }}</span>
                            </div>
                            <p class="text-sm text-gray-500">
                                @if ($transaction->type == 'Envoie')
                                    Envoie
                                @elseif ($transaction->type == 'Réception' || $transaction->type == 'Depot' || $transaction->type == 'Commission')
                                    Reception
                                @elseif ($transaction->type == 'Gele')
                                    Gele
                                @elseif ($transaction->type == 'withdrawal')
                                    Retrait
                                @endif
                                •
                                {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        @php
                            $amountDisplay = '';
                            $amountClass = '';
                            if (
                                ($transaction->type == 'Depot' ||
                                    $transaction->type == 'Réception' ||
                                    $transaction->type == 'Commission') &&
                                $transaction->receiver_admin_id == $adminID
                            ) {
                                $amountDisplay = '+' . number_format($transaction->amount, 2, ',', ' ') . ' FCFA';
                                $amountClass = 'text-green-500';
                            } elseif (
                                ($transaction->type == 'Envoie' || $transaction->type == 'withdrawal') &&
                                $transaction->sender_admin_id == $adminId
                            ) {
                                $amountDisplay = '-' . number_format($transaction->amount, 2, ',', ' ') . ' FCFA';
                                $amountClass = 'text-red-500';
                            } 
                        @endphp
                        @if ($amountDisplay)
                            <div class="{{ $amountClass }} font-bold text-lg">
                                {{ $amountDisplay }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Main modal -->
                <div id="static-modal-{{ $transaction->id }}" data-modal-backdrop="static-modal-{{ $transaction->id }}"
                    tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="w-full max-w-lg p-6 mx-4 bg-white rounded-lg shadow-lg">

                        <div class="flex items-center justify-between ">

                            <h2 class="mb-4 text-xl font-semibold text-gray-800">Détails de
                                la
                                Transaction
                            </h2>
                            <button type="button"
                                class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="static-modal-{{ $transaction->id }}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Informations de transaction -->
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Type :</span>
                                <span class="font-semibold text-gray-900">{{ $transaction->type }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Type de compte :</span>
                                <span class="font-semibold text-gray-900">{{ $transaction->type_compte }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Description :</span>
                                <span class="font-semibold text-gray-900">{{ $transaction->description }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant :</span>
                                <span
                                    class="font-semibold text-gray-900">{{ number_format($transaction->amount, 2, ',', ' ') }}
                                    FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Destinataire :</span>
                                <span
                                    class="font-semibold text-gray-900">{{ strtoupper($transaction->receiverUser->name ?? $transaction->receiverAdmin->name ?? 'Admin') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Expéditeur :</span>
                                <span
                                    class="font-semibold text-gray-900">{{ strtoupper($transaction->senderAdmin->name) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Date :</span>
                                <span class="font-semibold text-gray-900">29 Octobre
                                    2024</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Référence de transaction
                                    :</span>
                                <span class="font-semibold text-gray-900">{{ $transaction->reference_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Statut :</span>
                                <span class="font-semibold text-green-600">{{ $transaction->status }}</span>
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
