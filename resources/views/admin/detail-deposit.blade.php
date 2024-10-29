@extends('admin.layout.navside')

@section('title', 'Demande de depot')

@section('content')

  @livewire('detail-deposit', ['id' => $id])


@endsection