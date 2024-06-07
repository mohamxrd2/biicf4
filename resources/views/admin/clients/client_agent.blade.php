<div class="relative overflow-x-auto sm:rounded-lg">
    <div
        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
        <div>
            <h1 class="font-bold text-2xl">Liste des clients</h1>
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
                    class="block w-80 p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Rechercher...">
            </div>
            <a href="{{ route('clients.create') }}"
                class="inline-flex items-center gap-x-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg border border-transparent hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                data-hs-overlay="#hs-static-backdrop-modal">
                Ajouter
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Nom</th>
                <th scope="col" class="px-6 py-3">Téléphone</th>

                <th scope="col" class="px-6 py-3">
                    Status
                </th>
            </tr>
        </thead>
        <tbody>

            @if ($userAgentcount == 0)
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center">
                        <div class="flex flex-col justify-center items-center h-40 w-full">
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
                @foreach ($userAgent as $user)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                            <a href="{{ route('client.show', ['username' => $user->username]) }}"
                                class="flex items-center">
                                <img class="w-10 h-10 rounded-full object-cover" src="{{ asset($user->photo) }}" alt="">
                                <div class="ml-3">
                                    <div class="text-base font-semibold">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $user->phone }}</td>

                        <td class="px-6 py-4">
                            <span
                                class="bg-{{ $user->last_seen >= now()->subMinutes(2) ? 'green' : 'red' }}-500 text-white py-1 px-3 rounded-md">
                                {{ $user->last_seen >= now()->subMinutes(2) ? 'Online' : 'Offline' }}
                            </span>
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
    <div class="my-5 flex justify-end">
        {{ $userAgent->links() }}
    </div>

</div>
