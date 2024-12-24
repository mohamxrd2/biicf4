<div>
    <div class="text-center">
        <h2>Compte à rebours: {{ gmdate('i:s', $timeRemaining) }}</h2>

        @if (!$isRunning)
            <button wire:click="startCountdown" class="px-4 py-2 bg-blue-500 text-white rounded">
                Démarrer
            </button>
        @endif
    </div>
</div>
