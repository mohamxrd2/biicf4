@extends('biicf.layout.navside')

@section('title', 'Porte-feuille')

@section('content')

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
                <h1 class="lg:text-3xl text-xl font-semibold">{{ number_format($userWallet->balance, 2, ',', ' ') }} FCFA </h1>
                <p class="text-gray-500">Revenus total</p>
            </div>
            <div class="space-x-2">
                <button class="px-4 py-2 flex items-center bg-purple-600 text-white rounded-lg"
                    data-hs-overlay="#monney-send">
                    <svg class="w-4 h-4 text-white font-bold mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                    <p>Envoyé</p>
                </button>
                <form action="{{ route('biicf.send') }}" method="POST">
                    @csrf
                    <div id="monney-send"
                        class="hs-overlay h-auto hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                        <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                            <div
                                class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                    <h3 class="font-bold text-gray-800 dark:text-white">
                                        Envoyé de l'argent
                                    </h3>
                                    <button type="button"
                                        class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#monney-send">
                                        <span class="sr-only">Close</span>
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"></path>
                                            <path d="m6 6 12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4 overflow-y-auto">

                                    <div class=" w-full mb-3">

                                        <input type="hidden" id="user_id" name="user_id" value="">

                                        <div class="relative" data-hs-combo-box="">
                                            <div class="relative">
                                                <input
                                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                                    type="text" placeholder="Entrez le nom du client"
                                                    data-hs-combo-box-input="">

                                            </div>
                                            <div class="relative z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                                                style="display: none;" data-hs-combo-box-output="">

                                                @foreach ($users as $user)
                                                    <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                                        tabindex="{{ $loop->index }}"
                                                        data-hs-combo-box-output-item="{{ $user->id }}">
                                                        <div class="flex">
                                                            <img class="w-5 h-5 mr-2 rounded-full"
                                                                src="{{ asset($user->photo) }}" alt="">
                                                            <div class="flex justify-between items-center w-full">
                                                                <span data-hs-combo-box-search-text="{{ $user->username }} "
                                                                    data-hs-combo-box-value="{{ $user->id }}">{{ $user->username }}({{ $user->name }})</span>
                                                                <span class="hidden hs-combo-box-selected:block">
                                                                    <svg class="flex-shrink-0 size-3.5 text-blue-600"
                                                                        xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M20 6 9 17l-5-5"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <div class="space-y-3 w-full mb-3">
                                        <input type="number" id="input_somme_envoye"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Somme envoyé" />
                                    </div>

                                    <div class="space-y-3 w-full mb-3">
                                        <input type="number" id="input_somme_recu" disabled
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Somme réçu" />
                                    </div>

                                    <input type="hidden" name="amount" id="hidden_somme_recu" value="" />

                                    <input type="hidden" name="comAmount" id="poucent_somme" value="">

                                    <div class="w-full">
                                        <p class="text-center text-slate-500 text-sm">Les frais de transaction sont à 1%</p>

                                    </div>

                                </div>
                                <div
                                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                    <button type="reset"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                        data-hs-overlay="#monney-send">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Envoyé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- Currency Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="lg:col-span-1 col-span-3 flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <img src="https://via.placeholder.com/40x40" alt="US Flag" class="w-10 h-10 rounded-full mr-4">
                <div>
                    <h2 class="text-lg font-medium">{{ number_format($userWallet->balance, 2, ',', ' ') }} </h2>
                    <p class="text-sm text-gray-500">Compte d'operation courant</p>
                </div>
            </div>
            <div class="lg:col-span-1 col-span-3  flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <img src="https://via.placeholder.com/40x40" alt="UK Flag" class="w-10 h-10 rounded-full mr-4">
                <div>
                    <h2 class="text-lg font-medium">0.00</h2>
                    <p class="text-sm text-gray-500">Compte d'operation boursiere</p>
                </div>
            </div>
            <div class="lg:col-span-1 col-span-3 flex items-center p-4 bg-white border rounded-xl shadow-sm">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full mr-4">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-medium">Ouvrir</h2>
                    <p class="text-sm text-gray-500">Transfert d'un compte à un autre</p>
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Transactions</h2>
                <div class="lg:flex items-center space-x-2  hidden">

                    <div class="relative">
                        <input type="text" placeholder="Rechercher..."
                            class="px-4 py-2 border border-gray-300 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 absolute top-3 right-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
                                ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId))
                            <div class="flex justify-between items-center hover:bg-gray-100 p-4 rounded-xl cursor-pointer">
                                <div class="flex items-center">
                                    @if ($transaction->type == 'Depot' || ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                        <div
                                            class="bg-gray-200 flex justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                            <svg class="w-4 h-4 text-black font-bold" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                            </svg>
                                        </div>
                                    @elseif($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId)
                                        <div
                                            class="bg-gray-200 flex justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                            <svg class="w-4 h-4 text-black font-bold" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                            </svg>
                                        </div>
                                    @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                        <div
                                            class="bg-gray-200 flex justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                            <svg class="w-4 h-4 text-black font-bold" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                            </svg>
                                        </div>
                                    @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                        <div
                                            class="bg-gray-200 flex justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                            <svg class="w-4 h-4 text-black font-bold" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
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
                                                @elseif($transaction->receiverUser)
                                                    {{ $transaction->receiverUser->name }}
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
                                        @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id== $userId)
                                            <h3 class="text-sm font-medium">Gele</h3>
                                        @endif
                                        <p class="text-sm text-gray-500">
                                            @if ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                                Envoie
                                            @elseif (
                                                ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId) ||
                                                    $transaction->type == 'Depot' ||
                                                    ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId) 
                                                    )
                                                Reception
                                            @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                               Gele pour achat
                                            @endif
                                            • {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                        </p>
                                    </div>
                                </div>
                                @if (
                                    (($transaction->type == 'Depot' || $transaction->type == 'Reception') &&
                                        $transaction->receiver_user_id == $userId) ||
                                        ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                    <div class="text-sm font-medium text-green-500">
                                        +{{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                    </div>
                                @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                  <div class="flex flex-col justify-end">
                                    <div class="text-sm font-medium text-blue-600 text-end">
                                        {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                    </div>
                                    
                                  </div>
                                   
                                @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                    <div class="text-sm font-medium text-red-500">
                                        -{{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach





                @endif



                <!-- More transactions... -->
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            const comboBoxItems2 = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const userIdInput = document.getElementById('user_id');

            comboBoxItems2.forEach(function(item) {
                item.addEventListener('click', function() {
                    const userId = item.getAttribute('data-hs-combo-box-output-item');
                    userIdInput.value = userId;
                });
            });
        });

        document.getElementById('input_somme_envoye').addEventListener('input', function() {
            let sommeEnvoye = parseFloat(this.value);
            if (!isNaN(sommeEnvoye)) {
                let pourcentSomme = sommeEnvoye * 0.01;
                let sommeRecu = sommeEnvoye - pourcentSomme;
                // Arrondir à un multiple de 10
                let sommeRecuArrondi = Math.round(sommeRecu / 5) * 5;

                document.getElementById('input_somme_recu').value = sommeRecuArrondi;
                document.getElementById('hidden_somme_recu').value = sommeRecuArrondi;
                document.getElementById('poucent_somme').value = pourcentSomme.toFixed(2);
            } else {
                document.getElementById('input_somme_recu').value = '';
                document.getElementById('hidden_somme_recu').value = '';
                document.getElementById('poucent_somme').value = '';
            }
        });
    </script>
@endsection
