<div wire:poll.100ms>
    <div class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">
        @if ($commentCount == 0)
            <div class="w-full h-full flex items-center justify-center">
                <p class="text-gray-800">Aucune offre n'a été soumise</p>
            </div>
        @else
            @foreach ($comments as $comment)
                <div class="flex items-center gap-3 relative">
                    <img src="{{ asset($comment->user->photo) }}" alt="" class="w-8 h-8 mt-1 rounded-full overflow-hidden object-cover">
                    <div class="flex-1">
                        <p class="text-base text-black font-medium inline-block dark:text-white">
                            {{ $comment->user->name }}
                        </p>
                        <p class="text-sm mt-0.5">
                            {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
