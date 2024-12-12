@props(['href' => null])

<td {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-700']) }}>
    @if($href)
        <a href="{{ $href }}" class="hover:text-blue-600 font-medium transition-colors duration-150">
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>