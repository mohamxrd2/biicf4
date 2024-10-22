@extends('finance.layout.navside')

@section('title', 'Acceuil')

@section('content')


    @if ($id)
        @livewire('detail-projet', ['id' => $id])
    @endif

    @if ($id_projet)
        @livewire('detail-projet-negocie', ['id' => $id_projet])
    @endif

@endsection
