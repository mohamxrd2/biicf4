<div id="search-input">
    <!-- Section de recherche -->
    <section class="mt-4 shadow-sm">
        <form wire:submit.prevent="search" class="">

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
            <!-- Section de filtre -->
            <div id="filter-section"
                class="hidden opacity-0 translate-y-[-20px] transition-all duration-300 ease-in-out grid grid-cols-4 gap-3 mt-2 p-4 bg-purple-400 rounded-lg ">
                <div class="col-span-1">
                    <select wire:model.live="zone_economique" name="zoneEco" type="text"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Zone Economique">
                        <option selected>Zone economique</option>
                        <option value="proximite">Proximité</option>
                        <option value="locale">Locale</option>
                        <option value="departementale">Departementale</option>
                        <option value="nationale">Nationale</option>
                        <option value="sous_regionale">Sous Régionale</option>
                        <option value="continentale">Continentale</option>
                    </select>
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
                    <input wire:model.live="qte" name="qte" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité ">
                </div>
                <div class="col-span-1">
                    <input wire:model.live="prix" name="prix" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Prix unitaire">
                </div>
            </div>

        </form>
    </section>

    <section class=" py-4 antialiased dark:bg-gray-900 md:py-12 rounded-lg">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">

            <main class="max-w-6xl mx-auto px-4 py-8">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Projet 1 -->
                    @foreach ($produits as $produit)
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <!-- Image produit -->
                            <div class="relative h-48">
                                <img src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="{{ $produit->name }}" class="w-full h-full object-cover" />
                                <div
                                    class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-sm ">
                                    4.5 ★ <!-- Exemple de notation -->
                                </div>
                            </div>

                            <!-- Contenu produit -->
                            <div class="p-4">
                                <!-- Titre et prix -->
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $produit->name }}</h3>
                                    <span
                                        class="text-lg font-bold text-yellow-500">{{ number_format($produit->prix, 0, ',', ' ') }}
                                        XOF</span>
                                </div>

                                <!-- Localisation -->
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 2C8.134 2 5 5.134 5 9c0 3.866 7 13 7 13s7-9.134 7-13c0-3.866-3.134-7-7-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                    <span>{{ $produit->user->commune }} • {{ $produit->user->ville }}</span>
                                </div>

                                <!-- Temps et bouton -->
                                <div class="flex justify-between items-center mt-4">
                                    <span
                                        class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}</span>
                                    <a href="{{ route('biicf.postdet', $produit->id) }}"
                                        class="bg-purple-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-500 transition-colors duration-200">
                                        Voir le produit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </main>
        </div>

    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("default-search");
            const filterSection = document.getElementById("filter-section");

            searchInput.addEventListener("focus", () => {
                filterSection.classList.remove("hidden", "opacity-0", "translate-y-[-20px]");
                filterSection.classList.add("opacity-100", "translate-y-0");
            });

            searchInput.addEventListener("blur", () => {
                setTimeout(() => {
                    if (!document.activeElement.closest("#filter-section")) {
                        filterSection.classList.add("opacity-0", "translate-y-[-20px]");
                        filterSection.classList.remove("opacity-100", "translate-y-0");
                        setTimeout(() => {
                            filterSection.classList.add("hidden");
                        }, 300); // Durée correspondant à `duration-300`
                    }
                }, 200);
            });
        });
    </script>

</div>
