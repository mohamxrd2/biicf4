<!-- Composant Input -->
@props(['label', 'name', 'type' => 'text', 'errors'])
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
        required>
    @error($name)
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
    <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
</div>
