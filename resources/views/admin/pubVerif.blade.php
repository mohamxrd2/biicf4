@extends('admin.layout.navside')

@section('title', 'affichage de produits')

@section('content')


    @livewire('produit-show', ['id' => $id])



@endsection
