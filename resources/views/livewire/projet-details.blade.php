<div>

    <div class="min-h-screen px-4 py-12 bg-gray-50 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                {{-- Project Header --}}
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $projet->name }}</h1>
                        <span @class([
                            'px-3 py-1 rounded-full text-sm font-medium',
                            'bg-yellow-100 text-yellow-800' => $projet->statut === 'en attente',
                            'bg-green-100 text-green-800' => $projet->statut === 'approuvé',
                            'bg-red-100 text-red-800' => $projet->statut === 'rejeté',
                        ])>
                            {{ ucfirst($projet->statut) }}
                        </span>
                    </div>

                </div>

                <div class="flex  w-full gap-4 p-6 lg:grid-cols-2">
                    {{-- Image Gallery --}}
                    <div class="flex flex-col w-full space-y-6 md:w-1/2 md:h-auto">
                        <!-- Main Image -->
                        <div class="relative max-w-md mx-auto overflow-hidden rounded-lg shadow-lg lg:max-w-lg">
                            <img id="mainImage"
                                class="object-cover w-full transition duration-300 ease-in-out transform hover:scale-105"
                                src="{{ asset($images[0]) }}" alt="Main Product Image" />
                        </div>

                        <!-- Thumbnail Images -->
                        <div class="flex justify-center space-x-4">
                            @foreach ($images as $image)
                                @if ($image)
                                    <!-- Vérifie si l'image existe -->
                                    <img onclick="changeImage('{{ asset($image) }}')"
                                        class="object-cover w-20 h-20 transition-transform duration-200 ease-in-out transform border-2 border-gray-200 rounded-lg cursor-pointer hover:scale-105 hover:border-gray-400"
                                        src="{{ asset($image) }}" alt="Thumbnail">
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Project Details --}}
                    <div class="space-y-6">
                        <div class="grid gap-6">
                            {{-- Amount --}}
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100">
                                    <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Montant</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ number_format($projet->montant, 0, ',', ' ') }} CFA</p>
                                </div>
                            </div>

                            {{-- Financing Type --}}
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full">
                                    <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Type de Financement</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $projet->type_financement }}</p>
                                </div>
                            </div>

                            {{-- User --}}
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full">
                                    <svg class="w-5 h-5 text-purple-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Demandeur</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $projet->demandeur->name }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}


                        <div class="flex items-center justify-center w-full space-x-4">
                            <!-- Bouton Accepter -->
                            
                            <div class="flex items-center justify-center w-full space-x-4">
                                <!-- Bouton Accepter -->
                                <button wire:click="accepterProjet"
                                        wire:loading.attr="disabled"
                                        @if ($projet->statut === 'approuvé') disabled @endif
                                        class="px-6 py-2 text-white rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50
                                            @if ($projet->statut === 'approuvé') bg-green-600 cursor-not-allowed @else bg-green-500 hover:bg-green-600 focus:ring-green-400 @endif">
                                    @if ($projet->statut === 'approuvé')
                                        Accepté
                                    @else
                                        <span wire:loading.remove>Accepter</span>
                                        <svg wire:loading wire:target="accepterProjet" class="w-5 h-5 mx-auto animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    @endif
                                </button>
                            
                                <!-- Bouton Refuser -->
                                <button wire:click="refuserProjet"
                                        wire:loading.attr="disabled"
                                        @if ($projet->statut === 'rejeté') disabled @endif
                                        class="px-6 py-2 text-white rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50
                                            @if ($projet->statut === 'rejeté') bg-red-600 cursor-not-allowed @else bg-red-500 hover:bg-red-600 focus:ring-red-400 @endif">
                                    @if ($projet->statut=== 'rejeté')
                                        Refusé
                                    @else
                                        <span wire:loading.remove>Refuser</span>
                                        <svg wire:loading wire:target="refuserProjet" class="w-5 h-5 mx-auto animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            
                        </div>
                        

                    </div>
                </div>
                <div class="p-6">

                    <p class="mt-2 text-lg text-gray-600">{{ $projet->description }}</p>

                </div>


            </div>
        </div>
    </div>

 
        <script>
            // Image Gallery functionality

            function changeImage(src) {
                document.getElementById('mainImage').src = src;
            }
        </script>
   

</div>
