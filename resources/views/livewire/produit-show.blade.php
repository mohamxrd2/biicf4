@if (session('success'))
    <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="bg-green-200 text-red-800 px-4 py-2 rounded-md mb-4">
        {{ session('error') }}
    </div>
@endif
<div>
    <div class="mb-3 w-full">
        <h1 class=" text-center font-bold text-2xl">DETAILS DE LA PUBLICATION</h1>
    </div>
    <div class="lg:flex 2xl:gap-16 gap-12 max-w-[1065px] mx-auto">
        <div class="grid grid-cols-3 gap-4">

            <div class="lg:col-span-2 col-span-3">

                <div class="flex-1 items-center justify-center">

                    <!-- Slider -->
                    <div data-hs-carousel='{
                                "loadingClasses": "opacity-0",
                                "isAutoPlay": true
                            }'
                        class="relative">
                        @php
                            $photoCount = 0;
                            if ($produits->photoProd1) {
                                $photoCount++;
                            }
                            if ($produits->photoProd2) {
                                $photoCount++;
                            }
                            if ($produits->photoProd3) {
                                $photoCount++;
                            }
                            if ($produits->photoProd4) {
                                $photoCount++;
                            }
                        @endphp

                        @if ($photoCount > 0)

                            <div class="hs-carousel relative overflow-hidden w-full min-h-screen bg-white rounded-lg">
                                <div
                                    class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                                    @foreach ([$produits->photoProd1, $produits->photoProd2, $produits->photoProd3, $produits->photoProd4] as $photo)
                                        @if ($photo)
                                            <div class="hs-carousel-slide">
                                                <div class="flex justify-center bg-gray-100  dark:bg-neutral-900">
                                                    <img class="w-full h-auto rounded-md  object-cover"
                                                        src="{{ asset($photo) }}" alt="Image">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="flex justify-center h-full bg-gray-100  dark:bg-neutral-900">
                                <img class="w-full h-full  rounded-md" src="{{ asset('img/noimg.jpeg') }}"
                                    alt="Image">
                            </div>
                        @endif
                        @if ($photoCount > 1)

                            <button type="button"
                                class="hs-carousel-prev hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 rounded-s-lg dark:text-white dark:hover:bg-white/10">
                                <span class="text-2xl" aria-hidden="true">
                                    <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m15 18-6-6 6-6"></path>
                                    </svg>
                                </span>
                                <span class="sr-only">retour</span>
                            </button>
                            <button type="button"
                                class="hs-carousel-next hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 rounded-e-lg dark:text-white dark:hover:bg-white/10">
                                <span class="sr-only">suivant</span>
                                <span class="text-2xl" aria-hidden="true">
                                    <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </span>
                            </button>
                            <div
                                class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 space-x-2">
                                @foreach ([$produits->photoProd1, $produits->photoProd2, $produits->photoProd3, $produits->photoProd4] as $photo)
                                    @if ($photo)
                                        <span
                                            class="hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 size-3 border border-gray-400 rounded-full cursor-pointer dark:border-neutral-600 dark:hs-carousel-active:bg-blue-500 dark:hs-carousel-active:border-blue-500"></span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                    </div>

                </div>

            </div>

            <div class="lg:col-span-1 col-span-3">

                <div class="mb-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">

                    @if ($produit->statuts == 'Accepté')
                        <div class="text-gray-800 bg-gray-200 rounded-md text-center p-1 mb-3">accepté !</div>
                    @else
                        <button wire:click="accepter" class="w-full mb-3">
                            <div class="text-teal-800 bg-teal-100 rounded-md text-center p-1 mb-3">accepter</div>
                        </button>
                    @endif

                    @if ($produit->statuts == 'Refusé')
                        <div class="text-gray-800 bg-gray-200 rounded-md text-center p-1">refusé !</div>
                    @else
                        <button wire:click="refuser" class="w-full ">
                            <div class="text-teal-800 bg-red-100 rounded-md text-center p-1">refuser</div>
                        </button>
                    @endif

                </div>

                <div class="mb-4 mx-auto">


                    <div class=" card border shadow-sm rounded-xl flex space-x-5 p-5 mb-4">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Date de création</h4>

                            <p>{{ \Carbon\Carbon::parse($produits->created_at)->diffForHumans() }}</p>

                        </div>
                    </div>
                    <div class="mb-4 grid sm:grid-cols-2 gap-3">

                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Type </h4>
                                <p>{{ $produits->type }}</p>
                            </div>
                        </div>
                        @if ($produits->condProd)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> conditionnement </h4>
                                    <p>{{ $produits->condProd }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($produits->formatProd)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> format </h4>
                                    <p>{{ $produits->formatProd }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($produits->qteProd_min || $produits->qteProd_max)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> Quantité traité</h4>
                                    <p>[ {{ $produits->qteProd_min }} - {{ $produits->qteProd_max }} ]</p>
                                </div>
                            </div>
                        @endif


                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Prix par unité </h4>
                                <p>{{ $produits->prix }}</p>
                            </div>
                        </div>

                        @if ($produits->LivreCapProd)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold">Capacité de livré</h4>
                                    <p>{{ $produits->LivreCapProd }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($produits->qalifServ)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> Experiance </h4>
                                    <p>{{ $produits->qalifServ }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($produits->sepServ)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> Specialité </h4>
                                    <p>{{ $produits->sepServ }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($produits->qteServ)
                            <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                                <div class="card-body flex-1 p-0">
                                    <h4 class="card-title font-bold"> Nombre du personnel </h4>
                                    <p>{{ $produits->qteServ }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Zone economique </h4>
                                <p>{{ $produits->zonecoServ }}</p>
                            </div>
                        </div>
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Ville, Commune</h4>
                                <p> {{ $produits->villeServ }}, {{ $produits->comnServ }}</p>
                            </div>
                        </div>
                    </div>
                    <div class=" card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Description</h4>

                            <p>{{ $produits->desrip }}</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
