@props(['photos'])

<div class="grid col-span-2 lg:grid-cols-4 sm:grid-cols-4 mb-6 gap-9">
    @foreach ($photos as $photo)
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ ucfirst($photo) }} <span class="ml-1 text-red-500">*</span>
            </label>
            <input type="file" wire:model="{{ $photo }}" :disabled="locked"
                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            @error($photo)
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror

            <!-- Prévisualisation Image -->
            @if ($this->getPropertyValue($photo))
                <div class="mt-2">
                    @if (is_string($this->getPropertyValue($photo)))
                        <img src="{{ asset('post/all/' . $this->getPropertyValue($photo)) }}"
                            class="w-full h-32 object-cover rounded-md">
                    @else
                        <img src="{{ $this->getPropertyValue($photo)->temporaryUrl() }}"
                            class="w-full h-32 object-cover rounded-md">
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</div>
