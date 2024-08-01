<input type="{{ $type }}"
    class="py-3 px-4 mb-2 block w-full lg:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
    name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name) }}">
@if ($errors->has($name))
    <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
        @foreach ($errors->get($name) as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
