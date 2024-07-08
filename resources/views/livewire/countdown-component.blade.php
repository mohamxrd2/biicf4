<!-- resources/views/livewire/countdown-component.blade.php -->

<div>
    @if (!$countdownStarted)
        <button wire:click="startCountdown" class="bg-blue-500 text-white px-4 py-2 rounded">
            Lancer le compte à rebours
        </button>
    @else
        <div>
            <p>Le compte à rebours a commencé !</p>
            <p>Temps restant : <span id="time-remaining">{{ $timeRemaining }}</span></p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:load', function () {
        @if($countdownStarted)
            setInterval(function() {
                @this.call('updateTimeRemaining');
            }, 1000);
        @endif
    });
</script>
