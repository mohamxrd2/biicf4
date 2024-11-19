<div class="w-full md:w-1/2 md:h-auto flex flex-col space-y-6">
    <div class="bg-white rounded-lg shadow-lg p-6 ">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
            sur le client</h2>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-gray-600 font-medium">Nom du client:</p>
                <p class="text-gray-800">{{ $userDetails->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-medium">Email:</p>
                <p class="text-gray-800">{{ $userDetails->email }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-medium">Numéro de
                    téléphone:</p>
                <p class="text-gray-800">{{ $userDetails->phone }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-medium">Cote de Crédit</p>
                <p class="text-gray-800">{{ $crediScore->ccc }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-medium">Adresse:</p>
                <p class="text-gray-800">
                    {{ $userDetails->country }},{{ $userDetails->ville }},{{ $userDetails->departe }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 ">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
            sur la demande de crédit</h2>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-gray-600 font-medium">Motif du crédit:
                </p>
                <p class="text-gray-800">
                    {{ $demandeCredit->objet_financement ?? $projet->name }}</p>
            </div>


            <div>
                <p class="text-gray-600 font-medium">Type de crédit:
                </p>
                <p class="text-gray-800">
                    {{ $demandeCredit->type_financement ?? $projet->type_financement }}</p>
            </div>

            <div>
                @php
                    use Carbon\Carbon;
                    $date = Carbon::parse($demandeCredit->duree ?? $projet->durer); // Assurez-vous que duree est une date valide
                    $date2 = Carbon::parse($demandeCredit->date_debut ?? $projet->created_at); // Assurez-vous que duree est une date valide
                    $date3 = Carbon::parse($demandeCredit->date_fin ?? $projet->date_fin); // Assurez-vous que duree est une date valide
                @endphp
                <p class="text-gray-600 font-medium">Date de Remboursement du crédit:
                </p>
                <p class="text-gray-800"> {{ $date->isoFormat(' DD MMMM YYYY') }}

                </p>
            </div>

            <div>
                <p class="text-gray-600 font-medium">Date de soumission:
                </p>
                <p class="text-gray-800">{{ $date2->isoFormat(' DD MMMM YYYY') }}
                </p>
            </div>
            <div>
                <p class="text-gray-600 font-medium">Date de financement:
                </p>
                <p class="text-gray-800">{{ $date3->isoFormat(' DD MMMM YYYY') }}
                </p>
            </div>

        
            <div>
                <p class="text-gray-600 font-medium">Taux du crédit:
                </p>
                <p class="text-gray-800">{{ $demandeCredit->taux ?? $projet->taux }} %
                </p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-md">
                <p class="text-gray-700 font-semibold text-lg mb-1">Montant Total :</p>
                <p class="text-gray-900 font-bold text-xl">
                    {{ number_format($notification->data['montant'], 0, ',', ' ') }} FCFA</p>
            </div>



        </div>
    </div>
</div>
