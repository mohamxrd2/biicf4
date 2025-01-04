<a wire:navigate href="{{ route('biicf.notif') }}"
    class="flex items-center gap-x-3.5 py-3 px-2.5  @if (request()->route()->getName() == 'biicf.notif') text-purple-600 font-semibold  @endif text-md rounded-lg hover:bg-gray-100 dark:bg-gray-900 dark:text-white">
    <span class="relative inline-block">
        @if (request()->route()->getName() == 'biicf.notif')
            <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-6 h-6">
                <path
                    d="M5.85 3.5a.75.75 0 0 0-1.117-1 9.719 9.719 0 0 0-2.348 4.876.75.75 0 0 0 1.479.248A8.219 8.219 0 0 1 5.85 3.5ZM19.267 2.5a.75.75 0 1 0-1.118 1 8.22 8.22 0 0 1 1.987 4.124.75.75 0 0 0 1.48-.248A9.72 9.72 0 0 0 19.266 2.5Z" />
                <path fill-rule="evenodd"
                    d="M12 2.25A6.75 6.75 0 0 0 5.25 9v.75a8.217 8.217 0 0 1-2.119 5.52.75.75 0 0 0 .298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 1 0 7.48 0 24.583 24.583 0 0 0 4.83-1.244.75.75 0 0 0 .298-1.205 8.217 8.217 0 0 1-2.118-5.52V9A6.75 6.75 0 0 0 12 2.25ZM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 0 0 4.496 0l.002.1a2.25 2.25 0 1 1-4.5 0Z"
                    clip-rule="evenodd" />
            </svg>
        @else
            <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
            </svg>
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
