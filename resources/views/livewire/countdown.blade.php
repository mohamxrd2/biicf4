<div>
    <div class="flex items-center space-x-2">
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            class="bg-gradient-to-r from-red-500 to-red-600  text-white font-bold px-6 py-3 rounded-xl shadow-lg">
            <div class="flex flex-col items-center">
                <span class="text-xs uppercase tracking-wide mb-1">
                    @if ($achatdirect->count)
                        Temps écoulé
                    @else
                        Temps restant
                    @endif
                </span>
                <h2 class="text-2xl font-bold tabular-nums">
                    @if ($achatdirect->count)
                        00:00
                    @else
                        {{ sprintf('%02d:%02d', floor($timeRemaining / 60), $timeRemaining % 60) }}
                    @endif
                </h2>
            </div>

        </div>
    </div>
</div>
