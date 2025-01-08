@props(['nombre'])

<div class="inline-flex items-center justify-center px-6 py-3 bg-blue-100 rounded-full">
    <p class="text-lg text-blue-800 dark:text-gray-300">
        Clients potentiels est
        <span class="font-bold text-blue-600 dark:text-blue-400 text-lg ml-1">
            ({{ $nombre }})
        </span>
    </p>
</div>
