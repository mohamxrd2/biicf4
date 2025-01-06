<div class="p-6 bg-blue-50 hover:bg-gray-50 transition-colors">
    <div class="flex items-start">

        <!-- Icône Notification -->
        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">


            {!! $svg !!}
        </div>

        <div class="ml-4 flex-1">
            <a wire:navigate href="{{ route('notification.show', ['id' => $notification->id]) }}">
                <!-- Contenu Notification -->
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">{{ $title ?? 'Titre non spécifié' }}</p>
                    <p class="text-sm text-gray-500">{{ $time ?? 'Temps inconnu' }}</p>
                </div>
                <p class="mt-1 text-sm text-gray-600">{{ $description ?? 'Aucune description disponible.' }}</p>
                @if ($orderId ?? false)
                    <p class="mt-1 text-xs text-gray-500">Commande #{{ $orderId }}</p>
                @endif
                @if ($amount ?? false)
                    <p class="mt-1 text-xs font-medium text-gray-700">Montant: {{ $amount }}</p>
                @endif
            </a>
        </div>

        <!-- Actions -->
        <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
            @if ($markAsRead ?? false)
                <button class="p-1 rounded-full hover:bg-gray-100 transition-colors" title="Marquer comme lu">
                    <svg class="w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            @endif
            @if ($delete ?? false)
                <button class="p-1 rounded-full hover:bg-gray-100 transition-colors" title="Supprimer">
                    <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
