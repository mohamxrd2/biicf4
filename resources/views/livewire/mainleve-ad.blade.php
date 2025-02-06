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


{{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateLivrInput = document.querySelector('input[name="dateLivr"]');
            const startDate = new Date("{{ $notification->data['date_tot'] }}");
            const endDate = new Date("{{ $notification->data['date_tard'] }}");

            dateLivrInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);

                if (selectedDate < startDate || selectedDate > endDate) {
                    alert('La date de livraison doit être dans l\'intervalle spécifié.');
                    this.value = ''; // Réinitialiser le champ si la date est invalide
                }
            });
        });
    </script> --}}
</div>
