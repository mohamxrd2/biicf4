

<li>
    <a href="{{ $route }}" 
        class="flex items-center gap-3 p-3 rounded-md transition-colors duration-200 
            {{ request()->route()->getName() ==  $routeSelf  
                ? 'text-purple-600 bg-gray-100' 
                : 'text-gray-600 hover:bg-gray-100' }}">
        {!! $iconSvg !!}
        <span class="text-sm font-medium">{{ $title }}</span>
    </a>
</li>