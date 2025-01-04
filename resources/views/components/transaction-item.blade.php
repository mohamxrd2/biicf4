<div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-100 rounded-xl">
    <div class="flex items-center">
        <!-- Icône de transaction -->
        <div class="flex-shrink-0">
            @if($transaction->type == 'Réception')
                <div class="p-2 text-green-500 bg-green-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            @else
                <div class="p-2 text-red-500 bg-red-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Détails de la transaction -->
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-900">{{ $transaction->type }}</p>
            <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Montant -->
    <div class="text-right">
        <p class="text-sm font-medium {{ $transaction->receiver_user_id == $userId ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
        </p>
    </div>
</div>
