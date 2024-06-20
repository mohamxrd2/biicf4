@extends('biicf.layout.navside')

@section('title', 'Details notification')

@section('content')


    <div class=" mx-auto">

        @if (session('success'))
            <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('error') }}
            </div>
        @endif

        <!-- Centrer le titre en utilisant des classes de flexbox -->

        <div class="flex justify-center items-center h-16">
            <h1 class="text-3xl font-semibold">AJOUT DE QUANTITE </h1>

        </div>
        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="lg:col-span-1 col-span-2">


                <div class="w-full gap-y-2  mt-4">

                     <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Prix unitaire maximal</p>
                        <p class="text-md font-medium text-gray-600">
                            {{$appelOffreGroup->lowestPricedProduct }}
                        </p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Quantité total du groupage</p>
                        <p class="text-md font-medium text-gray-600">{{$sumquantite}}</p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Payement</p>
                        <p class="text-md font-medium text-gray-600">{{$appelOffreGroup->payment }}</p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Livraison</p>
                        <p class="text-md font-medium text-gray-600">{{$appelOffreGroup->Livraison }}</p>
                    </div>


                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Specificité</p>
                        <p class="text-md font-medium text-gray-600">{{$appelOffreGroup->specificity }}</p>
                    </div>



                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Date au plus tôt</p>
                        <p class="text-md font-medium text-gray-600">{{$appelOffreGroup->dateTot }}</p>
                    </div>

                    <div class="w-full flex justify-between items-center py-4  border-b-2">
                        <p class="text-md font-semibold">Date au plus tard</p>
                        <p class="text-md font-medium text-gray-600">{{$appelOffreGroup->dateTard }}</p>
                    </div>


                </div>



            </div>
            <div class="lg:col-span-1 col-span-2">

                <div class="p-4">

                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">



                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">

                            <form action="{{ route('biicf.storeoffre') }}" method="post" id="commentForm">
                                @csrf
                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="codeUnique" value="{{ $appelOffreGroup->codeunique }}">
                                    <input type="hidden" name="userId" value="{{ $userId }}">
                                    <input type="number" name="quantite" id="quantite"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>

                                    <button type="submit" id="submitBtn"
                                        class="inline-flex justify-center p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="w-full flex justify-center">
                                    <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                                </div>
                            </form>


                        </div>

                    </div>

                    <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">


                        @if ($datePlusAncienne)
                            <span class="mb-2">Temps restant pour le groupage</span>
                            <div id="countdown"
                                class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100 p-3 rounded-xl w-auto">
                                <div>-</div>:
                                <div>-</div>:
                                <div>-</div>
                            </div>
                        @endif


                    </div>



                </div>

            </div>

        </div>

        <script>
            // Convertir la date de départ en objet Date JavaScript
            const startDate = new Date("{{ $datePlusAncienne }}");

            // Ajout d'une heure à la date de départ
            startDate.setHours(startDate.getHours() + 1);


            // Mettre à jour le compte à rebours à intervalles réguliers
            const countdownTimer = setInterval(updateCountdown, 1000);

            function updateCountdown() {
                // Obtenir la date et l'heure actuelles
                const currentDate = new Date();

                // Calculer la différence entre la date cible et la date de départ en millisecondes
                const difference = startDate.getTime() - currentDate.getTime();

                // Convertir la différence en jours, heures, minutes et secondes
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
                const countdownElement = document.getElementById('countdown');
                countdownElement.innerHTML = `
               <div>${hours}h</div>:
               <div>${minutes}m</div>:
               <div>${seconds}s</div>
                `;

                // Arrêter le compte à rebours lorsque la date cible est atteinte
                if (difference <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.innerHTML = "Temps écoulé !";
                }
            }
        </script>




    </div>




@endsection
