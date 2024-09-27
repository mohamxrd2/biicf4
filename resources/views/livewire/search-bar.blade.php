<div id="search-input">
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

            </div>
            {{-- @if (!empty($keyword) && $produits->isNotEmpty())
                <div
                    class="absolute z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    @foreach ($produits as $produit)
                        <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                            tabindex="{{ $loop->index }}">
                            <div class="flex">
                                <img class="w-8 h-8 mr-2 rounded-md"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                                <div class="flex justify-between items-center w-full">
                                    <span>{{ $produit->name }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif --}}
        </div>


        <div class="grid grid-cols-4 gap-3 mt-2">
            <div class="col-span-1">
                <input wire:model.live="zone_economique" name="zoneEco" type="text"
                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                    placeholder="Zone Economique">
            </div>
            <div class="col-span-1">
                <select wire:model.live="type" name="type"
                    class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                    <option selected>Type</option>
                    <option>Produit</option>
                    <option>Service</option>
                </select>
            </div>
            <div class="col-span-1">

                <div class="mb-4">
                    <!-- Quantité  -->
                    <input wire:model.live="qte" name="qte" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité ">
                </div>
            </div>
            <div class="col-span-1">

                <div class="mb-4">
                    <!-- prix unitaire -->
                    <input wire:model.live="prix" name="prix" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Prix unitaire">
                </div>
            </div>
        </div>
    </form>


    <h2 class="text-2xl font-bold tracking-tight text-gray-900">localisation /Produit & Service / quantite / prix</h2>

    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>

        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-48 md:w-56 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
            </div>
            <div class="p-2 flex-grow">
                <h5 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy</h5>
                <h1 class="text-base sm:text-base font-bold tracking-tight text-gray-900 dark:text-white">X0F 11111</h1>
                <p class="font-normal text-gray-700 dark:text-gray-400">pays...</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">ville...</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">Produit</span>
                <div class="w-full bottom-0">
                    <p class="text-sm text-gray-600 text-right">2min</p>
                </div>
            </div>
        </div>

        <!-- Ajoute les autres divs de produit ici de la même manière -->
    </div>





    @foreach ($produits as $produit)
        <div class="max-w-2xl mx-auto my-3">

            <div class="w-full flex p-4 rounded-xl bg-white border border-gray-200">
                <div class="h-32 w-32 mr-2 ">
                    <img class="w-full h-full rounded-xl  object-cover"
                        src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
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

                            @if ($produit->type == 'Produit')
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
