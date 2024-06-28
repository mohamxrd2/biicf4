@extends('admin.layout.navside')

@section('title', 'services')

@section('content')
    @auth('admin')
        @if (Auth::guard('admin')->user()->admin_type == 'agent')
            @include('admin.produit_services.services_agent')
        @else
            <div class=" relative overflow-x-auto  sm:rounded-lg">
                

                @if (session('success'))
                    <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <livewire:publication-services lazy/>
            </div>
        @endif
    @endauth

@endsection
