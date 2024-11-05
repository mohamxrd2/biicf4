<div id="search-input">



    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12 rounded-lg">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <!-- Heading & Filters -->
            <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                            <li class="inline-flex items-center">
                                <a href="#"
                                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                                    <svg class="me-2.5 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                    </svg>
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m9 5 7 7-7 7" />
                                    </svg>
                                    <a href="#"
                                        class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white md:ms-2">Produits
                                        & Services</a>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    @if ($searchResults)
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                            Recherche effectuée sur
                        </h2>
                        <li><strong>Mot-clé:</strong> {{ $searchResults['keyword'] }}</li>
                        <li><strong>Zone économique:</strong> {{ $searchResults['zone_economique'] }}</li>
                        <li><strong>Type:</strong> {{ $searchResults['type'] }}</li>
                        <li><strong>Quantité:</strong> {{ $searchResults['qte'] }}</li>
                        <li><strong>Prix unitaire:</strong> {{ $searchResults['prix'] }}</li>
                    @endif
                </div>
                <div class="flex items-center space-x-4">

                    <!-- Modal btn toggle -->
                    <button data-modal-target="static-modal" data-modal-toggle="static-modal"
                        class="flex w-full items-center justify-center  text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                        </svg>
                        Filtres
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Main modal -->
                    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <!-- Modal header -->
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Filtres
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="static-modal">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-4 md:p-5 space-y-4">
                                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                                        <ul class="-mb-px flex flex-wrap text-center text-sm font-medium" id="myTab"
                                            data-tabs-toggle="#myTabContent" role="tablist">
                                            <li class="mr-1" role="presentation">
                                                <button class="inline-block pb-2 pr-1" id="brand-tab"
                                                    data-tabs-target="#brand" type="button" role="tab"
                                                    aria-controls="profile" aria-selected="false">Thèmes les plus
                                                    rechercher</button>
                                            </li>
                                            <li class="mr-1" role="presentation">
                                                <button
                                                    class="inline-block px-2 pb-2 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                                    id="advanced-filers-tab" data-tabs-target="#advanced-filters"
                                                    type="button" role="tab" aria-controls="advanced-filters"
                                                    aria-selected="false">Filtres
                                                    avancés</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="myTabContent">
                                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3" id="brand"
                                            role="tabpanel" aria-labelledby="brand-tab">
                                            <div
                                                class="space-y-3.5 capitalize text-xs font-normal mt-5 mb-2 text-gray-600 dark:text-white/80">
                                                @foreach ($searchQueries as $searchQuery)
                                                    <a href="#">
                                                        <div class="flex items-center gap-3 p">
                                                            <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round"
                                                                    stroke-width="2"
                                                                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                                            </svg>
                                                            <div class="flex-1">
                                                                <h4
                                                                    class="font-semibold text-black dark:text-white text-sm">
                                                                    {{ $searchQuery->query }}
                                                                </h4>
                                                                <div class="mt-0.5">{{ $searchQuery->count }}
                                                                    post{{ $searchQuery->count > 1 ? 's' : '' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                    <div class="space-y-4" id="advanced-filters" role="tabpanel"
                                        aria-labelledby="advanced-filters-tab">
                                        <!-- search  -->

                                        <form wire:submit.prevent="search" class="max-w-2xl mx-auto">
                                            <label for="default-search"
                                                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                            <div class="relative">
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                        </svg>
                                                    </div>
                                                    <input type="search" id="default-search" wire:model="keyword"
                                                        class="block w-full p-4 ps-10 text-sm sm:text-[12px] text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-purple-600 focus:border-purple-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        placeholder="Recherche de produit ou service..." required />

                                                </div>

                                            </div>

                                            <div class="grid grid-cols-4 gap-3 mt-2">
                                                <div class="col-span-1">
                                                    <select wire:model="zone_economique" name="zoneEco"
                                                        type="text"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="Zone Economique">
                                                        <option disabled selected>Zone economique</option>
                                                        <option value="proximite">Proximité</option>
                                                        <option value="locale">Locale</option>
                                                        <option value="departementale">Departementale</option>
                                                        <option value="nationale">Nationale</option>
                                                        <option value="sous_regionale">Sous Régionale</option>
                                                        <option value="continentale">Continentale</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-1">
                                                    <select wire:model="type" name="type"
                                                        class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                                        <option selected>Type</option>
                                                        <option>Produit</option>
                                                        <option>Service</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-1">

                                                    <div class="mb-4">
                                                        <!-- Quantité  -->
                                                        <input wire:model="qte" name="qte" type="number"
                                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                            placeholder="Quantité ">
                                                    </div>
                                                </div>
                                                <div class="col-span-1">

                                                    <div class="mb-4">
                                                        <!-- prix unitaire -->
                                                        <input wire:model="prix" name="prix" type="number"
                                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                            placeholder="Prix unitaire">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div
                                                class="flex items-center space-x-4 rounded-b p-4 dark:border-gray-600 md:p-5">
                                                <button data-modal-hide="static-modal" type="submit"
                                                    class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-900"
                                                    data-modal-hide="filterModal">
                                                    Montrer les résultats
                                                </button>

                                                <button type="reset"
                                                    class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-900">
                                                    Réinitialiser
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                        </svg>
                        Sort
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="dropdownSort1"
                        class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                        data-popper-placement="bottom">
                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                            aria-labelledby="sortDropdownButton">
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    The most popular </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Newest </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Increasing price </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Decreasing price </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    No. reviews </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Discount % </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <main class="max-w-6xl mx-auto px-4 py-8">
                {{-- <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">Produits & Services</h1> --}}

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Projet 1 -->
                    @foreach ($produits as $produit)
                        <div
                            class="group bg-white rounded-lg shadow-md overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="relative overflow-hidden">
                                <img src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="{{ $produit->name }}"
                                    class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                                <div
                                    class="absolute inset-0  bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300">
                                </div>
                            </div>
                            <div class="p-6 transform transition-all duration-300">
                                <a href="{{ route('biicf.postdet', $produit->id) }}"
                                    class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600">
                                    {{ $produit->name }}
                                </a>
                                <p class="text-gray-600 mb-4">{{ number_format($produit->prix, 0, ',', ' ') }} XOF
                                </p>
                                <hr class="my-3">

                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block text-gray-400"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $produit->comnServ }}</span>
                                    <span class="text-gray-500">&bull;</span>
                                    <span>{{ $produit->villeServ }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @if ($produit->type == 'Produit')
                                        <span
                                            class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm transform transition hover:scale-105">Produit</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm transform transition hover:scale-105">Service</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </main>

        </div>

    </section>

</div>
