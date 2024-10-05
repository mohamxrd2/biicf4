@extends('finance.layout.navside')

@section('title', 'Acceuil')

@section('content')

  @livewire('detail-projet', ['id' => $id])

@endsection