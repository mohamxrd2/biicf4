@extends('admin.layout.navside')

@section('title', 'Dashboard')

@section('content')

    @livewire('detail-psap-component', ['id' => $id])

@endsection