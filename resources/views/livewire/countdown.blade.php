<div>
    <div class="flex items-center space-x-2">
        @foreach ($countdowns as $code_unique => $countdown)
            <div wire:key="countdown-{{ $code_unique }}" x-data="{
                timeRemaining: {{ $countdown['timeRemaining'] }},
                code_unique: '{{ $code_unique }}',
                formatTime(seconds) {
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                    return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
                }
            }" x-init="Echo.channel(`countdown.${code_unique}`)
                .listen('CountdownTick', (e) => {
                    console.log('Received tick:', e);
                    if (e.code_unique === code_unique) {
                        timeRemaining = e.time;
                        $wire.$set('countdowns.' + code_unique + '.timeRemaining', e.time);
                    }
                });"
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg">

                <div class="flex flex-col items-center">
                    <span class="text-xs uppercase tracking-wide mb-1">
                        <span x-show="timeRemaining > 0">Temps restant</span>
                        <span x-show="timeRemaining <= 0">Temps écoulé</span>
                    </span>
                    <h2 class="text-2xl font-bold tabular-nums" x-text="formatTime(timeRemaining)"></h2>
                </div>
            </div>
        @endforeach
        {{ $message }}

    </div>
</div>
