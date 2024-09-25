<input type="{{ $type }}"
    class="py-3 px-4 mb-2 block w-full lg:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
    name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name) }}">
@if ($errors->has($name))
    @foreach ($errors->get($name) as $error)
        <p class=" text-red-500 ">{{ $error }}</p>
    @endforeach
@endif
