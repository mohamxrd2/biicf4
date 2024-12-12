@props(['status'])

@php
    $styles = [
        'En cours' => [
            'bg' => 'bg-gradient-to-r from-yellow-500/10 to-yellow-600/10',
            'text' => 'text-yellow-700',
            'icon' => 'fas fa-clock',
            'ring' => 'ring-yellow-500/30'
        ],
        'Accepté' => [
            'bg' => 'bg-gradient-to-r from-green-500/10 to-green-600/10',
            'text' => 'text-green-700',
            'icon' => 'fas fa-check',
            'ring' => 'ring-green-500/30'
        ],
        'Refusé' => [
            'bg' => 'bg-gradient-to-r from-red-500/10 to-red-600/10',
            'text' => 'text-red-700',
            'icon' => 'fas fa-times',
            'ring' => 'ring-red-500/30'
        ],
        'default' => [
            'bg' => 'bg-gradient-to-r from-gray-500/10 to-gray-600/10',
            'text' => 'text-gray-700',
            'icon' => 'fas fa-circle',
            'ring' => 'ring-gray-500/30'
        ]
    ];
    
    $style = $styles[$status] ?? $styles['default'];
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium leading-none {{ $style['bg'] }} {{ $style['text'] }} ring-1 {{ $style['ring'] }}">
    <i class="{{ $style['icon'] }} text-xs"></i>
    {{ $status }}
</span>