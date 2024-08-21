@php
    $sousregions = [
        'Afrique du Nord',
        'Afrique de l\'Ouest',
        'Afrique Centrale',
        'Afrique de l\'Est',
        'Afrique Australe',
        'Amérique du Nord',
        'Amérique Centrale ',
        'Amérique du Sud  ',
        'Caraïbes',
        'Asie de l\'Est',
        'Asie du Sud',
        'Asie du Sud-Est',
        'Asie Centrale',
        'Asie de l\'Ouest ',
        'Europe de l\'Est',
        'Europe de l\'Ouest',
        'Europe du Nord',
        'Europe du Sud',
        'Australie et Nouvelle-Zélande',
        'Mélanésie ',
        'Polynésie ',
        'Micronésie ',
    ];
@endphp

<select name="{{ $name }}" id="{{ $name }}"
    class="py-3 mb-2 px-4 pe-9 block w-full lg:w-1/2 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
    <option value="" disabled selected>{{ $title }}</option> <!-- Option par défaut -->
    @foreach ($sousregions as $sousregion)
        <option value="{{ $sousregion }}" {{ old($name) == $sousregion ? 'selected' : '' }}>
            {{ $sousregion }}
        </option>
    @endforeach
</select>
