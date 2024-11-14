@extends('finance.layout.navside')

@section('title', 'Credit')

@section('content')

    @if ($id)
        @livewire('details-credit', ['id' => $id])
    @endif

    @if ($id_projet)
        @livewire('details-credit-projet', ['id' => $id_projet])
    @endif

    @if ($id_details)
        @livewire('details-gagnant', ['id' => $id_details])
    @endif

@endsection
