@extends('admin.layout.navside')

@section('title', 'Clients')

@section('content')

    @auth('admin')
        @if (Auth::guard('admin')->user()->admin_type == 'agent')
            {{-- @include('admin.clients.client_agent') --}}
        @else

            <livewire:liste-clients />
        @endif
    @endauth
@endsection
