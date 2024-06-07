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
                    date de creation
                </th>
            </tr>
        </thead>
        <tbody>
            @if ($produitAgentsCount == 0)

                <tr>
                    <td colspan="6" class="px-6 py-4 text-center">
                        <div class="flex flex-col justify-center items-center h-40">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun produit 
                            </h1>
                        </div>
                    </td>
                </tr>
            @else
                @foreach ($produitAgents as $produit)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                        <th scope="row"
                            class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                            <img class="w-10 h-10 rounded-md"
                                src="{{ $produit->photo ? $produit->photo : asset('img/noimg.jpeg') }}"
                                alt="Jese image">
                            <div class="ps-3">
                                <div class="text-base font-semibold">{{ $produit->name }}</div>
                                <div class="font-normal text-gray-500">{{ $produit->username }}</div>
                            </div>
                        </th>

                        <td class="px-6 py-4">
                            <p class="mb-0"> [{{ $produit->qteProd_min }} - {{ $produit->qteProd_max }}]</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="mb-0">{{ $produit->prix }}</p>
                        </td>
                        <td class="px-6 py-4">

                            @if ($produit->statuts == 'En attente')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100 dark:text-red-400 dark:bg-red-200">{{ $produit->statuts }}</span>
                            @elseif ($produit->statuts == 'Accepté')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100 dark:text-red-400 dark:bg-red-200">{{ $produit->statuts }}</span>
                            @elseif ($produit->statuts == 'Refusé')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-200">{{ $produit->statuts }}</span>
                            @endif

                        </td>
                        <td class="px-6 py-4">
                            @if ($produit->user)
                                <a href="{{ route('client.show', ['username' => $produit->user->username]) }}">
                                    <p class="mb-0">{{ $produit->user->username }}</p>
                                </a>
                            @else
                                <p class="mb-0">Utilisateur inconnu</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="mb-0">{{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}</p>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>

    </table>





</div>
<div class="my-5 flex justify-end">
    {{ $produitAgents->links() }}
</div>
