@extends('admin.layout.navside')

@section('title', 'Porte-feuille')

@section('content')

    <div class="grid grid-cols-3 gap-4">
        <div class="lg:col-span-2 col-span-3">
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
            <div class="bg-black rounded-2xl p-6 flex flex-col justify-between h-40">
                <p class="text-md text-slate-400">Mon compte</p>

                <div class="flex justify-between items-center">
                    <p class="text-3xl text-white font-bold"> {{ number_format($adminWallet->balance, 2, ',', ' ')    }} FCFA</p>

                    <div>

                        @auth('admin')
                            @if (Auth::guard('admin')->user()->admin_type == 'admin')
                                <button class="bg-white bordertext-sm py-2 px-3 rounded-2xl flex items-center"
                                    data-hs-overlay="#hs-basic-modal">

                                    <svg class="flex-shrink-0 size-5 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m0-3-3-3m0 0-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" />
                                    </svg>
                                    Déposer
                                </button>
                            @endif
                        @endauth

                        <div id="hs-basic-modal"
                            class="hs-overlay hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                            <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                <form action="{{ route('wallet.deposit') }}" method="POST">
                                    @csrf

                                    <div
                                        class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                        <div
                                            class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                            <h3 class="font-bold text-gray-800 dark:text-white">
                                                Recharger le compte
                                            </h3>
                                            <button type="button"
                                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                                data-hs-overlay="#hs-basic-modal">
                                                <span class="sr-only">Close</span>
                                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M18 6 6 18"></path>
                                                    <path d="m6 6 12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="p-4 overflow-y-auto">
                                            <div class="space-y-3 w-full mb-3">
                                                <input type="number" name="amount" id="floating_prix"
                                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                    placeholder="Entrez la somme" />
                                            </div>
                                        </div>
                                        <div
                                            class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                            <button type="reset"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                                data-hs-overlay="#hs-basic-modal">
                                                Annuler
                                            </button>
                                            <button type="submit"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                Déposer
                                            </button>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

            @auth('admin')
                @if (Auth::guard('admin')->user()->admin_type == 'admin')
                    <div class="flex justify-between items-center my-6">
                        <p class="text-2xl font-bold">Rapport de finance</p>

                        <select name="" id="" class="px-3 py-1 rounded-2xl">
                            <option value="">Mois</option>
                            <option value="Année">Année</option>
                            <option value="">Depuis toujour</option>
                        </select>

                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="lg:col-span-1 col-span-3">
                            <div class="rounded-2xl p-6 flex flex-col justify-between  h-32 bg-green-100">

                                <p class="text-md text-slate-400">Total envoyé</p>

                                <p class="text-xl text-black font-bold"> 5,234,234 FCFA</p>


                            </div>
                        </div>
                        <div class="lg:col-span-1 col-span-3">
                            <div class="bg-black rounded-2xl p-6 flex flex-col justify-between  h-32 bg-orange-100">
                                <p class="text-md text-slate-400">Total reçu</p>
                                <div>
                                    <p class="text-xl text-black font-bold"> 5,234,234 FCFA</p>
                                </div>

                            </div>
                        </div>
                        <div class="lg:col-span-1 col-span-3">
                            <div class="bg-black rounded-2xl p-6 flex flex-col justify-between  h-32 bg-violet-100">
                                <p class="text-md text-slate-400">Total sur le compte</p>
                                <div>
                                    <p class="text-xl text-black font-bold"> 5,234,234 FCFA</p>
                                </div>

                            </div>
                        </div>

                    </div>
                @endif
            @endauth
            <div class="flex justify-between items-center my-6">
                <p class="text-2xl font-bold">Transactions</p>

                <select name="" id="" class="px-3 py-1 rounded-2xl">
                    <option value="">Mois</option>
                    <option value="Année">Année</option>
                    <option value="">Depuis toujour</option>
                </select>

            </div>
            <div>
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
                        {{ $transactions->links() }}
                    </div>

                @endif





            </div>


        </div>
        <div class="lg:col-span-1 col-span-3 ">

            <div>

                @auth('admin')
                    @if (Auth::guard('admin')->user()->admin_type == 'admin')
                        <div class="w-full p-5 bg-white border flex items-center rounded-2xl hover:bg-gray-50 mb-4 cursor-pointer"
                            data-hs-overlay="#monney-send1">

                            <div class="flex flex-col">
                                <p class="font-bold  mb-3">Envoyé a un agent</p>
                                <div class="flex items-center">
                                    <div class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>

                                    <div class="flex -space-x-4 rtl:space-x-reverse">

                                        @if ($agentCount == 0)
                                            <p class="text-gray-600">Aucun agent</p>
                                        @else
                                            @foreach ($agents->take(5) as $agent)
                                                <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                                    src="{{ asset($agent->photo) }}" alt="">
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                @endauth

                <form action="{{ route('recharge.account') }}" method="POST">
                    @csrf
                    <div id="monney-send1"
                        class="hs-overlay h-auto hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                        <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                            <div
                                class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                    <h3 class="font-bold text-gray-800 dark:text-white">
                                        Recharger le compte
                                    </h3>
                                    <button type="button"
                                        class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#monney-send1">
                                        <span class="sr-only">Close</span>
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M18 6 6 18"></path>
                                            <path d="m6 6 12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4 overflow-y-auto">

                                    <div class=" w-full mb-3">

                                        <input type="hidden" id="agent_id" name="agent_id" value="">

                                        <div class="relative" data-hs-combo-box="">
                                            <div class="relative">
                                                <input
                                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                                    type="text" placeholder="Entrez le nom de l'agent"
                                                    data-hs-combo-box-input="">
                                                <div class="absolute top-1/2 end-3 -translate-y-1/2"
                                                    data-hs-combo-box-toggle="">
                                                    <svg class="flex-shrink-0 size-3.5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m7 15 5 5 5-5"></path>
                                                        <path d="m7 9 5-5 5 5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="relative z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                                                style="display: none;" data-hs-combo-box-output="">

                                                <!-- Utiliser la boucle foreach pour générer les éléments de la liste déroulante -->
                                                @foreach ($agents as $agent)
                                                    <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                                        tabindex="{{ $loop->index }}"
                                                        data-hs-combo-box-output-item="{{ $agent->id }}">
                                                        <div class="flex">
                                                            <img class="w-5 h-5 mr-2 rounded-full"
                                                                src="{{ asset($agent->photo )}}" alt="">
                                                            <div class="flex justify-between items-center w-full">
                                                                <span data-hs-combo-box-search-text="{{ $agent->username }}"
                                                                    data-hs-combo-box-value="{{ $agent->id }}">{{ $agent->username }}({{ $agent->name }})</span>
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
                                        <input type="number" name="amount" id="floating_prix"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Entrez la somme" />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                    <button type="reset"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                        data-hs-overlay="#monney-send1">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Envoyé
                                    </button>
                                </div>
                            </div>



                        </div>
                    </div>
                </form>





                <div class="w-full p-5 bg-white border flex items-center rounded-2xl hover:bg-gray-50 mb-4 cursor-pointer"
                    data-hs-overlay="#monney-send2">

                    <div class="flex flex-col">
                        <p class="font-bold  mb-3">Envoyé a un client</p>
                        <div class="flex items-center">
                            <div class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>

                            <div class="flex -space-x-4 rtl:space-x-reverse">

                                @if ($userCount == 0)

                                    <p class="text-gray-600">Aucun client</p>
                                @else
                                    @foreach ($users->take(5) as $user)
                                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                            src="{{ asset($user->photo) }}" alt="">
                                    @endforeach

                                @endif



                            </div>

                        </div>

                    </div>

                </div>

                <form action="{{ route('recharge.clientaccount') }}" method="POST">
                    @csrf

                    <div id="monney-send2"
                        class="hs-overlay h-auto hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                        <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                            <div
                                class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                    <h3 class="font-bold text-gray-800 dark:text-white">
                                        Recharger le compte
                                    </h3>
                                    <button type="button"
                                        class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#monney-send2">
                                        <span class="sr-only">Close</span>
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
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
                                                <div class="absolute top-1/2 end-3 -translate-y-1/2"
                                                    data-hs-combo-box-toggle="">
                                                    <svg class="flex-shrink-0 size-3.5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m7 15 5 5 5-5"></path>
                                                        <path d="m7 9 5-5 5 5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="relative z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                                                style="display: none;" data-hs-combo-box-output="">

                                                <!-- Utiliser la boucle foreach pour générer les éléments de la liste déroulante -->
                                                @foreach ($users as $user)
                                                    <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                                        tabindex="{{ $loop->index }}"
                                                        data-hs-combo-box-output-item="{{ $user->id }}">
                                                        <div class="flex">
                                                            <img class="w-5 h-5 mr-2 rounded-full"
                                                                src="{{ asset($user->photo) }}" alt="">
                                                            <div class="flex justify-between items-center w-full">
                                                                <span data-hs-combo-box-search-text="{{ $user->username }} "
                                                                    data-hs-combo-box-value="{{ $user->id }}">{{ $user->username }}({{$user->name}})</span>
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
                                        <input type="number" name="amount" id="floating_prix"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Entrez la somme" />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                    <button type="reset"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                        data-hs-overlay="#monney-send2">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Envoyé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comboBoxItems = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const agentIdInput = document.getElementById('agent_id');

            comboBoxItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const agentId = item.getAttribute('data-hs-combo-box-output-item');
                    agentIdInput.value = agentId;
                });
            });

            const comboBoxItems2 = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const userIdInput = document.getElementById('user_id');

            comboBoxItems2.forEach(function(item) {
                item.addEventListener('click', function() {
                    const userId = item.getAttribute('data-hs-combo-box-output-item');
                    userIdInput.value = userId;
                });
            });
        });
    </script>

@endsection
