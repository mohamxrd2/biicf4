@extends('admin.layout.navside')

@section('title', 'Liste des projets')

@section('content')

  @livewire('projet-details', ['id' => $id])

@endsection