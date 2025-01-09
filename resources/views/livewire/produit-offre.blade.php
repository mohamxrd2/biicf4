<div>
    @if ($currentPage === 'produit')
        <!-- Carousel wrapper -->
        <section id="checkoutSection" class="bg-white md:py-16 dark:bg-gray-900 antialiased rounded-lg shadow-lg">

            <ol
                class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base  p-5">
                <li
                    class="after:border-1 flex items-center text-primary-700  after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center text-blue-600 after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Details
                    </span>
                </li>

                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Achat
                    </span>
                </li>


            </ol>

            <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 ">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                    <!-- Images Section -->
                    <div class="flex flex-col items-center space-y-4">
                        <!-- Main Image -->
                        <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                            <img id="mainImage" class="w-full dark:hidden rounded-lg shadow-md"
                                src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Main Product Image" />
                        </div>

                        <!-- Thumbnail Images -->
                        <div class="flex justify-center space-x-4">
                            <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd1) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-300 rounded-lg hover:shadow-lg transition-transform transform hover:scale-105"
                                src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Thumbnail 1">
                            <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd2) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-300 rounded-lg hover:shadow-lg transition-transform transform hover:scale-105"
                                src="{{ asset('post/all/' . $produit->photoProd2) }}" alt="Thumbnail 2">
                            <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd3) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-300 rounded-lg hover:shadow-lg transition-transform transform hover:scale-105"
                                src="{{ asset('post/all/' . $produit->photoProd3) }}" alt="Thumbnail 3">
                            <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd4) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-300 rounded-lg hover:shadow-lg transition-transform transform hover:scale-105"
                                src="{{ asset('post/all/' . $produit->photoProd4) }}" alt="Thumbnail 4">
                        </div>
                    </div>


                    <!-- Product Info Section -->
                    <div class="mt-6   sm:mt-8 lg:mt-0 sm:p-6">
                        <p class="text-xl font-extrabold text-gray-900 sm:text-3xl dark:text-white mr-4">
                            {{ $produit->name }} " {{ $produit->specification }}
                        </p>

                        <!-- Price and Rating -->
                        <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                            <p class="text-xl font-bold text-gray-900 sm:text-3xl dark:text-white mr-4"
                                data-price="{{ $produit->prix }}">
                                {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                            </p>
                            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                <div class="flex items-center gap-1">
                                    <!-- SVGs for rating stars (repeated for all stars) -->
                                    <svg class="w-4 h-4 text-yellow-300" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d=" M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397
                                                                                                                                                8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067
                                                                                                                                                2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39
                                                                                                                                                3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                    </svg>
                                    <!-- Repeat for additional stars -->
                                </div>
                                <p class="text-sm font-medium leading-none text-gray-500 dark:text-gray-400  mr-4">(5.0)
                                </p>
                                <a href="#"
                                    class="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white">
                                    345 Reviews
                                </a>
                            </div>
                        </div>


                        <x-offre.menu-dropdown :produit="$produit" />


                        <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                        <button class="w-full bg-red-500 text-white py-2 mb-3 rounded-xl">Supprimé produit</button>

                        <div class="w-full p-3 bg-gray-200 rounded-2xl flex justify-between items-center cursor-pointer "
                            onclick="toggleVisibility()">
                            <p class="font-medium text-sm text-gray-700">Caracteristique</p>
                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>


                        <!-- Product Description -->
                        <div id="toggleContent" class="w-full p-3 gap-y-2 hidden mb-4">
                            @if ($produit->continent)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Localisation</p>
                                    <p class="text-sm font-medium text-gray-600"> {{ $produit->continent }},
                                        {{ $produit->sous_region }},
                                        {{ $produit->pays }}, {{ $produit->zoneecoServ }}, {{ $produit->villeServ }},
                                        {{ $produit->comnServ }}</p>
                                </div>
                            @endif
                            @if ($produit->reference)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Reference</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->reference }}</p>
                                </div>
                            @endif
                            @if ($produit->condProd)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Conditionnement</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->condProd }}</p>
                                </div>
                            @endif
                            @if ($produit->formatProd)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Format</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->formatProd }}</p>
                                </div>
                            @endif
                            @if ($produit->qteProd_min || $produit->qteProd_max)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Quantité traité</p>
                                    <p class="text-sm font-medium text-gray-600">[{{ $produit->qteProd_min }} -
                                        {{ $produit->qteProd_max }}]</p>
                                </div>
                            @endif
                            @if ($produit->specification)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Capacité de livré</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->specification }}</p>
                                </div>
                            @endif
                            @if ($produit->origine)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Capacité de livré</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->origine }}</p>
                                </div>
                            @endif
                            @if ($produit->Particularite)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Particularite</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->Particularite }}</p>
                                </div>
                            @endif
                            @if ($produit->qalifServ)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Année d'experiance</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->qalifServ }}</p>
                                </div>
                            @endif
                            @if ($produit->sepServ)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Specialité</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->sepServ }}</p>
                                </div>
                            @endif
                            @if ($produit->qteServ)
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Nombre de personnel</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->qteServ }}</p>
                                </div>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </section>
    @elseif($currentPage === 'simple')
        <x-dynamic-component component="offre-simple-modal" :produit="$produit" :nombreProprietaires="$nombreProprietaires" />
    @elseif($currentPage === 'negocie')
        <x-dynamic-component component="offre-negociee-modal" :produit="$produit" :nombreProprietaires="$nombreProprietaires"/>
    @elseif($currentPage === 'groupe')
        <x-dynamic-component component="offre-groupee-modal" :produit="$produit" :nombreFournisseurs="$nomFournisseurCount" :users="$users" />
    @endif

</div>
