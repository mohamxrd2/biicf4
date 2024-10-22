<p>Actions / prix par Action est de {{ number_format($projet->Portion_action, 0, ',', ' ') }} FCFA</p>

<div class="w-full bg-gray-200 rounded-full h-2 mt-2">
    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvestiAction }}%">
    </div>
</div>

<div class="mt-4">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
        <!-- Montant Reçu -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">
                {{ number_format($sommeInvestieActions, 0, ',', ' ') }}
            </span>
            <span class="text-gray-500 text-sm">Reçu sur
                {{ number_format($projet->nombreActions, 0, ',', ' ') }}</span>
        </div>

        <!-- Nombre d'Investisseurs -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ $nombreInvestisseursDistinctAction }}</span>
            <span class="text-gray-500 text-sm">Actionnaires</span>
        </div>

        <!-- Jours Restants -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ $this->joursRestants() }} </span>
            <span class="text-gray-500 text-sm">Jours restants</span>
        </div>

        <!-- Progression -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">{{ number_format($pourcentageInvestiAction, 2) }}%</span>
            <span class="text-gray-500 text-sm">Progression</span>
        </div>
    </div>
</div>

<p>Obligation</p>

<div class="w-full bg-gray-200 rounded-full h-2 mt-2">
    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentageInvesti }}%">
    </div>
</div>

<div class="mt-4">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mt-4 w-full justify-between">
        <!-- Montant Reçu -->
        <div class="flex flex-col text-center">
            <span class="font-semibold text-lg">
                {{ number_format($sommeInvestie, 0, ',', ' ') }}
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
            <span class="font-semibold text-lg">{{ number_format($pourcentageInvesti, 2) }}%</span>
            <span class="text-gray-500 text-sm">Progression</span>
        </div>
    </div>
</div>

<div class="mt-4">
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
    <div class="border border-gray-300 rounded-lg p-6 shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">
            Participer au financement du projet
        </h3>
        <p class="text-gray-600 text-md mb-6">
            Contribuez au financement du projet pour l'aider à atteindre la somme souhaitée.
        </p>
        @if ($projet->id_user != Auth::id())
            <div class="flex">
                <!-- Bouton pour ajouter un montant -->
                <button id="showMontantInputButton"
                    class="w-1/2 py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium">
                    Ajouter un montant
                </button>
                <!-- Bouton pour ajouter une action -->
                <button id="showActionInputButton"
                    class="w-1/2 py-3 bg-blue-600 hover:bg-blue-700 transition-colors rounded-md text-white font-medium">
                    Ajouter une action
                </button>
            </div>
        @else
            <button
                class="w-full py-3 bg-gray-200 hover:bg-gray-300 transition-colors rounded-md text-black font-medium"
                disabled>
                Ceci est votre projet
            </button>
        @endif
    </div>

    <!-- Section pour entrer le montant -->
    <div id="montantInputDiv" class="mt-6 hidden">
        <p class="text-md mb-3 text-gray-700">Le montant restant est de : <span class="font-bold">
                {{ number_format($sommeRestante, 0, ',', ' ') }} FCFA</span></p>

        <input type="number" id="montantInput"
            class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Entrez le montant" wire:model="montant" oninput="verifierSolde()">

        <p id="messageSolde" class="text-red-500 text-center mt-2 hidden">Votre solde est
            insuffisant</p>
        <p id="messageSommeRestante" class="text-red-500 text-center mt-2 hidden">Le montant
            doit être supérieur ou égal à la
            somme restante</p>

        <button id="confirmer"
            class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4"
            wire:click="confirmer" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Confirmer le montant
            </span>
            <span wire:loading>
                Chargement...
            </span>
        </button>
    </div>

    <!-- Section pour entrer une action (à ajouter selon vos besoins) -->
    <div id="actionInputDiv" class="mt-6 hidden">
        <!-- Contenu du formulaire pour ajouter une action -->
        <p class="text-md mb-3 text-gray-700">Entrez le nombre d'actions que vous souhaitez
            acheter.</p>

        <input type="number" id="actionInput"
            class="w-full py-3 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Nombre d'actions" wire:model="action">

        <button id="confirmerActionButton"
            class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium mt-4"
            wire:click="confirmerAction" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Confirmer l'action
            </span>
            <span wire:loading>
                Chargement...
            </span>
        </button>
    </div>
</div>
