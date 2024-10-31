
<div
    class="grid grid-cols-3 gap-4 text-sm border border-gray-300 rounded-lg p-3 shadow-md text-gray-600 mt-4 w-full justify-between">

    <!-- Montant de base (sans pourcentage) -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($demandeCredit->montant, 0, ',', ' ') }}
            FCFA</span>
        <span class="text-gray-500 text-sm">Capital Demandé (sans intérêt)</span>
    </div>

    <!-- Taux -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($demandeCredit->taux, 0, ',', ' ') }}%</span>
        <span class="text-gray-500 text-sm">Taux</span>
    </div>
    @php
        // Capital demandé
        $capital_demande = $demandeCredit->montant;

        // Taux d'intérêt
        $taux = $demandeCredit->taux;

        // Calcul du capital total incluant les intérêts
        $capital_total = $capital_demande * (1 + $taux / 100);
    @endphp
    <!-- Montant total (avec pourcentage) -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ number_format($capital_total, 0, ',', ' ') }}
            FCFA</span>
        <span class="text-gray-500 text-sm">Capital Total (avec intérêt)</span>
    </div>
    <!-- Investisseurs -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinct }}</span>
        <span class="text-gray-500 text-sm">Investisseurs</span>
    </div>
    <!-- Periode de remboursement -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ $demandeCredit->duree }}</span>
        <span class="text-gray-500 text-sm">Periode de remboursement</span>
    </div>
    <!-- Jours restant -->
    <div class="flex flex-col text-center">
        <span class="font-semibold text-lg">{{ $this->joursRestants() }}</span>
        <span class="text-gray-500 text-sm">Jours restants</span>
    </div>
    <div class="sm:col-span-3">

        <!-- ROI -->
        <div class="flex flex-col text-center text-red-500">
            <span class="font-semibold text-lg">{{ number_format($capital_total - $demandeCredit->montant, 0, ',', ' ') }}</span>
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
