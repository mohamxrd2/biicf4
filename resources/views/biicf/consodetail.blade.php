@extends('biicf.layout.navside')

@section('title', 'Details')

@section('content')

    <div class="mb-3">
        <h1 class=" text-center text-slate-600 font-bold text-xl">DETAILS DE LA CONSOMMATION</h1>
    </div>


    <div class="grid grid-cols-3 gap-4">
        <div class="lg:col-span-2 col-span-3">
            <div class="max-w-2xl mx-auto">
                <h1 class="my-3 text-2xl font-semibold">{{ $consommation->name }}</h1>

                <div class="flex flex-col">
                    <p class="text-xl mt-6 text-slate-900 font-bold">Description</p>
                    <div class="mt-3 card border shadow-sm rounded-xl flex space-x-5 p-5">
                        <div class="card-body flex-1 p-0">
                            <p>{{ $consommation->desrip }}</p>
                        </div>
                    </div>
                    @if ($consommation->condProd)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> conditionnement </h4>
                                <p>{{ $consommation->condProd }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($consommation->formatProd)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> format </h4>
                                <p>{{ $consommation->formatProd }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($consommation->qteProd_min || $consommation->qteProd_max)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Quantité traité</h4>
                                <p>[ {{ $consommation->qteProd_min }} - {{ $consommation->qteProd_max }} ]</p>
                            </div>
                        </div>
                    @endif


                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Prix par unité </h4>
                            <p>{{ $consommation->prix }}</p>
                        </div>
                    </div>

                    @if ($consommation->LivreCapProd)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold">Capacité de livré</h4>
                                <p>{{ $consommation->LivreCapProd }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($consommation->qalifServ)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Experiance </h4>
                                <p>{{ $consommation->qalifServ }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($consommation->sepServ)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Specialité </h4>
                                <p>{{ $consommation->sepServ }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($consommation->qteServ)
                        <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                            <div class="card-body flex-1 p-0">
                                <h4 class="card-title font-bold"> Nombre du personnel </h4>
                                <p>{{ $consommation->qteServ }}</p>
                            </div>
                        </div>
                    @endif


                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Ville, Commune</h4>
                            <p> {{ $consommation->villeServ }}, {{ $consommation->comnServ }}</p>
                        </div>
                    </div>
                    <div class="card border shadow-sm rounded-xl flex space-x-5 p-5 mt-3 mb-6">
                        <div class="card-body flex-1 p-0">
                            <h4 class="card-title font-bold"> Zone economique </h4>
                            <p>{{ $consommation->zonecoServ }}</p>
                        </div>
                    </div>
                    <div
                        class="flex flex-col w-full bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
                        <div class="p-4 md:p-10">

                            <p class="mt-2 text-gray-500 dark:text-neutral-400">
                            <h4 class="card-title font-bold"> Date de création </h4>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($consommation->created_at)->diffForHumans() }}</p>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    </div>



@endsection
