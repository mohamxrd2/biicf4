@extends('admin.layout.navside')

@section('title', 'Details Agent')

@section('content')


    <div class="w-full flex justify-between items-center p-6 bg-gray-50 border border-gray-200 rounded-lg  dark:bg-neutral-800">


        <div class="flex md:gap-8 gap-4 items-center md:p-8 p-6 md:pb-4">

            <div class="relative md:w-20 md:h-20 w-12 h-12 shrink-0">
                <label for="file" class="cursor-pointer">
                    <img id="img" src="{{ asset($agent->photo) }}" class="object-cover w-full h-full rounded-full"
                        alt="" />
                </label>

            </div>


            <div class="flex-1">
                <h3 class="md:text-xl text-base font-semibold text-black dark:text-white">{{ $agent->name }}</h3>
                <p class="text-sm text-blue-600 mt-1 font-normal">{{ '@' . $agent->username }}</p>
            </div>


        </div>
        <div> 
            <div  class=" p-3 border border-gray-200 bg-white rounded-md">
                <p class="text-sm">Solde</p>
                <p class="text-2xl font-bold">{{ $wallet->balance }} FCFA</p>

            </div>

        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 my-4">
        <div class="lg:col-span-2 col-span-3 ">
            @if (session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
            <div class="p-4 bg-white border border-gray-200 rounded-md">

                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div>
                            <h1 class="font-bold text-md">Liste des clients</h1>
                        </div>
                        <div class="flex items-center">
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative mr-2">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" onkeyup="searchTable()"
                                    class="block w-80 p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Rechercher...">
                            </div>

                        </div>
                    </div>

                   

                    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom</th>
                                <th scope="col" class="px-6 py-3">Téléphone</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($userCount == 0)

                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">
                                        <div class="flex flex-col justify-center items-center h-40">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun utilisateur</h1>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($users as $user)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                            <a href="{{ route('client.show', ['username' => $user->username]) }}"
                                                class="flex items-center">
                                            <img class="w-10 h-10 rounded-full" src="{{ asset($user->photo )}}" alt="">
                                            <div class="ml-3">
                                                <div class="text-base font-semibold">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                            </div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">{{ $user->phone }}</td>
                                        <td class="px-6 py-4">
                                            <span class="bg-{{ $user->last_seen >= now()->subMinutes(2) ? 'green' : 'red' }}-500 text-white py-1 px-3 rounded-md">
                                                {{ $user->last_seen >= now()->subMinutes(2) ? 'Online' : 'Offline' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex">
                                                <a href="#" data-hs-overlay="#hs-delete-{{ $user->id }}"
                                                    class="mr-2 font-medium text-red-600 dark:text-blue-500">
                                                    <button type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </a>
                                            </div>
                                            <div id="hs-delete-{{ $user->id }}"
                                                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                                                <div
                                                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                                    <div
                                                        class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                                                        <div class="absolute top-2 end-2">
                                                            <button type="button"
                                                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                                                data-hs-overlay="#hs-delete">
                                                                <span class="sr-only">Close</span>
                                                                <svg class="flex-shrink-0 size-4"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M18 6 6 18" />
                                                                    <path d="m6 6 12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
        
                                                        <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                                            <!-- Icon -->
                                                            <span
                                                                class="mb-4 inline-flex justify-center items-center size-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500 dark:bg-yellow-700 dark:border-yellow-600 dark:text-yellow-100">
                                                                <svg class="flex-shrink-0 size-5"
                                                                    xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                                                </svg>
                                                            </span>
                                                            <!-- End Icon -->
        
                                                            <h3
                                                                class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                                                Supprimé
                                                            </h3>
                                                            <p class="text-gray-500 dark:text-neutral-500">
                                                                Vous etes sur de supprimé le compte de l'utilisateur
                                                                ?
                                                            </p>
        
                                                            <div class="mt-6 flex justify-center gap-x-4">
                                                                <form
                                                                    action="{{ route('admin.user.destroy', $user->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
        
                                                                    <button type="submit"
                                                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                                                        Supprimé
                                                                    </button>
                                                                </form>
                                                                <button type="button"
                                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                                                    data-hs-overlay="#hs-delete">
                                                                    Annuler
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <!-- Message d'aucun résultat trouvé -->
                    <div id="noResultMessage" class="h-20 flex justify-center items-center" style="display: none;">
                        Aucun résultat trouvé.
                    </div>

                </div>
                <div class="my-5 flex justify-end">

                </div>

            </div>

            <div class="p-4 mt-4 bg-white border border-gray-200 rounded-md">
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div>
                            <h1 class="font-bold text-md">Transactions</h1>
                        </div>

                        <div class="flex items-center">
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative mr-2">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" onkeyup="searchTable()"
                                    class="block w-80 p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Rechercher...">
                            </div>
                        </div>

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
            </div>


        </div>
        <div class="lg:col-span-1 col-span-3  ">
            <div class="flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">
                <div class="mb-3">
                    <h1 class="font-bold text-md">Information personnel</h1>
                </div>
                <div class="mb-3">
                    <p class="font-semibold text-sm">Nom et pronom</p>
                    <p class="text-sm text-gray-400">{{ $agent->name }}</p>
                </div>
                <div class="mb-3">
                    <p class="font-semibold text-sm">Numero de téléphone</p>
                    <p class="text-sm text-gray-400">{{ $agent->phonenumber }}</p>
                </div>

                <div class="mb-3">
                    <p class="font-semibold text-sm">Nombre de client enregistré</p>
                    <p class="text-sm text-gray-400">{{ $userCount }}</p>
                </div>

                <form action="{{ route('admin.agent.isban', $agent->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="w-full mb-3">
                        @if($agent->isban)



                        <div class="text-gray-800 bg-gray-200 rounded-md text-center p-1" title="Debloquer">
                            Debloquer

                        </div>
                        @else
                        <div class="text-yellow-800 bg-yellow-100 rounded-md text-center p-1 " title="bloquer">
                            Bloquer
                        </div>
                        
                          
                        @endif
                    </button>
                </form>

                <a href="#" data-hs-overlay="#hs-delete1"
                    class="w-full  text-red-800 bg-red-100 rounded-md text-center p-1 ">
                    Supprimé l'agent

                </a>

                <div id="hs-delete1"
                    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div
                        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-delete1">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <!-- Icon -->
                                <span
                                    class="mb-4 inline-flex justify-center items-center size-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500 dark:bg-yellow-700 dark:border-yellow-600 dark:text-yellow-100">
                                    <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                    </svg>
                                </span>
                                <!-- End Icon -->

                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Supprimé
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    Vous etes sur de supprimé le compte de l'agent ?
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('admin.agent.destroy', $agent->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                            Supprimé
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                        data-hs-overlay="#hs-delete1">
                                        Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





            </div>

        </div>
    </div>





@endsection
