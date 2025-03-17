@extends('admin.layout.navside')

@section('title', 'Produits')


@section('content')

    <div class=" relative overflow-x-auto  sm:rounded-lg">

        <livewire:publication-produits lazy />
    </div>

@endsection
