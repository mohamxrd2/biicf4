@props(['label', 'value', 'description', 'cost', 'selectedOption', 'icon' => null])

<input type="radio" name="selectedOption" value="{{ $value }}"
    @click="selectedOption = '{{ $value }}'"
    id="{{ $value }}Option"
    class="hidden"
    wire:model.live="selectedOption">

<label for="{{ $value }}Option"
    class="flex items-center p-4 rounded-lg border-2 transition-all w-full {{ $value === 'Delivery' ? 'mb-4' : '' }}"
    :class="{
        'border-blue-500 bg-blue-50': selectedOption === '{{ $value }}',
        'border-gray-200 hover:border-blue-200': selectedOption !== '{{ $value }}'
    }">
    <div class="p-3 rounded-full mr-4"
        :class="{
            'bg-blue-500 text-white': selectedOption === '{{ $value }}',
            'bg-gray-100 text-gray-600': selectedOption !== '{{ $value }}'
        }">
        @if($value === 'Delivery')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="1" y="3" width="15" height="13" rx="2" ry="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></rect>
                <path d="M16 8h5l3 5v3h-8V8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                <circle cx="5.5" cy="18.5" r="2.5" stroke-width="2"></circle>
                <circle cx="18.5" cy="18.5" r="2.5" stroke-width="2"></circle>
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
        @endif
    </div>
    <div class="flex-1 text-left">
        <h3 class="font-semibold text-gray-800">{{ $label }}</h3>
        <p class="text-sm text-gray-500">{{ $description }}</p>
    </div>
    <div class="text-right">
        <span class="font-semibold text-blue-800">{{ $cost }}</span>
    </div>
</label>
