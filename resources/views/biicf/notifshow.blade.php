@extends('biicf.layout.navside')

@section('title', 'Details notification')



@section('content')


    <div class=" mx-auto">

        {{-- <livewire:notification-show :id="$id"/> --}}
        @livewire('notification-show', ['id' => $id])

    </div>




@endsection
