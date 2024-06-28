@extends('admin.layout.navside')

@section('title', 'consommation')

@section('content')
    <div class=" relative overflow-x-auto  sm:rounded-lg">

        <livewire:consommation-list-serv lazy />
        
    </div>


    <script src="{{ asset('js/search.js') }}"></script>




@endsection
