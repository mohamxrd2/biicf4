<div>
    
    <div class="container py-4 ">

        <div class="bg-gray-100 p-8 min-h-screen">
            <h1 class="text-2xl font-bold text-gray-800">Mes Investissements</h1>
            <p class="text-gray-600 mb-6">Suivez vos investissements en attente et leur statut</p>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1s1-.448 1-1V9c0-.552-.448-1-1-1zm0-5a1.5 1.5 0 000 3h.01A1.5 1.5 0 0012 3zM12 18a1.5 1.5 0 00-1.5 1.5c0 .74.54 1.4 1.28 1.5h.44c.74-.1 1.28-.76 1.28-1.5A1.5 1.5 0 0012 18zM7.757 7.757a1 1 0 00-1.414 0L3.586 10.5a1 1 0 000 1.415l3.757 3.757a1 1 0 001.414-1.414L5.414 12l2.343-2.343a1 1 0 000-1.415zM16.243 7.757a1 1 0 011.414 0L21.414 10.5a1 1 0 010 1.415l-3.757 3.757a1 1 0 11-1.414-1.414L18.586 12l-2.343-2.343a1 1 0 010-1.415z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Total en Attente</p>
                        <p class="text-2xl font-semibold text-gray-800">
                            {{ number_format($totalMontants, 2, ',', ' ') }}FCFA</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 17.25l5-5 3 3 5-5 5.25 5.25" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Rendement Attendu</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $calculInteret }}FCFA</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a2 2 0 00-2-2h-3v-4H7v4H4a2 2 0 00-2 2v2h5M10 4V3a1 1 0 011-1h2a1 1 0 011 1v1m-6 4v9h8V8" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Investissements Actifs</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $countRemboursements }}</p>
                    </div>
                </div>
            </div>

            <!-- Tableau des investissements -->
            <div class="bg-white shadow rounded-lg p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="pb-3 text-gray-600 font-semibold">Objet d'investissement</th>
                            <th class="pb-3 text-gray-600 font-semibold">Montant</th>
                            <th class="pb-3 text-gray-600 font-semibold">Rendu Attendu(ROI)</th>
                            <th class="pb-3 text-gray-600 font-semibold">Emprunteur</th>
                            <th class="pb-3 text-gray-600 font-semibold">Date</th>
                            <th class="pb-3 text-gray-600 font-semibold">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($remboursements as $remboursement)
                            @php
                                $montant_total =
                                    ($remboursement->montant_capital * $remboursement->montant_interet) / 100;
                            @endphp
                            <tr>
                                <td class="py-3">{{ $remboursement->description }} </td>
                                <td class="py-3">{{ $remboursement->montant_capital }} FCFA</td>
                                <td class="py-3">{{ $remboursement->montant_interet }}%({{ $montant_total }} FCFA)
                                </td>
                                <td class="py-3">{{ $remboursement->creditgrp->emprunteur->name }}
                                </td>
                                <td class="py-3">
                                    {{ Carbon\Carbon::parse($remboursement->date_remboursement)->format('d F Y') }}</td>
                                <td class="py-3">
                                    <span
                                        class="px-2 py-1 text-sm font-semibold bg-blue-100 text-blue-800 rounded-full">en
                                        cours
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>



            <!-- pour les projets -->

            <h1 class="text-2xl mt-5 font-bold text-gray-800">Mes Projets</h1>
            <p class="text-gray-600 mb-6">Suivez vos projets en attente et leur statut</p>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1s1-.448 1-1V9c0-.552-.448-1-1-1zm0-5a1.5 1.5 0 000 3h.01A1.5 1.5 0 0012 3zM12 18a1.5 1.5 0 00-1.5 1.5c0 .74.54 1.4 1.28 1.5h.44c.74-.1 1.28-.76 1.28-1.5A1.5 1.5 0 0012 18zM7.757 7.757a1 1 0 00-1.414 0L3.586 10.5a1 1 0 000 1.415l3.757 3.757a1 1 0 001.414-1.414L5.414 12l2.343-2.343a1 1 0 000-1.415zM16.243 7.757a1 1 0 011.414 0L21.414 10.5a1 1 0 010 1.415l-3.757 3.757a1 1 0 11-1.414-1.414L18.586 12l-2.343-2.343a1 1 0 010-1.415z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Total en Attente</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $totalMontantp }}</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 17.25l5-5 3 3 5-5 5.25 5.25" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Rendement Moyen Attendu</p>
                        <p class="text-2xl font-semibold text-gray-800">15.0%</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a2 2 0 00-2-2h-3v-4H7v4H4a2 2 0 00-2 2v2h5M10 4V3a1 1 0 011-1h2a1 1 0 011 1v1m-6 4v9h8V8" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Investisseurs Actifs</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $countProjets }}</p>
                    </div>
                </div>
            </div>


            <div class="bg-white shadow-md rounded-lg overflow-hidden">

                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Nom du Projet</th>
                            <th class="py-3 px-6 text-left">Montant</th>
                            <th class="py-3 px-6 text-left">Date</th>
                            <th class="py-3 px-6 text-left">Categorie</th>
                            <th class="py-3 px-6 text-left">Statut</th>

                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-base font-semibold tracking-wide">
                        @foreach ($projets as $projet)
                            <!-- Exemple de données statiques pour la démonstration -->
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $projet->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $projet->montant }} fcfa</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') }} </td>
                                <td class="py-3 px-6 text-left">{{ $projet->categorie }} </td>
                                <td class="py-3 px-6 text-left">
                                    <span
                                        class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">{{ $projet->etat }}</span>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

            <!-- pour les demande de credits -->

            <h1 class="text-2xl mt-5 font-bold text-gray-800">Mes Demandes de crédits</h1>
            <p class="text-gray-600 mb-6">Suivez vos crédits en attente et leur statut</p>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-.552 0-1 .448-1 1v6c0 .552.448 1 1 1s1-.448 1-1V9c0-.552-.448-1-1-1zm0-5a1.5 1.5 0 000 3h.01A1.5 1.5 0 0012 3zM12 18a1.5 1.5 0 00-1.5 1.5c0 .74.54 1.4 1.28 1.5h.44c.74-.1 1.28-.76 1.28-1.5A1.5 1.5 0 0012 18zM7.757 7.757a1 1 0 00-1.414 0L3.586 10.5a1 1 0 000 1.415l3.757 3.757a1 1 0 001.414-1.414L5.414 12l2.343-2.343a1 1 0 000-1.415zM16.243 7.757a1 1 0 011.414 0L21.414 10.5a1 1 0 010 1.415l-3.757 3.757a1 1 0 11-1.414-1.414L18.586 12l-2.343-2.343a1 1 0 010-1.415z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Total en Attente</p>
                        <p class="text-2xl font-semibold text-gray-800">
                            {{ number_format($totalMontant, 2, ',', ' ') }}FCFA</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 17.25l5-5 3 3 5-5 5.25 5.25" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Rendement Moyen Attendu</p>
                        <p class="text-2xl font-semibold text-gray-800">15.0%</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6 flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a2 2 0 00-2-2h-3v-4H7v4H4a2 2 0 00-2 2v2h5M10 4V3a1 1 0 011-1h2a1 1 0 011 1v1m-6 4v9h8V8" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Demandes Credits Actifs</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $countDemandecredits }}</p>
                    </div>
                </div>
            </div>


            <div class="bg-white shadow-md rounded-lg overflow-hidden">

                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">objet de financement</th>
                            <th class="py-3 px-6 text-left">Montant</th>
                            <th class="py-3 px-6 text-left">Date</th>
                            <th class="py-3 px-6 text-left">Type</th>
                            <th class="py-3 px-6 text-left">Statut</th>

                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-base font-semibold tracking-wide">
                        <!-- Exemple de données statiques pour la démonstration -->
                        @foreach ($demandecredits as $demandecredit)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $demandecredit->objet_financement }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ number_format($demandecredit->montant, 2, ',', ' ') }} FCFA</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($demandecredit->date_demande)->format('d/m/Y') }}</td>
                                <td class="py-3 px-6 text-left">{{ $demandecredit->type_financement }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if ($demandecredit->status === 'terminer')
                                        <span
                                            class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">accepté</span>
                                    @elseif ($demandecredit->status === 'refuser')
                                        <span
                                            class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">refusé</span>
                                    @else
                                        <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">en
                                            cours</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
