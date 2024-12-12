@props(['headers'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-200/60">
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        @foreach($headers as $header)
                            <x-table.header :label="$header" />
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>
