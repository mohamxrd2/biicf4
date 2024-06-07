
<div class="flex flex-col bg-{{ $bgcolor }} border shadow-sm rounded-xl">
    <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg">
            {!! $svgPath !!}
        </div>
        <div class="grow">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide @if($bgcolor == 'black') text-white @else text-gray-500 @endif">{{ $title }}</p>
                @if (!empty($tooltip))
                <div class="hs-tooltip">
                    <div class="hs-tooltip-toggle">
                        <svg class="flex-shrink-0 size-4 text-gray-500"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                            <path d="M12 17h.01" />
                        </svg>
                        <span
                            class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm"
                            role="tooltip">
                            {{ $tooltip }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
            <div class="mt-3 flex flex-col items-start gap-2">
                <h3 class="text-md sm:text-2xl font-medium @if($bgcolor == 'black') text-white @else text-black @endif">{{ $amount }}</h3>
                <span class="flex items-center gap-1 py-0.5 px-2 rounded-full bg-green-100 text-green-900">
                    <svg class="w-4 h-4 self-center" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                        <polyline points="16 7 22 7 22 13" />
                    </svg>
                    <span class="text-xs font-medium">{{ $chart }}%</span>
                </span>
            </div>
        </div>
    </div>
</div>
