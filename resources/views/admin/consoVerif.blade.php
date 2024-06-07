@extends('admin.layout.navside')

@section('title', 'affichage de consommations')

@section('content')
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
    <div class="mb-3">
        <h1 class=" text-center font-bold text-2xl">DETAILS DE LA CONSOMMATION</h1>
    </div>

    <div class="lg:flex 2xl:gap-16 gap-12 max-w-[1065px] mx-auto">

        <div class="mb-4 flex-1 mx-auto">

            <div class="md:max-w-[650px] mx-auto flex-1 xl:space-y-6 space-y-3">

                <div class="flex items-center py-3 dark:border-gray-600 my-3">

                    <!--  TITRE DU PRODUIT  -->
                    <div
                        class="flex flex-col w-full bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
                        <div class="p-4 md:p-10">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $consommations->name }}
                            </h3>
                            <p class="mt-2 text-gray-500 dark:text-neutral-400">
                            <h4 class="card-title font-bold"> Date de création </h4>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($consommations->created_at)->diffForHumans() }}
                            </p>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="mb-4 grid sm:grid-cols-2 gap-3">

                <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                    <div class="card-body flex-1 p-0">
                        <h4 class="card-title font-bold"> Type </h4>
                        <p>{{ $consommations->type }}</p>
                    </div>
                </div>


                @if ($consommations->format)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Format </h4>
                            <p>{{ $consommations->format }}</p>
                        </div>
                    </div>
                @endif

                @if ($consommations->conditionnement)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold">Conditionnement</h4>
                            <p>{{ $consommations->conditionnement }}</p>
                        </div>
                    </div>
                @endif
                @if ($consommations->qte)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Quantité </h4>
                            <p>{{ $consommations->qte }}</p>
                        </div>
                    </div>
                @endif

                @if ($consommations->prix)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Prix </h4>
                            <p>{{ $consommations->prix }}</p>
                        </div>
                    </div>
                @endif

                @if ($consommations->qteProd_min)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Frequence de consommation</h4>
                            <p>{{ $consommations->qteProd_min }} </p>
                        </div>
                    </div>
                @endif

                @if ($consommations->jourAch_cons)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Jour d'achat</h4>
                            <p> {{ $consommations->jourAch_cons }}</p>
                        </div>
                    </div>
                @endif

                @if ($consommations->qualif_serv)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title bold"> Qualification </h4>
                            <p> {{ $consommations->qualif_serv }}</p>
                        </div>
                    </div>
                @endif
                @if ($consommations->specialité)
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Specialité </h4>
                            <p> {{ $consommations->specialité }}</p>
                        </div>
                    </div>
                @endif


                <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                    <div class="card-body flex-1 p-0">
                        <h4 class="card-title font-bold"> Zone d'activité </h4>
                        <p>{{ $consommations->zonecoServ }}</p>
                    </div>
                </div>
                <div class="card border shadow-sm rounded-xl flex space-x-5 p-5">
                    <div class="card-body flex-1 p-0">
                        <h4 class="card-title font-bold"> Ville</h4>
                        <p> {{ $consommations->villeCons }}</p>
                    </div>
                </div>
            </div>

        </div>


        <div class="flex-1 items-center justify-center">
            <!-- Boutons -->
            <div class="flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">

                <form method="POST" action="{{ route('consommation.consoEtat', $consommations->id) }}"
                    class="flex items-center space-x-5">
                    @csrf <!-- Ajoutez le jeton CSRF pour protéger votre formulaire -->
                    @method('POST')
    
                    <!-- Champ pour l'action accepter -->
                    <input type="hidden" name="action" value="accepter">
    
                    <!-- Bouton accepter -->
                    @if ($consommations->statuts == 'Accepté')
                        <div class="w-full mb-3">
                            <div class="text-gray-800 bg-gray-200 rounded-md text-center p-1">accepté !</div>
                        </div>
                    @else
                        <button type="submit" class="w-full mb-3">
                            <div class="text-teal-800 bg-teal-100 rounded-md text-center p-1">accepter</div>
                        </button>
                    @endif
                </form>
    
                <form method="POST" action="{{ route('consommation.consoEtat', $consommations->id) }}"
                    class="flex items-center space-x-5 ">
                    @csrf <!-- Ajoutez le jeton CSRF pour protéger votre formulaire -->
                    @method('POST')
    
                    <!-- Champ pour l'action refuser -->
                    <input type="hidden" name="action" value="refuser">
    
                    <!-- Bouton refuser -->
                    @if ($consommations->statuts == 'Refusé')
                        <div class="w-full">
                            <div class="text-gray-800 bg-gray-200 rounded-md text-center p-1">refusé !</div>
                        </div>
                    @else
                        <button type="submit" class="w-full">
                            <div class="text-red-800 bg-red-100 rounded-md text-center p-1">Refuser</div>
                        </button>
                    @endif
                </form>

            </div>
        </div>

    </div>






@endsection
