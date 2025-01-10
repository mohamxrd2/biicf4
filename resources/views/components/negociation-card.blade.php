@props(['lastActivity', 'nombreParticipants', 'isNegociationActive', 'commentCount'])

<div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-6">
    <!-- Barre supérieure avec statut -->
    <div class="border-b border-white border-opacity-20">
        <div class="container mx-auto px-6 py-2">
            <div class="flex justify-between items-center text-white">
                <span class="text-sm flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    Dernière activité:
                    @if ($lastActivity)
                        {{ \Carbon\Carbon::parse($lastActivity)->diffForHumans() }}
                    @else
                        Aucune activité
                    @endif
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coc" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="text-white w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>

                <!-- Tooltip -->
                <div id="tooltip-coc" role="tooltip"
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    Le prix le plus bas remportera la négociation. <br />
                    Le gagnant recevra une notification.
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <i class="fas fa-users mr-2"></i>
                        <span class="text-sm">{{ $nombreParticipants ?? '0' }} participants</span>
                    </span>
                    <div class="relative">
                        @if ($isNegociationActive)
                            <button
                                class="flex items-center space-x-1 bg-white bg-opacity-20 rounded-full px-3 py-1 hover:bg-opacity-30 transition-colors">
                                <span class="animate-pulse h-2 w-2 bg-green-400 rounded-full"></span>
                                <span class="text-sm">Négociation en cours</span>
                            </button>
                        @else
                            <button class="flex items-center space-x-1 bg-white bg-opacity-20 rounded-full px-3 py-1">
                                <span class="h-2 w-2 bg-red-400 rounded-full"></span>
                                <span class="text-sm">Négociation terminée</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="relative p-6">
        <div class="relative">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <!-- Titre et informations principales -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white p-2 rounded-lg shadow-md">
                            <i class="fas fa-truck text-blue-600 text-xl"></i>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-white tracking-wide">
                            {{ $name }}
                        </h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <span
                            class="flex items-center bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm hover:bg-opacity-30 transition-colors cursor-pointer">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Date prévue: 25 Mars 2024
                        </span>
                        <span
                            class="flex items-center bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm hover:bg-opacity-30 transition-colors cursor-pointer">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Distance: 250 km
                        </span>
                    </div>
                </div>

                <!-- Statut et actions -->
                <div class="flex items-center space-x-4">
                    <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
                        <div class="text-center">
                            <span class="text-gray-600 text-sm block">Offres reçues</span>
                            @if ($commentCount > 0)
                                <span class="text-2xl font-bold text-blue-600">{{ $commentCount }}</span>
                            @else
                                <div class="flex items-center justify-center space-x-2 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Aucune offre</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
