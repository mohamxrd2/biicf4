<div>
    @if ($currentPage === 'produit')
        @component('components.produit-details', ['produit' => $produit, 'id' => $id])
        @endcomponent
    @elseif($currentPage === 'achat')
        @livewire('achat-direct-groupe', ['id' => $id])
    @elseif($currentPage === 'credit')
        @livewire('demandecredit')
    @endif
</div>

<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('navigate', page => {
            @this.set('currentPage', page);
        });
    });
</script>
