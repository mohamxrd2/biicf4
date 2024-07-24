<div>
    <span class="absolute -top-2 -right-1.5 w-4 h-4 rounded-full bg-red-500 flex items-center justify-center text-xs font-bold text-white @if ($unreadCount == 0) hidden @endif">
        @if ($unreadCount > 9)
            9+
        @else
            {{ $unreadCount }}
        @endif
    </span>
</div>
