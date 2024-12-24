<div>
    <div class="text-center">
        @if($timeRemaining > 0)
            <h2 class="text-xl font-bold">
                {{ sprintf('%02d:%02d', floor($timeRemaining / 60), $timeRemaining % 60) }}
            </h2>
        @endif

        @if (!$isRunning)
            <button wire:click="startCountdown" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Démarrer
            </button>
        @endif
    </div>
</div>
