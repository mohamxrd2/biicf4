


<li>
    <a class="flex items-center gap-x-3.5 py-3 px-2.5 @if (request()->route()->getName() == $routeSelf)   text-purple-600 font-semibold @endif text-md  rounded-lg hover:bg-gray-100 dark:bg-gray-900 dark:text-white"
        href="{{ $route }}">
        
        @if (request()->route()->getName() == $routeSelf)

        {!! $iconSvg2 !!}

        @else
        {!! $iconSvg !!}

        @endif

        {{ $title }}
    </a>
</li>