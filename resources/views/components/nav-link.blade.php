@props(['route', 'isActive', 'label', 'activeIcon', 'inactiveIcon'])

<a wire:navigate href="{{ $route }}"
    class="flex flex-col items-center {{ $isActive ? 'text-purple-600 font-semibold' : 'text-gray-500' }} dark:text-gray-400">
    @if ($isActive)
        {!! $activeIcon !!}
    @else
        {!! $inactiveIcon !!}
    @endif
    <span class="text-xs">{{ $label }}</span>
</a>
