<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Détails de la Livraison</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <p><strong>Nom & prénom :</strong> {{ $livraison->user->name }}</p>
        <p><strong>Véhicule :</strong> {{ $livraison->vehicle }}</p>
        <p><strong>Expérience :</strong> {{ $livraison->experience }}</p>
        <p><strong>État :</strong>
            @if ($livraison->etat == 'En cours')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100">{{ $livraison->etat }}</span>
            @elseif ($livraison->etat == 'Accepté')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100">{{ $livraison->etat }}</span>
            @elseif ($livraison->etat == 'Refusé')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100">{{ $livraison->etat }}</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-gray-800 bg-gray-100">{{ $livraison->etat }}</span>
            @endif
        </p>
        <!-- Ajoutez d'autres détails ici -->
    </div>
</div>
