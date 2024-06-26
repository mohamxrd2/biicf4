@extends('admin.layout.navside')

@section('title', 'Dashboard')

@section('content')
    <!-- Dans votre fichier de vue de tableau de bord -->

    @auth('admin')
        @if (auth()->guard('admin')->user()->admin_type == 'agent')
            <!-- Éléments spécifiques à l'agent -->
            <livewire:dashboard-agent :lazy="true" />
        @else
            <!-- Éléments spécifiques à l'administrateur général -->
            <livewire:dashboard-admin :lazy="true" />
        @endif
    @endauth








@endsection
