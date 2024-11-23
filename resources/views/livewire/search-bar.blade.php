<div id="search-input">

    <!-- Section de recherche -->
    <section class="mt-4 shadow-sm">
        <form wire:submit.prevent="search" class="">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                    <select wire:model="zone_economique" name="zoneEco" type="text"
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
        </form>
    </section>



    <section class=" py-8 antialiased dark:bg-gray-900 md:py-12 rounded-lg">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">

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
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <!-- Image produit -->
                            <div class="relative h-48">
                                <img src="https://via.placeholder.com/300" alt="Titre du produit" class="w-full h-full object-cover" />
                                <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-sm">
                                    4.5 ★ <!-- Exemple de notation -->
                                </div>
                            </div>

                            <!-- Contenu produit -->
                            <div class="p-4">
                                <!-- Titre et prix -->
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Titre du produit</h3>
                                    <span class="text-lg font-bold text-yellow-500">99,99 €</span>
                                </div>

                                <!-- Localisation -->
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 2C8.134 2 5 5.134 5 9c0 3.866 7 13 7 13s7-9.134 7-13c0-3.866-3.134-7-7-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                    <span>Paris • France</span>
                                </div>

                                <!-- Temps et bouton -->
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-sm text-gray-500">Il y a 2 heures</span>
                                    <button
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-200">
                                        Voir le produit
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </main>

        </div>

    </section>

</div>
