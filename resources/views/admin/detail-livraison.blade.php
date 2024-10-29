@extends('admin.layout.navside')

@section('title', 'Demande Livraison')

@section('content')

    @livewire('detail-livraison', ['id' => $id])

@endsection
