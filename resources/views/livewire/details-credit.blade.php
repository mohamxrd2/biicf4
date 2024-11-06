<div>
    <!-- Image du demandeCredit -->
    <div class="flex flex-col justify-center items-center text-center bg-gray-200 p-4 rounded-lg mb-6">
        <h1 class="text-lg font-bold">DETAILS DE LA DEMANDE DE CREDIT</h1>
    </div>
    @if (
        $investisseurQuiAPayeTout ||
            ($demandeCredit->type_financement === 'négocié' &&
                isset($demandeCredit) &&
                $demandeCredit->type_financement === 'offre-composite') ||
            $demandeCredit->type_financement === 'négocié')
        @include('finance.components.entete')
    @endif


    <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
        <!-- information Section -->
        @include('finance.components.Informations')


        @if ($demandeCredit->type_financement === 'offre-composite')
            <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
                @if ($investisseurQuiAPayeTout)
                    <!-- Vérifier si un investisseur a payé tout le montant -->
                    <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                    @php
                        $tauxPresent = $demandeCredit->taux; // Récupérer le taux présent pour la comparaison

                        // Trouver le plus petit taux dans la liste des commentaires
                        $minTaux = $commentTauxList->min('taux');

                        // Trouver le commentaire le plus ancien avec le taux minimal
                        $oldestMinTauxComment = $commentTauxList
                            ->where('taux', $minTaux)
                            ->sortBy('created_at')
                            ->first();
                    @endphp


                    <div x-data="countdownTimer({{ json_encode($demandeCredit->duree) }})" class="flex flex-col">
                        <div class="border flex items-center justify-between border-gray-300 rounded-lg p-1 shadow-md">
                            <div x-show="projetDurer" class="text-xl font-medium">Temps restant</div>
                            <div id="countdown" x-show="projetDurer"
                                class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                                @if (!$demandeCredit->count)
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

                            <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
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
                                        @if (!$demandeCredit->count)
                                            <input type="number" id="tauxTrade" name="tauxTrade" wire:model="tauxTrade"
                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                placeholder="Faire une offre..." oninput="validateTaux()" required>
                                            @error('tauxTrade')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                            <button type="submit" id="submitBtnAppel"
                                                class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                                <span wire:loading.remove>
                                                    <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                                <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                            </div>
                        </div>



                    </div>
                @else
                    <!-- Catégorie du demandeCredit -->
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                        <span class="ml-2 text-sm text-gray-500">{{ $demandeCredit->objet_financement }}</span>
                    </div>
                    <div>

                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvesti }}%">
                            </div>
                        </div>

                        <div class="mt-4">


                            <div
                                class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
                                <!-- Montant Reçu -->
                                <div class="flex flex-col text-center">
                                    <span class="font-semibold text-lg">
                                        {{ number_format($sommeInvestie, 0, ',', ' ') }}
                                        FCFA</span>
                                    <span class="text-gray-500 text-sm">Reçu de
                                        {{ number_format($demandeCredit->montant, 0, ',', ' ') }} FCFA </span>
                                </div>

                                <!-- Nombre d'Investisseurs -->
                                <div class="flex flex-col text-center">
                                    <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span>
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
                                        class="font-semibold text-lg">{{ number_format($pourcentageInvesti, 2) }}%</span>
                                    <span class="text-gray-500 text-sm">Progression</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex py-2 mt-2 items-center">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                                    src="{{ asset($userDetails->photo) }}" alt="">
                            </div>
                            <div class="ml-2 text-sm font-semibold">
                                <span class="font-medium text-gray-500 mr-2">De</span>{{ $userDetails->name }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">

                        @if (session()->has('success'))
                            <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
                        @endif
                        @if (session()->has('error'))
                            <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
                        @endif
                        <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                Approuver la demande crédit
                            </h3>
                            <p class="text-gray-600 text-md mb-6">
                                Contribuez a la finalisation de l'achat d'un produit.
                            </p>

                            <!-- Afficher un message si l'objet du financement est 'demande-directe' -->
                            <div class="flex space-x-4">
                                @if ($notification->reponse == 'approved')
                                    <div class="text-green-600 font-bold">
                                        Demande de crédit approuvée.
                                    </div>
                                @elseif ($notification->reponse == 'refuser')
                                    <div class="text-red-600 font-bold">
                                        Demande de crédit refusée.
                                    </div>
                                @elseif ($pourcentageInvesti < 100)
                                    <button id="showInputButton"
                                        class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                                        Ajouter un montant
                                    </button>
                                @else
                                    <div class="text-green-600 font-bold">
                                        Demande de crédit terminé.
                                    </div>
                                @endif

                            </div>
                            <div id="inputDiv" class="mt-6 hidden">
                                <input type="number" id="montantInput"
                                    class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                                <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est
                                    insuffisant</p>
                                <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le
                                    montant doit être
                                    supérieur
                                    ou égal à la somme restante</p>

                                <button id="confirmerButton"
                                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4 relative"
                                    wire:click="confirmer" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Confirmer le montant</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 text-white inline-block"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>

                            </div>

                        </div>
                    </div>
                @endif

            </div>
        @elseif ($demandeCredit->type_financement === 'négocié')
            <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
                <!-- Vérifier si un investisseur a payé tout le montant -->
                <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                @php
                    $tauxPresent = $demandeCredit->taux; // Récupérer le taux présent pour la comparaison

                    // Trouver le plus petit taux dans la liste des commentaires
                    $minTaux = $commentTauxList->min('taux');

                    // Trouver le commentaire le plus ancien avec le taux minimal
                    $oldestMinTauxComment = $commentTauxList->where('taux', $minTaux)->sortBy('created_at')->first();
                @endphp


                <div x-data="countdownTimer({{ json_encode($demandeCredit->duree) }})" class="flex flex-col">
                    <div class="border flex items-center justify-between border-gray-300 rounded-lg p-1 shadow-md">
                        <div x-show="projetDurer" class="text-xl font-medium">Temps restant</div>
                        <div id="countdown" x-show="projetDurer"
                            class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                            @if (!$demandeCredit->count)
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

                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">



                                @if ($commentTauxList->isNotEmpty())
                                    <div class="flex flex-col space-y-2">
                                        @foreach ($commentTauxList as $comment)
                                            <div class="flex items-center gap-3 relative rounded-xl bg-gray-200 p-2">
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
                                    @if (!$demandeCredit->count)
                                        <input type="number" id="tauxTrade" name="tauxTrade" wire:model="tauxTrade"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Faire une offre..." oninput="validateTaux()" required>
                                        @error('tauxTrade')
                                            <span class="text-red-500">{{ $message }}</span>
                                        @enderror
                                        <button type="submit" id="submitBtnAppel"
                                            class="justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600 relative">
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                            <span id="prixTradeError" class="text-red-500 text-sm hidden text-center py-3"></span>
                        </div>
                    </div>



                </div>
            </div>
        @else
            <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
                <div class="flex items-center mb-2">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    <span class="ml-2 text-sm text-gray-500">{{ $demandeCredit->objet_financement }}</span>
                </div>
                @php
                    // Récupérer les données actuelles
                    $montantTotal = $notification->data['montant'];
                    $taux = $demandeCredit->taux ?? 0;

                    // Calculer le montant sans pourcentage (montant de base)
                    $montantDeBase = $montantTotal / (1 + $taux / 100);
                @endphp
                <div
                    class="grid grid-cols-2 gap-4 text-sm border border-gray-300 rounded-lg p-3 shadow-md text-gray-600 mt-4 w-full justify-between">

                    <!-- Montant de base (sans pourcentage) -->
                    <div class="flex flex-col text-center">
                        <span class="font-semibold text-lg">{{ number_format($montantDeBase, 0, ',', ' ') }}
                            FCFA</span>
                        <span class="text-gray-500 text-sm">Capital Demandé (sans intérêt)</span>
                    </div>

                    <!-- Taux -->
                    <div class="flex flex-col text-center">
                        <span class="font-semibold text-lg">{{ $taux }}%</span>
                        <span class="text-gray-500 text-sm">Taux</span>
                    </div>

                    <!-- Montant total (avec pourcentage) -->
                    <div class="flex flex-col text-center">
                        <span class="font-semibold text-lg">{{ number_format($montantTotal, 0, ',', ' ') }}
                            FCFA</span>
                        <span class="text-gray-500 text-sm">Capital Total (avec intérêt)</span>
                    </div>
                    <!-- Montant total (avec pourcentage) -->
                    <div class="flex flex-col text-center">
                        <span class="font-semibold text-lg">{{ $demandeCredit->duree }}</span>
                        <span class="text-gray-500 text-sm"> Fin de Remboursement</span>
                    </div>
                    <div class="sm:col-span-2">

                        <!-- Montant total (avec pourcentage) -->
                        <div class="flex flex-col text-center text-red-500">
                            <span
                                class="font-semibold text-lg">{{ number_format($montantTotal - $montantDeBase, 0, ',', ' ') }}</span>
                            <span class="text-red-500 text-sm">Retour sur Investissement (ROI)</span>
                        </div>

                    </div>
                </div>
                <div class="flex py-2 mt-2 items-center">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                        <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                            src="{{ asset($userDetails->photo) }}" alt="">
                    </div>
                    <div class="ml-2 text-sm font-semibold">
                        <span class="font-medium text-gray-500 mr-2">De</span>{{ $userDetails->name }}
                    </div>
                </div>
                <div class="mt-4">

                    @if (session()->has('success'))
                        <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
                    @endif
                    @if (session()->has('error'))
                        <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
                    @endif
                    <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">
                            Approuver la demande crédit
                        </h3>
                        <p class="text-gray-600 text-md mb-6">
                            Contribuez a la finalisation de l'achat d'un produit.
                        </p>

                        <!-- Afficher un message si l'objet du financement est 'demande-directe' -->
                        <div class="flex space-x-4">
                            @if ($notification->reponse == 'approved')
                                <div class="text-green-600 font-bold">
                                    Demande de crédit approuvée.
                                </div>
                            @elseif ($notification->reponse == 'refuser')
                                <div class="text-red-600 font-bold">
                                    Demande de crédit refusée.
                                </div>
                            @elseif ($pourcentageInvesti < 100)
                                <!-- Bouton Approuver -->
                                <button id="approveButton" wire:click="approuver({{ $montantDeBase }})"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Approuver</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 text-white inline-block"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z"></path>
                                        </svg>
                                    </span>
                                </button>

                                <!-- Bouton Refuser -->
                                <button id="rejectButton" wire:click="refuser"
                                    class="w-full py-3 bg-red-600 hover:bg-red-700 transition-colors rounded-md text-white font-medium">
                                    Refuser
                                </button>
                            @else
                                <div class="text-green-600 font-bold">
                                    Demande de crédit terminé.
                                </div>
                            @endif

                        </div>
                        <div id="inputDiv" class="mt-6 hidden">
                            <input type="number" id="montantInput"
                                class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

                            <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est
                                insuffisant</p>
                            <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le
                                montant doit être
                                supérieur
                                ou égal à la somme restante</p>

                            <button id="confirmerButton"
                                class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4 relative"
                                wire:click="confirmer" wire:loading.attr="disabled">
                                <span wire:loading.remove>Confirmer le montant</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 text-white inline-block"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z">
                                        </path>
                                    </svg>
                                </span>
                            </button>

                        </div>

                    </div>
                </div>
            </div>

        @endif

    </div>
    @if (isset($demandeCredit) && $demandeCredit->type_financement === 'offre-composite')
        <script>
            document.getElementById('showInputButton').addEventListener('click', function() {
                var inputDiv = document.getElementById('inputDiv');
                inputDiv.classList.toggle('hidden'); // Basculer l'affichage
            });

            const solde = @json($solde);
            const sommeRestante = @json($sommeRestante); // Récupérer sommeRestante depuis le composant Livewire

            function verifierSolde() {
                const montantInput = document.getElementById('montantInput');
                const messageSolde = document.getElementById('messageSolde');
                const messageSommeRestante = document.getElementById('messageSommeRestante');
                const confirmerButton = document.getElementById('confirmerButton');

                //Récupérer la valeur directement sans modifications
                const montant = montantInput.value;

                //Vérifie si la valeur est vide
                if (montant.trim() === '') {
                    messageSolde.classList.add('hidden');
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true; // Désactiver le bouton si le montant est vide
                    return;
                }

                //Convertir en nombre flottant
                const montantFloat = parseFloat(montant);

                //Vérifiez si la conversion a fonctionné (montant est NaN si non valide)
                if (isNaN(montantFloat)) {
                    messageSolde.classList.remove('hidden');
                    messageSolde.innerText = 'Le montant saisi n\'est pas valide';
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true;
                    return;
                }

                //Vérifie si le montant saisi dépasse le solde
                if (montantFloat > solde) {
                    messageSolde.classList.remove('hidden');
                    messageSolde.innerText = 'Votre solde est insuffisant';
                    messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante
                    confirmerButton.disabled = true; // Désactive le bouton si le solde est insuffisant
                } else {
                    messageSolde.classList.add('hidden');

                    //Vérifie si le montant est supérieur à la somme restante
                    if (montantFloat > sommeRestante) {
                        messageSommeRestante.classList.remove('hidden');
                        messageSommeRestante.innerText = 'Le montant doit être inférieur ou égal à la somme restante';
                        confirmerButton.disabled = true; // Désactive le bouton si le montant est supérieur à la somme restante
                    } else {
                        messageSommeRestante.classList.add('hidden'); // Cacher le message de somme restante

                        //Vérifie si le montant est supérieur à zéro pour activer le bouton
                        confirmerButton.disabled = montantFloat <= 0; // Désactive le bouton si le montant est négatif ou zéro
                    }
                }
            }
        </script>
    @endif
</div>
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
