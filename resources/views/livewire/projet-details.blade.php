

<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold">{{ $projet->name }}</h1>
        
        <!-- Carousel d'images -->
        
        <div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
            <!-- Main Image -->
            <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                <img id="mainImage"
                    class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                    src="{{ asset($images[0]) }}" alt="Main Product Image" />
            </div>
        
            <!-- Thumbnail Images -->
            <div class="flex justify-center space-x-4">
                @foreach ($images as $image)
                    @if ($image)
                        <!-- Vérifie si l'image existe -->
                        <img onclick="changeImage('{{ asset($image) }}')"
                            class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                            src="{{ asset($image) }}" alt="Thumbnail">
                    @endif
                @endforeach
            </div>
        </div>
        
        <p class="mt-2 text-gray-600">Montant : {{ number_format($projet->montant, 0, ',', ' ') }} CFA</p>
        <p class="mt-2 text-gray-600">Type de Financement : {{ $projet->type_financement }}</p>
        <p class="mt-2 text-gray-600">Utilisateur : {{ $projet->demandeur->name }}</p>
        <p class="mt-2 text-gray-600">Statut : {{ $projet->statut }}</p>
        <p class="mt-2 text-gray-600">Description : {{ $projet->description }}</p>

       
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



