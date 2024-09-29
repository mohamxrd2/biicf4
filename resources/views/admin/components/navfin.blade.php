<div class="text-center">
    <a href="{{ $route }}"
        class=" @if (request()->route()->getName() == $routeSelf) text-purple-600 bg-gray-100 @endif   text-gray-600 hover:bg-gray-100  p-2 rounded-md inline-block">
        <!-- Dashboard Icon -->

        {!! $iconSvg !!}
    </a>
</div>
