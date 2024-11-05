<div>

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                {{-- Project Header --}}
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $projet->name }}</h1>
                        <span @class([
                            'px-3 py-1 rounded-full text-sm font-medium',
                            'bg-yellow-100 text-yellow-800' => $projet->statut === 'en_attente',
                            'bg-green-100 text-green-800' => $projet->statut === 'approuvé',
                            'bg-red-100 text-red-800' => $projet->statut === 'rejeté',
                        ])>
                            {{ ucfirst($projet->statut) }}
                        </span>
                    </div>
                    <p class="mt-2 text-lg text-gray-600">{{ $projet->description }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
                    {{-- Image Gallery --}}
                    <div class="space-y-4">
                        {{-- Main Image --}}
                        <div class="relative aspect-video rounded-lg overflow-hidden bg-gray-100">
                            <img
                                id="mainImage"
                                src="{{ asset($images[0]) }}"
                                alt="Main Project Image"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        {{-- Thumbnails --}}
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($images as $index => $image)
                                <button
                                    data-thumbnail
                                    data-image="{{ asset($image) }}"
                                    class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group"
                                >
                                    <img
                                        src="{{ asset($image) }}"
                                        alt="Project Image {{ $index + 1 }}"
                                        class="w-full h-full object-cover transition duration-300 group-hover:scale-110"
                                    >
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition duration-300"></div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Project Details --}}
                    <div class="space-y-6">
                        <div class="grid gap-6">
                            {{-- Amount --}}
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Montant</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($projet->montant, 0, ',', ' ') }} CFA</p>
                                </div>
                            </div>

                            {{-- Financing Type --}}
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Type de Financement</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $projet->type_financement }}</p>
                                </div>
                            </div>

                            {{-- User --}}
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Demandeur</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $projet->demandeur->name }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        @if($projet->statut === 'en_attente')
                            <div class="border-t border-gray-100 pt-6">
                                <div class="flex space-x-4">
                                    <form action="{{ route('projets.accepter', $projet) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Accepter
                                        </button>
                                    </form>

                                    <form action="{{ route('projets.refuser', $projet) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Refuser
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Image Gallery functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('[data-thumbnail]');

            if (mainImage && thumbnails.length) {
                thumbnails.forEach(thumb => {
                    thumb.addEventListener('click', function() {
                        thumbnails.forEach(t => t.classList.remove('ring-2', 'ring-blue-500'));
                        this.classList.add('ring-2', 'ring-blue-500');

                        const newImageSrc = this.getAttribute('data-image');
                        if (newImageSrc) {
                            mainImage.src = newImageSrc;
                        }
                    });
                });
            } else {
                console.warn("Main image or thumbnails are not found in the DOM.");
            }
        });
    </script>
    @endpush

</div>



