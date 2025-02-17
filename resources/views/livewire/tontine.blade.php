<div class="min-h-screen p-8">
    @if (!$tontineStart)
        <x-tontine-form :server-time="$serverTime" :errors="$errors" />
    @else
        <!-- Display active tontine -->
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tontines en cours</h2>
            @if ($tontineEnCours)
                <x-tontine-card :id="$tontineEnCours->id" :montant="$tontineEnCours->montant_cotisation" :frequence="$tontineEnCours->frequence" :dateDebut="$tontineEnCours->date_debut"
                    :dateFin="$tontineEnCours->date_fin" :progression="$pourcentage" :cotisationsEffectuees="$cotisationsCount" :cotisationsTotales="$tontineEnCours->nombre_cotisations" :montantCollecte="$cotisationSum"
                    :prochainPaiement="$tontineEnCours->next_payment_date" status="active" />
            @endif
        </div>
    @endif
    <!-- Historique des tontines -->
    <div class="max-w-3xl mx-auto mt-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Historique des tontines</h2>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all">Toutes les tontines</option>
                    <option value="active">Tontines actives</option>
                    <option value="completed">Tontines terminées</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($tontineDatas as $tontine)
                <x-tontine-card :id="$tontineEnCours->id" :montant="$tontineEnCours->montant_cotisation" :frequence="$tontineEnCours->frequence" :dateDebut="$tontineEnCours->date_debut"
                    :dateFin="$tontineEnCours->date_fin" :progression="$pourcentage" :cotisationsEffectuees="$cotisationsCount" :cotisationsTotales="$tontineEnCours->nombre_cotisations" :montantCollecte="$cotisationSum"
                    :prochainPaiement="$tontineEnCours->next_payment_date" status="active" />
            @empty
                <div
                    class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg shadow-sm border border-gray-200">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune tontine en cours</h3>
                    <p class="text-gray-600">Commencez par créer une nouvelle tontine pour voir les détails ici</p>

                </div>
            @endforelse
        </div>
    </div>
</div>
