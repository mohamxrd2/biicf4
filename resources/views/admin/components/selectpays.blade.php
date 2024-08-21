<select name="{{ $name }}" id="{{ $name }}"
    class="py-3 mb-2 px-4 pe-9 block w-full lg:w-1/2 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
    <option value="" disabled selected>{{ $title }}</option> <!-- Option par défaut -->
    @foreach ($countries as $country)
        <option value="{{ $country }}" {{ old($name) == $country ? 'selected' : '' }}>
            {{ $country }}
        </option>
    @endforeach
</select>
