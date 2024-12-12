@props(['active' => false, 'label', 'icon' => null])

<button 
    {{ $attributes }}
    class="group relative px-6 py-3 text-sm font-medium rounded-xl transition-all duration-200 
    {{ $active 
        ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/30' 
        : 'bg-white text-gray-700 hover:bg-gray-50 hover:shadow-md border border-gray-200' }}">
    <div class="flex items-center gap-2">
        @if($icon)
            <i class="{{ $icon }} {{ $active ? 'text-white' : 'text-blue-500 group-hover:text-blue-600' }}"></i>
        @endif
        <span>{{ $label }}</span>
    </div>
    @if($active)
        <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2">
            <div class="w-2 h-2 bg-blue-500 rotate-45"></div>
        </div>
    @endif
</button>