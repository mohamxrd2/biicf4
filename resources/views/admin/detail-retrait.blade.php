@extends('admin.layout.navside')

@section('title', 'Demande de retrait')

@section('content')

  @livewire('retrait-show', ['id' => $id])


@endsection