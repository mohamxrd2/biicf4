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
                            'bg-yellow-100 text-yellow-800' => $projet->statut === 'en_attente',
                            'bg-green-100 text-green-800' => $projet->statut === 'approuvé',
                            'bg-red-100 text-red-800' => $projet->statut === 'rejeté',
                        ])>
                            {{ ucfirst($projet->statut) }}
                        </span>
                    </div>
                    <p class="mt-2 text-lg text-gray-600">{{ $projet->description }}</p>
                </div>

                <div class="grid grid-cols-1 gap-8 p-6 lg:grid-cols-2">
                    {{-- Image Gallery --}}
                    <div class="space-y-4">
                        {{-- Main Image --}}
                        <div class="relative overflow-hidden bg-gray-100 rounded-lg aspect-video">
                            <img
                                id="mainImage"
                                src="{{ asset($images[0]) }}"
                                alt="Main Project Image"
                                class="object-cover w-full h-full"
                            >
                        </div>

                        {{-- Thumbnails --}}
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($images as $index => $image)
                                <button
                                    data-thumbnail
                                    data-image="{{ asset($image) }}"
                                    class="relative overflow-hidden bg-gray-100 rounded-lg aspect-square group"
                                >
                                    <img
                                        src="{{ asset($image) }}"
                                        alt="Project Image {{ $index + 1 }}"
                                        class="object-cover w-full h-full transition duration-300 group-hover:scale-110"
                                    >
                                    <div class="absolute inset-0 transition duration-300 bg-black/0 group-hover:bg-black/10"></div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Project Details --}}
                    <div class="space-y-6">
                        <div class="grid gap-6">
                            {{-- Amount --}}
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100">
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
                                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full">
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
                                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full">
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
                            <div class="pt-6 border-t border-gray-100">
                                <div class="flex space-x-4">
                                    <form action="{{ route('projets.accepter', $projet) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-3 text-base font-medium text-white transition-colors duration-200 border border-transparent rounded-md bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Accepter
                                        </button>
                                    </form>

                                    <form action="{{ route('projets.refuser', $projet) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-3 text-base font-medium text-white transition-colors duration-200 bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
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



