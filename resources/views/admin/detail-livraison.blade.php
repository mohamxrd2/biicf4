@extends('admin.layout.navside')

@section('title', 'Dashboard')

@section('content')

    @livewire('detail-livraison', ['id' => $id])

@endsection
