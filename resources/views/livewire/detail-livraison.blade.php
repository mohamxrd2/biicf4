<!-- resources/views/livewire/detail-livraison.blade.php -->

<div class="container mx-auto py-8" wire:poll.15000ms>
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Détails de la Demande</h1>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Nom & prénom :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->user->name }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Véhicule :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->vehicle }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Expérience :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->experience }}</p>
        </div>

        

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">État :</p>
            <div class="mt-2">
                @if ($livraison->etat == 'En cours')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-yellow-800 bg-yellow-100">{{ $livraison->etat }}</span>
                @elseif ($livraison->etat == 'Accepté')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-green-800 bg-green-100">{{ $livraison->etat }}</span>
                @elseif ($livraison->etat == 'Refusé')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-red-800 bg-red-100">{{ $livraison->etat }}</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-gray-800 bg-gray-100">{{ $livraison->etat }}</span>
                @endif
            </div>
        </div>

        <!-- Ajoutez d'autres détails ici -->
        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Matricule :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->matricule }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Disponibilité :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->availability }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Zone :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->zone }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Commentaires :</p>
            <p class="text-xl font-bold text-gray-800">{{ $livraison->comments }}</p>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Photo :</p>
            <div class="mt-2">
                <img src="{{ asset('post/all') }}/{{ $livraison->identity }}" alt="Pièce d'identité" class="w-64 h-64 object-cover rounded-md shadow-md">
            </div>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Permis :</p>
            <div class="mt-2">
                <img src="{{ asset('post/all') }}/{{ $livraison->permis }}" alt="Permis de conduire" class="w-64 h-64 object-cover rounded-md shadow-md">
            </div>
        </div>

        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-600">Assurance :</p>
            <div class="mt-2">
                <img src="{{ asset('post/all') }}/{{ $livraison->assurance }}" alt="Assurance" class="w-64 h-64 object-cover rounded-md shadow-md">
            </div>
        </div>
        <div class="flex space-x-4 mt-4">
            @if ($livraison->etat == 'Accepté')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-200 hover:bg-gray-300">Accepté</div>
            @elseif ($livraison->etat == 'Refusé')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-200 hover:bg-gray-300">Refusé</div>
            @else
                <button wire:click="accept" type="button" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">Accepter</button>
                <button wire:click="refuse" type="button" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">Refuser</button>
            @endif
        </div>
    </div>
</div>
