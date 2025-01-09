@if ($title == 'Notifications')
    <a wire:navigate
        class="flex items-center gap-x-3.5 py-3 px-2.5 @if (request()->route()->getName() == $routeSelf) text-purple-600 font-semibold @endif text-md rounded-lg hover:bg-gray-100 dark:bg-gray-900 dark:text-white"
        href="{{ $route }}">
        <span class="relative inline-block">
            @if (request()->route()->getName() == $routeSelf)
                {!! $iconSvg2 !!}
            @else
                {!! $iconSvg !!}
            @endif

            <span
                class="absolute -top-2 -right-1.5 w-4 h-4 rounded-full bg-red-500 flex items-center justify-center text-xs font-bold text-white @if ($unreadCount == 0) hidden @endif">
                @if ($unreadCount > 9)
                    9+
                @else
                    {{ $unreadCount }}
                @endif
            </span>
        </span>

        <span class="nav-title"> Notifications</span>

    </a>
@else
    <a wire:navigate
        class="flex items-center gap-x-3.5 py-3 px-2.5 @if (request()->route()->getName() == $routeSelf) text-purple-600 font-semibold @endif text-md rounded-lg hover:bg-gray-100 dark:bg-gray-900 dark:text-white"
        href="{{ $route }}">

        @if (request()->route()->getName() == $routeSelf)
            {!! $iconSvg2 !!}
        @else
            {!! $iconSvg !!}
        @endif

        <!-- Title, toggled hidden -->
        <span class="nav-title">{{ $title }}</span>
    </a>

@endif
