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


    <h2 class="text-2xl font-bold tracking-tight text-gray-900 hidden">localisation /Produit & Service / quantite / prix
    </h2>

    



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($produits as $produit)
            <a href="{{ route('biicf.postdet', $produit->id) }}" class="group relative transform hover:scale-105 transition-transform duration-300 ease-in-out">
                <div class="bg-white border border-gray-200 rounded-lg shadow-lg hover:shadow-xl dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    
                    <!-- Image Section -->
                    <div class="h-48 w-full relative">
                        <img class="w-full h-full object-cover transition-opacity duration-500 ease-in-out group-hover:opacity-90"
                            src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                            alt="Image Produit">
                        <!-- Badge for Product/Service -->
                        <div class="absolute top-2 right-2">
                            @if ($produit->type == 'Produit')
                                <span class="inline-block bg-green-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    Produit
                                </span>
                            @else
                                <span class="inline-block bg-yellow-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    Service
                                </span>
                            @endif
                        </div>
                    </div>
    
                    <!-- Content Section -->
                    <div class="p-4 space-y-3">
                        <!-- Title -->
                        <h5 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">{{ $produit->name }}</h5>
    
                        <!-- Price -->
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-indigo-600">{{ number_format($produit->prix, 0, ',', ' ') }} XOF</span>
                        </div>
    
                        <!-- Location and Service Info -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400 text-sm space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                            </svg>
                            <p class="truncate">{{ $produit->comnServ }} • {{ $produit->villeServ }}</p>
                        </div>
    
                        <!-- Time Ago -->
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
</div>
