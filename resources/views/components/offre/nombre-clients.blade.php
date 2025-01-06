@props(['nombre'])

<div class="inline-flex items-center justify-center p-4 bg-blue-50 rounded-lg dark:bg-blue-900/50 mb-4 w-full">
    <p class="text-gray-700 dark:text-gray-300">
        Le nombre de clients potentiels est
        <span class="font-bold text-blue-600 dark:text-blue-400 text-lg ml-1">
            ({{ $nombre }})
        </span>
    </p>
</div>
