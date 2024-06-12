@extends('biicf.layout.navside')

@section('title', 'Acceuil')

@section('content')



    <div class="grid grid-cols-3 gap-4">

        <div class="lg:col-span-2 col-span-3">

            <form action="{{ route('biicf.search') }}" method="GET" class="max-w-2xl mx-auto">

                <label for="default-search"
                    class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative " data-hs-combo-box="">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" id="default-search"
                            class="block w-full p-4 ps-10 text-sm sm:text-[12px]  text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-purple-600 focus:border-purple-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Recherche de produit ou service..." required data-hs-combo-box-input=""
                            name="keyword" />
                        <button type="submit"
                            class="text-white absolute end-2.5 bottom-2.5 bg-purple-600 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>

                    </div>
                    <div class="absolute z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                        style="display: none;" data-hs-combo-box-output="">

                        <!-- Utiliser la boucle foreach pour générer les éléments de la liste déroulante -->
                        @foreach ($produits->take(5) as $produit)
                            <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                tabindex="{{ $loop->index }}" data-hs-combo-box-output-item="{{ $produit->id }}">
                                <div class="flex">
                                    <img class="w-8 h-8 mr-2 rounded-md"
                                        src="{{ $produit->photoProd1 ? asset($produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                        alt="">
                                    <div class="flex justify-between items-center w-full">
                                        <span data-hs-combo-box-search-text="{{ $produit->name }} "
                                            data-hs-combo-box-value="{{ $produit->id }}">{{ $produit->name }}</span>
                                        <span class="hidden hs-combo-box-selected:block">
                                            <svg class="flex-shrink-0 size-3.5 text-blue-600"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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

                <div class="grid grid-cols-3 gap-3 mt-2">
                    <div class="col-span-1">
                        <select name="zone_economique"
                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm   disabled:opacity-50 disabled:pointer-events-none">
                            <option disabled selected>Zone economique</option>
                            <option value="Proximité">Proximité</option>
                            <option value="Locale">Locale</option>
                            <option value="Nationale">Nationale</option>
                            <option value="Sous Régionale">Sous Régionale</option>
                            <option value="Continentale">Continentale</option>
                            <option value="Internationale">Internationale</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <select name="type"
                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                            <option selected disabled>Type</optionselected>
                            <option>Produit</option>
                            <option>Service</option>

                        </select>
                    </div>
                    <div class="col-span-1">
                        <input name="qte_search" type="text"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Quantité souhaité">
                    </div>

                </div>

            </form>


            @foreach ($produits as $produit)
                <div class="max-w-2xl mx-auto my-3">

                    <div class="w-full flex p-4 rounded-xl bg-white border border-gray-200">
                        <div class="h-32 w-32 mr-2 ">
                            <img class="w-full h-full rounded-xl  object-cover"
                                src="{{ $produit->photoProd1 ? asset($produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                alt="">
                        </div>

                        <div class="flex flex-col w-full">
                            <div class="flex flex-col w-full justify-between h-full">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('biicf.postdet', $produit->id) }}" class="flex items-center">
                                            <p class="text-xl font-semibold">{{ $produit->name }}</p>
                                        </a>

                                        <div class="flex items-center text-[12px] text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="w-4 h-4 inline-block align-middle">
                                                <path fill-rule="evenodd"
                                                    d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-[12px]  inline-block align-middle">
                                                {{ $produit->villeServ }},{{ $produit->comnServ }}
                                            </p>

                                        </div>
                                    </div>

                                    @if ($produit->type == 'produits')
                                        <span
                                            class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none  text-green-800 bg-green-100">Produit</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none  text-yellow-800 bg-yellow-100">Service</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-700">
                                    {{ strlen($produit->desrip) > 50 ? substr($produit->desrip, 0, 50) . '...' : $produit->desrip }}
                                </p>
                                <div class="w-full bottom-0">
                                    <p class="text-sm text-gray-600 text-right ">
                                        {{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            @endforeach
        </div>
        <div class="lg:col-span-1 lg:block hidden">
            <div class="flex flex-col ">
                <div class="flex bg-white border border-gray-200 p-4 rounded-xl mb-3">
                    <img class="h-12 w-12 border-2 border-white rounded-full dark:border-gray-800 object-cover"
                        src="{{ asset($user->photo) }}" alt="">

                    <div class="flex flex-col ml-3">
                        <p class="font-semibold"> {{ $user->name }}</p>
                        <p class="text-[12px] text-gray-500 "> {{ $user->username }}</p>
                    </div>
                </div>
                <div class="flex flex-col bg-white border border-gray-200 p-4 rounded-xl">
                    <p class="font-semibold">Thèmes les plus rechercher</p>

                    <div class="space-y-3.5 capitalize text-xs font-normal mt-5 mb-2 text-gray-600 dark:text-white/80">
                        <a href="#">
                            <div class="flex items-center gap-3 p">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> artificial intelligence
                                    </h4>
                                    <div class="mt-0.5"> 1,245,62 post </div>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> Web developers</h4>
                                    <div class="mt-0.5"> 1,624 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> Ui Designers</h4>
                                    <div class="mt-0.5"> 820 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> affiliate marketing </h4>
                                    <div class="mt-0.5"> 480 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> affiliate marketing </h4>
                                    <div class="mt-0.5"> 480 post </div>
                                </div>
                            </div>
                        </a>


                    </div>

                </div>
                <footer class="text-center text-sm text-gray-600 pt-8 pb-11 ">
                    &copy; 2024 BIICF. Tous droits réservés.
                </footer>
            </div>

        </div>
    </div>

@endsection
