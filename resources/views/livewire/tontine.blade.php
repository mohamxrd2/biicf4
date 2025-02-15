<div class="min-h-screen p-8">
    @if (!$tontineStart)
        <x-tontine-form :server-time="$serverTime" :errors="$errors" />
    @else
        <!-- Display active tontine -->
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tontines en cours</h2>
            @if ($tontineEnCours)
                <x-tontine-card :id="$tontineEnCours->id" :montant="$tontineEnCours->montant_cotisation" :frequence="$tontineEnCours->frequence" :dateDebut="$tontineEnCours->date_debut"
                    :dateFin="$tontineEnCours->date_fin" :progression="65" :cotisationsEffectuees="15" :cotisationsTotales="24" :montantCollecte="180000"
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
            @foreach ($tontineDatas as $tontine)
                <x-tontine-card :id="$tontine->id" :montant="$tontine->montant_cotisation" :frequence="$tontine->frequence" :dateDebut="$tontine->date_debut"
                    :dateFin="$tontine->date_fin" :progression="rand(10, 100)" :cotisationsEffectuees="rand(1, 24)" :cotisationsTotales="24" :montantCollecte="rand(50000, 500000)"
                    :prochainPaiement="$tontine->next_payment_date" status="active" />
            @endforeach
        </div>
    </div>
</div>
