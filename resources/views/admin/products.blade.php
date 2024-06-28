@extends('admin.layout.navside')

@section('title', 'Produits')


@section('content')
    @auth('admin')
        @if (Auth::guard('admin')->user()->admin_type == 'agent')
            @include('admin.produit_services.products_agent')
        @else
            <div class=" relative overflow-x-auto  sm:rounded-lg">
                

                @if (session('success'))
                    <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                @endif



                <livewire:publication-produits lazy/>





            </div>

        @endif
    @endauth



    <script src="{{ asset('js/search.js') }}"></script>




@endsection
