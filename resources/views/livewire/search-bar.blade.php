<div>
    <form wire:submit.prevent="search" class="max-w-2xl mx-auto">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="relative">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" id="default-search" wire:model.live="keyword"
                    class="block w-full p-4 ps-10 text-sm sm:text-[12px] text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-purple-600 focus:border-purple-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Recherche de produit ou service..." required />
                {{-- <button type="submit"
                    class="text-white absolute end-2.5 bottom-2.5 bg-purple-600 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button> --}}
            </div>
            @if (!empty($keyword) && $produits->isNotEmpty())
                <div
                    class="absolute z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    @foreach ($produits as $produit)
                        <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                            tabindex="{{ $loop->index }}">
                            <div class="flex">
                                <img class="w-8 h-8 mr-2 rounded-md"
                                    src="{{ $produit->photoProd1 ? asset($produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                                <div class="flex justify-between items-center w-full">
                                    <span>{{ $produit->name }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>


        <div class="grid grid-cols-3 gap-3 mt-2">
            <div class="col-span-1">
                <select wire:model="zone_economique" name="zone_economique"
                    class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
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
                <select wire:model="type" name="type"
                    class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                    <option selected disabled>Type</option>
                    <option>Produit</option>
                    <option>Service</option>
                </select>
            </div>
            <div class="col-span-1">

                <div class="mb-4">
                    <!-- Quantité Minimale -->
                    <input wire:model="qte_min" name="qte_min" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité minimale">

                    <!-- Quantité Maximale -->
                    <input wire:model="qte_max" name="qte_max" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 mt-2"
                        placeholder="Quantité maximale">
                </div>
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
