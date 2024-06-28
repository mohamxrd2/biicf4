@extends('admin.layout.navside')

@section('title', 'consommation produits')

@section('content')

    <div class=" relative overflow-x-auto  sm:rounded-lg">

        <livewire:consommation-list lazy />
    </div>



@endsection
