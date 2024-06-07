@extends('admin.layout.navside')

@section('title', 'services')

@section('content')
    @auth('admin')
        @if (Auth::guard('admin')->user()->admin_type == 'agent')
            @include('admin.produit_services.services_agent')
        @else
            <div class=" relative overflow-x-auto  sm:rounded-lg">
                <div
                    class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                    <div>
                        <h1 class="bold" style="font-size: 24px;">Liste des @yield('title')</h1>

                    </div>
                    <div class="flex items-center">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative mr-2">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text" id="searchInput" onkeyup="searchTable()"
                                class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Rechercher...">
                        </div>

                    </div>

                </div>

                @if (session('success'))
                    <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                @endif


                <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                nom & photo
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Prix
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Experiance
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>

                            <th scope="col" class="px-6 py-3">
                                utilisateur
                            </th>

                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                            <th scope="col" class="px-6 py-3">
                                date de creation
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @if ($servieCount == 0)
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">
                                    <div class="flex flex-col justify-center items-center h-72 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun service</h1>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($services as $service)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row"
                                        class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="{{ route('produit.pubShow', $service->id) }}" class="flex items-center">

                                            <img class="w-10 h-10 rounded-md"
                                            src="{{ $service->photoProd1 ? asset($service->photoProd1) : asset('img/noimg.jpeg') }}">
                                            <div class="ps-3">
                                                <div class="text-base font-semibold">{{ $service->name }}</div>
                                                <div class="font-normal text-gray-500">{{ $service->username }}</div>
                                            </div>
                                        </a>
                                    </th>

                                    <td class="px-6 py-4">
                                        <p class="mb-0">{{ $service->prix }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="mb-0">{{ $service->qalifServ }} ans</p>
                                    </td>
                                    <td class="px-6 py-4">

                                        @if ($service->statuts == 'En attente')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100 dark:text-red-400 dark:bg-red-200">{{ $service->statuts }}</span>
                                        @elseif ($service->statuts == 'Accepté')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100 dark:text-red-400 dark:bg-red-200">{{ $service->statuts }}</span>
                                        @elseif ($service->statuts == 'Refusé')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-200">{{ $service->statuts }}</span>
                                        @endif

                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($service->user)
                                            <a href="{{ route('client.show', ['username' => $service->user->username]) }}">
                                                <p class="mb-0">{{ $service->user->username }}</p>
                                            </a>
                                        @else
                                            <p class="mb-0">Utilisateur inconnu</p>
                                        @endif
                                    </td>



                                    <td class="px-6 py-4">
                                        <div class="flex">
                                            <a href="#" data-hs-overlay="#hs-delete-{{ $service->id }}"
                                                class="font-medium text-red-600 dark:text-blue-500  mr-2">
                                                <button type="submit"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg></button>
                                            </a>
                                        </div>

                                        <div id="hs-delete-{{ $service->id }}"
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
                                                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2"
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
                                                                height="16" fill="currentColor" viewBox="0 0 16 16">
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
                                                            Vous etes sur de supprimé le service ?
                                                        </p>

                                                        <div class="mt-6 flex justify-center gap-x-4">
                                                            <form action="{{ route('admin.services.destroy', $service->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                                                    Supprimer
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
                                    <td class="px-6 py-4">
                                        <p class="mb-0">{{ \Carbon\Carbon::parse($service->created_at)->diffForHumans() }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <!-- Message d'aucun résultat trouvé -->
                <div id="noResultMessage" class="h-20 flex justify-center items-center" style="display: none;">Aucun résultat
                    trouvé.</div>
            </div>
            <div class="my-5 flex justify-end">
                {{ $services->links() }}
            </div>

        @endif
    @endauth




    <script src="{{ asset('js/search.js') }}"></script>




@endsection
