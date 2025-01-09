<div>
    <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8">
        {{-- <x-offre.menu-dropdown :produit="$produit" /> --}}

        <x-dynamic-component
            component="offre-simple-modal"
            :produit="$produit"
            :nombreProprietaires="$nombreProprietaires" />

        {{-- <x-dynamic-component
            component="offre-negociee-modal"
            :produit="$produit"
            :nombreProprietaires="$nombreProprietaires" /> --}}

        <x-dynamic-component
            component="offre-groupee-modal"
            :produit="$produit"
            :nombreFournisseurs="$nomFournisseurCount" />
    </div>
</div>
