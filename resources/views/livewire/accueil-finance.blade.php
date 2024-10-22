<div>
    {{-- Be like water. --}}

    @if ($projetCount > 0)

        <div class="bg-gray-50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    Tout les projets <span class="text-purple-600">({{ $projetCount }})</span>
                </h2>
            </div>

            @if ($projetRecent)
                <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
                    <!-- Image du projet -->
                    <img class="w-full md:w-1/2  md:h-96 object-cover rounded-md" src="{{ asset($projetRecent->photo1) }}"
                        alt="Image du projet" />

                    <!-- Contenu du projet -->
                    <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
                        <!-- Catégorie du projet -->
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                            </svg>
                            <span class="ml-2 text-sm capitalize text-gray-500">{{ $projetRecent->categorie }}</span>
                        </div>


                        @if ($projetRecent->type_financement == 'groupé')
                            <a href="{{ route('detailprojetGroupe', $projetRecent->id) }}">
                                <h3 class="text-xl font-semibold text-gray-800 mt-2">
                                    {{ $projetRecent->name }}
                                </h3>
                            </a>
                        @elseif ($projetRecent->type_financement == 'négocié')
                            <a href="{{ route('detailprojetNegocie', $projetRecent->id) }}">
                                <h3 class="text-xl font-semibold text-gray-800 mt-2">
                                    {{ $projetRecent->name }}
                                </h3>
                            </a>
                        @endif

                        <!-- Description courte -->
                        <p class="text-gray-500 mt-2 text-sm">
                            {{ \Illuminate\Support\Str::words($projetRecent->description, 50, '...') }}
                        </p>

                        <!-- Informations de progression -->
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-green-500 h-2 rounded-full"
                                    style="width: {{ $projetRecent->pourcentageInvesti }}%"></div>
                            </div>

                            <div
                                class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
                                <!-- Montant Reçu -->
                                <div class="flex flex-col text-center">
                                    <span class="font-semibold text-lg">{{ $projetRecent->sommeInvestie }} FCFA</span>
                                    <span class="text-gray-500 text-sm">Reçu de
                                        {{ number_format($projetRecent->montant, 0, ',', ' ') }} FCFA </span>
                                </div>

                                <!-- Nombre d'Investisseurs -->
                                <div class="flex flex-col text-center">
                                    <span
                                        class="font-semibold text-lg">{{ $projetRecent->nombreInvestisseursDistinct }}</span>
                                    <span class="text-gray-500 text-sm">Investisseurs</span>
                                </div>

                                <!-- Jours Restants -->
                                <div class="flex flex-col text-center">
                                    <span class="font-semibold text-lg">{{ $this->joursRestants() }}</span>
                                    <span class="text-gray-500 text-sm">Jours restants</span>
                                </div>

                                <!-- Progression -->
                                <div class="flex flex-col text-center">
                                    <span
                                        class="font-semibold text-lg">{{ number_format($projetRecent->pourcentageInvesti, 2) }}%</span>
                                    <span class="text-gray-500 text-sm">Progression</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projets as $projet)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <img class="w-full h-48 object-cover" src="{{ asset($projet->photo1) }}" alt="Projet image" />
                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-500">{{ $projet->categorie }}</span>
                                <!-- Catégorie -->
                            </div>
                            @if ($projet->type_financement == 'groupé')
                                <a href="{{ route('detailprojetGroupe', $projet->id) }}">
                                    <h3 class="text-lg font-semibold text-gray-800 mt-1">{{ $projet->name }}</h3>
                                    <!-- Nom du projet -->
                                </a>
                            @elseif ($projet->type_financement == 'négocié')
                                <a href="{{ route('detailprojetNegocie', $projet->id) }}">
                                    <h3 class="text-lg font-semibold text-gray-800 mt-1">{{ $projet->name }}</h3>
                                    <!-- Nom du projet -->
                                </a>
                            @endif

                            <p class="text-gray-600 mt-2">
                                {{ \Illuminate\Support\Str::words($projet->description, 15, '...') }}
                                <!-- Affiche les 100 premiers mots de la description -->
                            </p>

                            <div class="bg-gray-200 h-2 mt-2 rounded-full">
                                <div class="bg-green-500 h-2 rounded-full"
                                    style="width: {{ $projet->pourcentageInvesti }}%"></div> <!-- Taux d'avancement -->
                            </div>
                            <div class="flex justify-between items-center mt-4">
                                <div class="flex flex-col">
                                    <span
                                        class="font-semibold text-lg">{{ number_format($projet->sommeInvestie, 0, ',', ' ') }}
                                        FCFA</span> <!-- Montant -->
                                    <span class="text-gray-500 text-sm">Reçu de
                                        {{ number_format($projet->montant, 0, ',', ' ') }} FCFA </span>
                                    <!-- Montant reçu -->
                                </div>

                                <div class="flex flex-col">
                                    <span
                                        class="font-semibold text-lg text-right">{{ $projet->nombreInvestisseursDistinct }}</span>
                                    <!-- Nombre d'investisseurs (exemple statique) -->
                                    <span class="text-gray-500 text-sm">Investisseurs</span>
                                </div>
                            </div>
                            <div class="flex py-2 mt-2 items-center">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                    <!-- Placeholder pour l'image de profil -->
                                    <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                                        src="{{ asset($projet->demandeur->photo) }}" alt="">
                                </div>
                                <div class="ml-2 text-sm font-semibold">
                                    <span
                                        class="font-medium text-gray-500 mr-2">De</span>{{ $projet->demandeur->name }}
                                    <!-- Nom du demandeur -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Pagination -->

            <div class="flex justify-center mt-4">
                <button wire:click="chargerPlus" class="bg-purple-600 text-white px-4 py-2 rounded-md"
                    wire:loading.attr="disabled" wire:loading.class="bg-gray-400">
                    <span wire:loading.remove>Voir plus</span>
                    <span wire:loading>Chargement...</span>
                </button>
            </div>
        </div>
    @else
        <div class="text-center text-gray-500">
            Aucun projet

        </div>

    @endif


</div>
