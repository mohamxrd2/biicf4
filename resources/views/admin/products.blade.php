@extends('admin.layout.navside')

@section('title', 'Produits')


@section('content')
    @auth('admin')
        @if (Auth::guard('admin')->user()->admin_type == 'agent')
            @include('admin.produit_services.products_agent')
        @else
            <div class=" relative overflow-x-auto  sm:rounded-lg">
                <div id="resultsContainer"
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
                                quantite traité
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Prix
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Statuts
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
                    
                </table>
                <livewire:publication-produits lazy/>





            </div>
            
        @endif
    @endauth



    <script src="{{ asset('js/search.js') }}"></script>




@endsection
