@extends('finance.layout.navside')

@section('title', 'Credit')

@section('content')

  @livewire('details-credit', ['id' => $id])

@endsection
