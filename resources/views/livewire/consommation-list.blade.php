<div wire:poll.15000ms>

    <div
        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
        <div>
            <h1 class="bold" style="font-size: 24px;">Liste des consommations en produits</h1>
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

    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>

                <th scope="col" class="px-12 py-3">
                    Nom
                </th>

                <th scope="col" class="px-8 py-3">
                    Quantité
                </th>
                <th scope="col" class="px-6 py-3">
                    Prix
                </th>
                <th scope="col" class="px-6 py-3">
                    Fréquence de consommation
                </th>

                <th scope="col" class="px-6 py-3">
                    Statuts
                </th>

                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>

        <tbody>

            @foreach ($consommations as $consommation)
                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                    <th scope="row"
                        class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('consommation.consoShow', $consommation->id) }}" class="flex items-center">

                            <div class="ps-3 hover:underline hover:text-blue-500 cursor-pointer">
                                <div class="text-base font-semibold">{{ $consommation->name }}</div>
                            </div>
                        </a>
                    </th>

                    <td class="px-6 py-4">
                        <p class="mb-0">{{ $consommation->qte }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="mb-0">{{ $consommation->prix }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="mb-0">{{ $consommation->frqce_cons }}</p>
                    </td>


                    <td class="px-6 py-4">
                        @if ($consommation->statuts == 'En attente')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100 dark:text-red-400 dark:bg-red-200">{{ $consommation->statuts }}</span>
                        @elseif ($consommation->statuts == 'Accepté')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100 dark:text-red-400 dark:bg-red-200">{{ $consommation->statuts }}</span>
                        @elseif ($consommation->statuts == 'Refusé')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-200">{{ $consommation->statuts }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex font-medium text-red-600 dark:text-blue-500 mr-2">
                            <button wire:click="delete({{ $consommation->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>




    <div class="my-5 flex justify-end">
        {{ $consommations->links('vendor.livewire.tailwind') }}
    </div>
</div>
