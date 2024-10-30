<div>
    @if (isset($projet->montant) && !isset($projet->Portion_action) && !isset($projet->Portion_obligt))
        <h1>projet groupe avec obligation</h1>

        <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
            <!-- Images Section -->
            <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>

                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Contenu du projet -->
            <div class="md:px-4 flex flex-col w-full md:w-1/2 ">
                @if ($pourcentageInvesti < 100)
                    <!-- Le projet n'est pas encore entièrement financé -->
                    @include('finance.components.Obligation')
                @else
                    <!-- Si le projet est entièrement financé -->
                    @if ($investisseurQuiAPayeTout)
                        <!-- Vérifier si un investisseur a payé tout le montant -->
                        @if ($projet->id_user == Auth::id())
                            <!-- Le propriétaire voit Obligation -->
                            @include('finance.components.Obligation')
                        @else
                            <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                            @php
                                $tauxPresent = $projet->taux; // Récupérer le taux présent pour la comparaison

                                // Trouver le plus petit taux dans la liste des commentaires
                                $minTaux = $commentTauxList->min('taux');

                                // Trouver le commentaire le plus ancien avec le taux minimal
                                $oldestMinTauxComment = $commentTauxList
                                    ->where('taux', $minTaux)
                                    ->sortBy('created_at')
                                    ->first();
                            @endphp

                            @if ($montantVerifie)

                                <div x-data="countdownTimer({{ json_encode($projet->durer) }})" class="flex flex-col">
                                    <div
                                        class="border flex items-center justify-between border-gray-300 rounded-lg p-1 shadow-md">
                                        <div x-show="projetDurer" class="text-xl font-medium">Temps restant</div>
                                        <div id="countdown" x-show="projetDurer"
                                            class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                                            @if (!$projet->count)
                                                <div x-text="jours">--</div>j
                                                <span>:</span>
                                                <div x-text="hours">--</div>h
                                                <span>:</span>
                                                <div x-text="minutes">--</div> m
                                                <span>:</span>
                                                <div x-text="seconds">--</div>s
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Afficher les messages d'erreur -->
                                    @if (session()->has('error'))
                                        <div class="bg-red-500 text-white p-2 mt-2 rounded-md">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                                        <div
                                            class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
                                            <div
                                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">



                                                @if ($commentTauxList->isNotEmpty())
                                                    <div class="flex flex-col space-y-2">
                                                        @foreach ($commentTauxList as $comment)
                                                            <div
                                                                class="flex items-center gap-3 relative rounded-xl bg-gray-200 p-2">
                                                                <img src="{{ asset($comment->investisseur->photo) }}"
                                                                    alt="Profile Picture"
                                                                    class="w-9 h-9 mt-1 rounded-full overflow-hidden object-cover">
                                                                <div class="flex-1">
                                                                    <p
                                                                        class="text-base text-black font-medium inline-block dark:text-white">
                                                                        {{ $comment->investisseur->name }}
                                                                        <!-- Afficher le nom de l'investisseur -->
                                                                    </p>
                                                                    <p class="text-sm mt-0.5">
                                                                        {{ $comment->taux }} % <!-- Afficher le taux -->
                                                                        @if ($comment->id == $oldestMinTauxComment->id)
                                                                            <!-- Afficher une étoile jaune pour le commentaire avec le plus ancien taux minimal -->
                                                                            <span class="text-yellow-500">★</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex h-full justify-center items-center">
                                                        <p class="text-md text-center text-gray-600">Aucun commentaire
                                                            sur le taux
                                                            disponible.</p>
                                                    </div>

                                                @endif



                                            </div>
                                            <form wire:submit.prevent="commentForm">
                                                <div
                                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                                    @if (!$projet->count)
                                                        <input type="number" id="tauxTrade" name="tauxTrade"
                                                            wire:model="tauxTrade"
                                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                            placeholder="Faire une offre..." oninput="validateTaux()"
                                                            required>
                                                        @error('tauxTrade')
                                                            <span class="text-red-500">{{ $message }}</span>
                                                        @enderror
                                                        <button type="submit" id="submitBtnAppel"
                                                            class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                                            <span wire:loading.remove>
                                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 18 20">
                                                                    <path
                                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                                </svg>
                                                            </span>
                                                            <span wire:loading>
                                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                                </svg>
                                                            </span>
                                                        </button>
                                                        <div id="errorMessage" class="text-red-500 mt-2 hidden"></div>
                                                    @endif
                                                </div>
                                            </form>



                                        </div>

                                        <div class="w-full flex justify-center">
                                            <span id="prixTradeError"
                                                class="text-red-500 text-sm hidden text-center py-3"></span>
                                        </div>
                                    </div>



                                </div>

                            @endif

                        @endif
                    @else
                        <!-- Si aucun investisseur n'a payé la totalité, tous les utilisateurs voient Obligation -->
                        @include('finance.components.Obligation')
                    @endif
                @endif
            </div>
        </div>
        <div class="flex flex-col md:flex-row">
            <div class="flex flex-col w-full md:w-1/2">
                <h3 class="text-xl font-semibold text-gray-600 mt-2 mb-6">
                    Description du projet
                </h3>
                <p class="text-gray-500">
                    {{ $projet->description }}
                </p>
            </div>
            @if ($projet->id_user != Auth::id() && $montantVerifie)
                <div class="w-full md:w-1/2 mt-4 px-6">
                    <!-- Catégorie du projet -->
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                        <span class="ml-2 text-sm text-gray-500">{{ $projet->categorie }}</span>
                    </div>

                    <!-- Titre du projet -->
                    <a href="details.html">
                        <h3 class="text-xl font-semibold text-gray-800 mt-2">
                            {{ $projet->name }}
                        </h3>
                    </a>

                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvesti }}%">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
                            <!-- Montant Reçu -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg"> {{ number_format($sommeInvestie, 0, ',', ' ') }}
                                    FCFA</span>
                                <span class="text-gray-500 text-sm">Reçu de
                                    {{ number_format($projet->montant, 0, ',', ' ') }} FCFA </span>
                            </div>

                            <!-- Nombre d'Investisseurs -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span>
                                <span class="text-gray-500 text-sm">Investisseurs</span>
                            </div>

                            <!-- Jours Restants -->
                            <div class="flex flex-col text-center">
                                <span class="font-semibold text-lg">{{ $this->joursRestants() }} /
                                    {{ $projet->taux }}%</span>
                                <span class="text-gray-500 text-sm">Jours restants/ taux de remboursement</span>
                            </div>

                            <!-- Progression -->
                            <div class="flex flex-col text-center">
                                <span
                                    class="font-semibold text-lg">{{ number_format($pourcentageInvesti, 2) }}%</span>
                                <span class="text-gray-500 text-sm">Progression</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex py-2 mt-2 items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                            <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                                src="{{ asset($projet->demandeur->photo) }}" alt="">
                        </div>
                        <div class="ml-2 text-sm font-semibold">
                            <span class="font-medium text-gray-500 mr-2">De</span>{{ $projet->demandeur->name }}
                        </div>
                        <!-- Bouton de déclenchement du modal -->
                        <button data-modal-target="static-modal" data-modal-toggle="static-modal"
                            class="block bg-gray-200 mt-2 hover:bg-blue-800 text-blue-800 hover:text-white font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button">
                            Plus d'informations
                        </button>
                    </div>

                    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <!-- Modal header -->
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Projet Details
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="static-modal">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-4 md:p-5 space-y-4">
                                    <!-- Client Information -->
                                    <div class="bg-white rounded-lg shadow-lg p-8">
                                        <h2 class="text-2xl font-bold mb-6 text-gray-800">Client Information</h2>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <p class="text-gray-600 font-medium">Nom du client:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Email:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->email }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Numéro de
                                                    téléphone:</p>
                                                <p class="text-gray-800">{{ $projet->demandeur->phone }}</p>
                                            </div>
                                            {{-- <div>
                                            <p class="text-gray-600 font-medium">Cote de Crédit</p>
                                            <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                                        </div> --}}
                                            <div>
                                                <p class="text-gray-600 font-medium">Adresse:</p>
                                                <p class="text-gray-800">
                                                    {{ $projet->demandeur->country }},{{ $projet->demandeur->ville }},{{ $projet->demandeur->departe }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit Request Information -->
                                    <div class="bg-white rounded-lg shadow-lg p-8">
                                        <h2 class="text-2xl font-bold mb-6 text-gray-800">Credit Request Information
                                        </h2>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <p class="text-gray-600 font-medium">Montant demandé:
                                                </p>
                                                <p class="text-gray-800">
                                                    {{ $projet->montant }} FCFA</p>
                                            </div>
                                            {{-- <div>
                                            <p class="text-gray-600 font-medium">Durée du crédit:
                                            </p>
                                            <p class="text-gray-800">{{ $demandeCredit->duree }}
                                                mois</p>
                                        </div> --}}
                                            <div>
                                                <p class="text-gray-600 font-medium">Taux du crédit:
                                                </p>
                                                <p class="text-gray-800">{{ $projet->taux }} %
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-gray-600 font-medium">Date fin:
                                                </p>
                                                <p class="text-gray-800">{{ $projet->durer }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Type de crédit:
                                                </p>
                                                <p class="text-gray-800">
                                                    {{ $projet->type_financement }}</p>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    @elseif (isset($projet->Portion_action) || (isset($projet->Portion_action) && isset($projet->Portion_obligt)))
        <h1>projet groupe avec obligation & action</h1>

        <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
            <!-- Images Section -->
            <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>

                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>
            <!-- Contenu du projet -->
            <div class="md:px-4 flex flex-col w-full md:w-1/2 ">
                @if ($pourcentageInvesti < 100)
                    <!-- Le projet n'est pas encore entièrement financé -->
                    @include('finance.components.Action&Obligation')
                @else
                    <!-- Si le projet est entièrement financé -->
                    @if ($investisseurQuiAPayeTout)
                        <!-- Vérifier si un investisseur a payé tout le montant -->
                        @if ($projet->id_user == Auth::id())
                            <!-- Le propriétaire voit Obligation -->
                            @include('finance.components.Action&Obligation')
                        @else
                            <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                            @php
                                $tauxPresent = $projet->taux; // Récupérer le taux présent pour la comparaison

                                // Trouver le plus petit taux dans la liste des commentaires
                                $minTaux = $commentTauxList->min('taux');

                                // Trouver le commentaire le plus ancien avec le taux minimal
                                $oldestMinTauxComment = $commentTauxList
                                    ->where('taux', $minTaux)
                                    ->sortBy('created_at')
                                    ->first();
                            @endphp

                            @if ($montantVerifie)

                                <div x-data="countdownTimer({{ json_encode($projet->durer) }})" class="flex flex-col">
                                    <div
                                        class="border flex items-center justify-between border-gray-300 rounded-lg p-1 shadow-md">
                                        <div x-show="projetDurer" class="text-xl font-medium">Temps restant</div>
                                        <div id="countdown" x-show="projetDurer"
                                            class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                                            @if (!$projet->count)
                                                <div x-text="jours">--</div>j
                                                <span>:</span>
                                                <div x-text="hours">--</div>h
                                                <span>:</span>
                                                <div x-text="minutes">--</div> m
                                                <span>:</span>
                                                <div x-text="seconds">--</div>s
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Afficher les messages d'erreur -->
                                    @if (session()->has('error'))
                                        <div class="bg-red-500 text-white p-2 mt-2 rounded-md">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                                        <div
                                            class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
                                            <div
                                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">



                                                @if ($commentTauxList->isNotEmpty())
                                                    <div class="flex flex-col space-y-2">
                                                        @foreach ($commentTauxList as $comment)
                                                            <div
                                                                class="flex items-center gap-3 relative rounded-xl bg-gray-200 p-2">
                                                                <img src="{{ asset($comment->investisseur->photo) }}"
                                                                    alt="Profile Picture"
                                                                    class="w-9 h-9 mt-1 rounded-full overflow-hidden object-cover">
                                                                <div class="flex-1">
                                                                    <p
                                                                        class="text-base text-black font-medium inline-block dark:text-white">
                                                                        {{ $comment->investisseur->name }}
                                                                        <!-- Afficher le nom de l'investisseur -->
                                                                    </p>
                                                                    <p class="text-sm mt-0.5">
                                                                        {{ $comment->taux }} %
                                                                        <!-- Afficher le taux -->
                                                                        @if ($comment->id == $oldestMinTauxComment->id)
                                                                            <!-- Afficher une étoile jaune pour le commentaire avec le plus ancien taux minimal -->
                                                                            <span class="text-yellow-500">★</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex h-full justify-center items-center">
                                                        <p class="text-md text-center text-gray-600">Aucun commentaire
                                                            sur le taux
                                                            disponible.</p>
                                                    </div>

                                                @endif



                                            </div>
                                            <form wire:submit.prevent="commentForm">
                                                <div
                                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                                    @if (!$projet->count)
                                                        <input type="number" id="tauxTrade" name="tauxTrade"
                                                            wire:model="tauxTrade"
                                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                            placeholder="Faire une offre..." oninput="validateTaux()"
                                                            required>
                                                        @error('tauxTrade')
                                                            <span class="text-red-500">{{ $message }}</span>
                                                        @enderror
                                                        <button type="submit" id="submitBtnAppel"
                                                            class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                                            <span wire:loading.remove>
                                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 18 20">
                                                                    <path
                                                                        d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                                                </svg>
                                                            </span>
                                                            <span wire:loading>
                                                                <svg class="w-5 h-5 animate-spin inline-block"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                                </svg>
                                                            </span>
                                                        </button>
                                                        <div id="errorMessage" class="text-red-500 mt-2 hidden"></div>
                                                    @endif
                                                </div>
                                            </form>



                                        </div>

                                        <div class="w-full flex justify-center">
                                            <span id="prixTradeError"
                                                class="text-red-500 text-sm hidden text-center py-3"></span>
                                        </div>
                                    </div>



                                </div>

                            @endif
                        @endif
                    @else
                        <!-- Si aucun investisseur n'a payé la totalité, tous les utilisateurs voient Obligation -->
                        @include('finance.components.Action&Obligation')
                    @endif

                @endif
            </div>
        </div>
        <div class="flex flex-col md:flex-row">
            <div class="flex flex-col w-full md:w-1/2">
                <h3 class="text-xl font-semibold text-gray-600 mt-2 mb-6">
                    Description du projet
                </h3>
                <p class="text-gray-500">
                    {{ $projet->description }}
                </p>
            </div>
            @if ($projet->id_user != Auth::id() && $montantVerifie)
                <div class="w-full md:w-1/2 mt-4 px-6">
                    @include('finance.components.Action&Obligation')
                </div>
            @endif

        </div>


    @endif

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }

        document.getElementById('showMontantInputButton')?.addEventListener('click', function() {
            var montantInputDiv = document.getElementById('montantInputDiv');

            if (montantInputDiv) {
                montantInputDiv.classList.toggle('hidden'); // Basculer l'affichage de la section action
            } else {
                console.warn('L\'élément montantInputDiv n\'existe pas.');
            }
        });

        document.getElementById('showActionInputButton')?.addEventListener('click', function() {
            var actionInputDiv = document.getElementById('actionInputDiv');

            if (actionInputDiv) {
                actionInputDiv.classList.toggle('hidden'); // Basculer l'affichage de la section action
            } else {
                console.warn('L\'élément actionInputDiv n\'existe pas.');
            }
        });

        const solde = @json($solde);
        const sommeRestante = @json($sommeRestante); // Récupérer sommeRestante depuis le composant Livewire

        function verifierSolde() {
            const montantInput = document.getElementById('montantInput');
            const messageSolde = document.getElementById('messageSolde');
            const messageSommeRestante = document.getElementById('messageSommeRestante');
            const confirmerButton = document.getElementById('confirmerButton'); // Correction ici

            // Désactive le bouton de confirmation si sommeRestante est égale à 0
            if (sommeRestante === 0) {
                messageSommeRestante.classList.remove('hidden');
                messageSommeRestante.innerText = 'La somme demandée est totalement collectée';
                confirmerButton.disabled = true;
                return;
            }

            // Récupérer la valeur directement sans modifications
            const montant = montantInput.value;

            // Vérifie si la valeur est vide
            if (montant.trim() === '') {
                messageSolde.classList.add('hidden');
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true; // Désactiver le bouton si le montant est vide
                return;
            }

            // Convertir en nombre flottant
            const montantFloat = parseFloat(montant);

            // Vérifiez si la conversion a fonctionné (montant est NaN si non valide)
            if (isNaN(montantFloat)) {
                messageSolde.classList.remove('hidden');
                messageSolde.innerText = 'Le montant saisi n\'est pas valide';
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true;
                return;
            }

            // Vérifie si le montant saisi dépasse le solde
            if (montantFloat > solde) {
                messageSolde.classList.remove('hidden');
                messageSolde.innerText = 'Votre solde est insuffisant';
                messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                confirmerButton.disabled = true; // Désactive le bouton si le solde est insuffisant
            } else {
                messageSolde.classList.add('hidden');

                // Vérifie si le montant est supérieur à la somme restante
                if (montantFloat > sommeRestante) {
                    messageSommeRestante.classList.remove('hidden');
                    messageSommeRestante.innerText = 'Le montant doit être inférieur ou égal à la somme restante';
                    confirmerButton.disabled = true; // Désactive le bouton si le montant est supérieur à la somme restante
                } else {
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante

                    // Vérifie si le montant est supérieur à zéro pour activer le bouton
                    confirmerButton.disabled = montantFloat <= 0; // Désactive le bouton si le montant est négatif ou zéro
                }
            }
        }

        const sommeRestanteAction = @json($sommeRestanteAction);
        const prixAction = @json($projet->Portion_action);

        function verifierActions() {
            const actionInput = document.getElementById('actionInput');
            const messageActionRestante = document.getElementById('messageActionRestante');
            const confirmerActionButton = document.getElementById('confirmerActionButton');

            if (sommeRestanteAction === 0) {
                messageActionRestante.classList.remove('hidden');
                messageActionRestante.innerText = 'Pas d\'actions disponibles';
                confirmerActionButton.disabled = true;
                return;
            }

            const action = actionInput.value;

            if (action.trim() === '') {
                messageActionRestante.classList.add('hidden');
                confirmerActionButton.disabled = true;
                return;
            }

            const actionInt = parseInt(action);

            if (isNaN(actionInt) || actionInt <= 0) {
                messageActionRestante.classList.remove('hidden');
                messageActionRestante.innerText = 'Le nombre d\'actions n\'est pas valide';
                confirmerActionButton.disabled = true;
                return;
            }

            if (actionInt > sommeRestanteAction) {
                messageActionRestante.classList.remove('hidden');
                messageActionRestante.innerText = 'Le nombre d\'actions doit être inférieur ou égal au nombre restant';
                confirmerActionButton.disabled = true;
                return;
            }

            // Calculer le coût total des actions
            const totalCost = actionInt * prixAction;

            if (totalCost > solde) {
                messageActionRestante.classList.remove('hidden');
                messageActionRestante.innerText = 'Solde insuffisant pour acheter ce nombre d\'actions';
                confirmerActionButton.disabled = true;
            } else {
                messageActionRestante.classList.add('hidden');
                confirmerActionButton.disabled = false;
            }
        }


        ///////////

        const tauxPresent = @json($projet->taux); // Le taux déjà présent
        const tauxTradeInput = document.getElementById('tauxTrade');
        const submitBtn = document.getElementById('submitBtnAppel');
        const errorMessage = document.getElementById('errorMessage');

        // Fonction de validation du taux
        function validateTaux() {
            if (parseFloat(tauxTradeInput.value) >= parseFloat(tauxPresent)) {
                errorMessage.innerText = `Le taux ne peut pas être supérieur à ${tauxPresent}.`;
                errorMessage.classList.remove('hidden');
                submitBtn.disabled = true;
                return false;
            } else {
                errorMessage.classList.add('hidden');
                submitBtn.disabled = false;
                return true;
            }
        }
    </script>

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('refreshPage', () => {
                location.reload(); // Recharge la page
            });
        });
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdownTimer', (projetDurer) => ({
                projetDurer: projetDurer ? new Date(projetDurer) : null,
                jours: '--',
                hours: '--',
                minutes: '--',
                seconds: '--',
                interval: null,
                isCountdownActive: false,
                isFinished: false,
                hasSubmitted: false, // Variable pour éviter la soumission multiple

                init() {
                    if (this.projetDurer) {
                        this.startDate = new Date(this.projetDurer);
                        this.startCountdown();
                    }


                },

                startCountdown() {
                    if (this.isCountdownActive) return; // Empêche le redémarrage si déjà actif

                    this.clearExistingInterval();
                    this.updateCountdown();
                    this.interval = setInterval(this.updateCountdown.bind(this), 1000);
                    this.isCountdownActive = true;
                },

                clearExistingInterval() {
                    if (this.interval) {
                        clearInterval(this.interval);
                    }
                },

                updateCountdown() {
                    const currentDate = new Date();
                    const difference = this.projetDurer - currentDate;

                    if (difference <= 0) {
                        this.endCountdown();
                        return;
                    }

                    this.jours = Math.floor(difference / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((difference % (1000 * 60)) / 1000);
                },

                endCountdown() {
                    this.clearExistingInterval();
                    this.jours = this.hours = this.minutes = this.seconds = 0;
                    this.isFinished = true;

                    // Soumettre l'événement seulement une fois
                    if (!this.hasSubmitted) {
                        setTimeout(() => {
                            Livewire.dispatch('compteReboursFini');
                            this.hasSubmitted = true; // Empêcher la soumission multiple
                        }, 100); // Petit délai pour laisser le temps à l'affichage de se mettre à jour
                    }

                    // Mettre à jour l'interface
                    document.getElementById('countdown').innerText = "Temps écoulé !";

                },
            }));
        });
    </script>
</div>
