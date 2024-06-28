@extends('admin.layout.navside')

@section('title', 'Agent')

@section('content')

    <div class=" relative overflow-x-auto  sm:rounded-lg">
        <div
            class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
            <div>

            </div>


            <livewire:add-agents />




        </div>

    </div>

    @if (session('success'))
        <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <livewire:liste-agents lazy />


@endsection
