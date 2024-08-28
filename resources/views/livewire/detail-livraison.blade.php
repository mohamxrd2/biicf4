<!-- resources/views/livewire/detail-livraison.blade.php -->

<div class="container mx-auto py-8" wire:poll.15000ms>
    <h1 class="text-4xl font-extrabold mb-10 text-center text-gray-900">Détails de la Demande</h1>

    <div class="bg-white shadow-lg rounded-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Nom & prénom :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->user->name }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Véhicule :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->vehicle }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Expérience :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->experience }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">État :</h2>
                <div class="mt-2">
                    @if ($livraison->etat == 'En cours')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-yellow-800 bg-yellow-200">{{ $livraison->etat }}</span>
                    @elseif ($livraison->etat == 'Accepté')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-green-800 bg-green-200">{{ $livraison->etat }}</span>
                    @elseif ($livraison->etat == 'Refusé')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-red-800 bg-red-200">{{ $livraison->etat }}</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-gray-800 bg-gray-200">{{ $livraison->etat }}</span>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Matricule :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->matricule }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Disponibilité :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->availability }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Zone :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->zone }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Commentaires :</h2>
                <p class="text-2xl font-bold text-gray-900">{{ $livraison->comments }}</p>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Photo :</h2>
                <img src="{{ asset('post/all') }}/{{ $livraison->identity }}" alt="Pièce d'identité" class="w-full h-48 object-cover rounded-lg shadow-md">
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Permis :</h2>
                <img src="{{ asset('post/all') }}/{{ $livraison->permis }}" alt="Permis de conduire" class="w-full h-48 object-cover rounded-lg shadow-md">
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Assurance :</h2>
                <img src="{{ asset('post/all') }}/{{ $livraison->assurance }}" alt="Assurance" class="w-full h-48 object-cover rounded-lg shadow-md">
            </div>
        </div>

        <div class="flex justify-center space-x-4 mt-10">
            @if ($livraison->etat == 'Accepté')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-300 cursor-not-allowed">Accepté</div>
            @elseif ($livraison->etat == 'Refusé')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-300 cursor-not-allowed">Refusé</div>
            @else
                <button wire:click="accept" type="button" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-lg font-semibold text-white bg-green-600 hover:bg-green-700">Accepter</button>
                <button wire:click="refuse" type="button" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-lg font-semibold text-white bg-red-600 hover:bg-red-700">Refuser</button>
            @endif
        </div>
    </div>
</div>

