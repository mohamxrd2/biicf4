<div>
    <!-- Main livreur -->

    @if ($notification->data['fournisseur'] == $user)
        @include('biicf.components.fournisseur')
    @endif


    @if ($notification->data['livreur'] == $user)
        @if ($showMainlever)
            @include('biicf.components.mainleveClick')
        @else
            @include('biicf.components.livreur')
        @endif
    @endif
</div>
