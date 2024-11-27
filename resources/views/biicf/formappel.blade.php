@extends('biicf.layout.navside')

@section('title', 'Formulaire de l\'Appel d\'offre')

@section('content')


    <livewire:appaeloffre
    :wallet="$wallet"
    :lowestPricedProduct="$lowestPricedProduct"
    :distinctCondProds="$distinctCondProds"
    :type="$type"
    :prodUsers="$prodUsers"
    :distinctquatiteMax="$distinctquatiteMax"
    :distinctquatiteMin="$distinctquatiteMin"
    :name="$name"
    :reference="$reference"
    :distinctSpecifications="$distinctSpecifications"
    :appliedZoneValue="$appliedZoneValue"
     />




@endsection
