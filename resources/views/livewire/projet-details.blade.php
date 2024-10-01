

<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold">{{ $projet->name }}</h1>
        
        <!-- Carousel d'images -->
        @if($projet->photo1 || $projet->photo2 || $projet->photo3 || $projet->photo4 || $projet->photo5)
        <div class="swiper-container mb-6">
            <div class="swiper-wrapper">
                @if($projet->photo1)
                    <div class="swiper-slide">
                        <img src="{{ asset('post/'. $projet->photo1) }}" alt="Photo 1" class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif
               
            </div>
    
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    @else
        <p>Aucune photo disponible pour ce projet.</p>
    @endif
    
    

        <p class="mt-2 text-gray-600">Montant : {{ number_format($projet->montant, 0, ',', ' ') }} CFA</p>
        <p class="mt-2 text-gray-600">Type de Financement : {{ $projet->type_financement }}</p>
        <p class="mt-2 text-gray-600">Utilisateur : {{ $projet->demandeur->name }}</p>
        <p class="mt-2 text-gray-600">Statut : {{ $projet->statut }}</p>
        <p class="mt-2 text-gray-600">Description : {{ $projet->description }}</p>

        <a href="{{ route('admin.projetlist') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
            Retour à la liste des projets
        </a>

        <div class="flex justify-center space-x-4 mt-10">
            @if ($projet->statut == 'approuvé')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-300 cursor-not-allowed">Accepté</div>
            @elseif ($projet->statut == 'rejeté')
                <div class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-gray-300 cursor-not-allowed">Refusé</div>
            @else
                <button wire:click="accepterProjet" type="button" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-lg font-semibold text-white bg-green-600 hover:bg-green-700">Accepter</button>
                <button wire:click="refuserProjet" type="button" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-lg font-semibold text-white bg-red-600 hover:bg-red-700">Refuser</button>
            @endif
        </div>
    </div>
</div>



