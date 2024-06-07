// publication
const selectType = document.getElementById("choose");
const champNomProduit = document.getElementById("floating_first_name");
const champConditionnement = document.getElementById("floating_cond");
const champFormat = document.getElementById("floating_format");
const champQteMin = document.getElementById("floating_qtemin");
const champQteMax = document.getElementById("floating_qtemax");
const champPrix = document.getElementById("floating_prix");
const champLivraison = document.getElementById("floating_livraison");
const champPhoto1 = document.getElementById("floating_photo1");
const champPhoto2 = document.getElementById("floating_photo2");
const champPhoto3 = document.getElementById("floating_photo3");
const champPhoto4 = document.getElementById("floating_photo4");
const champDescription = document.getElementById("floating_description");
const champQualification = document.getElementById("floating_qualification");
const champSpecialite = document.getElementById("floating_specialite");
const champQteService = document.getElementById("floating_qte_service");

const champVille = document.getElementById("floating_ville");
const champCommune = document.getElementById("floating_commune");

// Initialement, masquer les champs
champNomProduit.style.display ="none";
champConditionnement.style.display ="none";
champFormat.style.display = "none";
champQteMin.style.display = "none";
champQteMax.style.display = "none";
champPrix.style.display = "none";
champLivraison.style.display = "none";
champPhoto1.style.display = "none";
champPhoto2.style.display = "none"
champPhoto3.style.display = "none"
champPhoto4.style.display = "none"
champDescription.style.display = "none";
champQualification.style.display = "none";
champSpecialite.style.display = "none";
champQteService.style.display = "none";

champVille.style.display = "none";
champCommune.style.display = "none";

// Écouter les changements sur la sélection du type
selectType.addEventListener("change", function () {
    if (selectType.value === "services") {
        // Si "Service" est sélectionné, afficher les champs Nom du produit
        champNomProduit.style.display = "block";
        champPhoto1.style.display = "block";
        champPhoto2.style.display = "block";
        champPhoto3.style.display = "block";
        champPhoto4.style.display = "block";
        champDescription.style.display = "block";
        champSpecialite.style.display = "block";
        champQteService.style.display = "block";
        champQualification.style.display = "block";

        champVille.style.display = "block";
        champCommune.style.display = "block";
        champPrix.style.display = "block";

        // Masquer les autres champs
        champConditionnement.style.display = "none";
        champFormat.style.display = "none";
        champQteMin.style.display = "none";
        champQteMax.style.display = "none";
        champFormat.style.display = "none";
        champQteMin.style.display = "none";
        champQteMax.style.display = "none";

        champLivraison.style.display = "none";
    } else {
        // Sinon, afficher tous les champs
        champNomProduit.style.display = "block";
        champConditionnement.style.display = "block";
        champFormat.style.display = "block";
        champQteMin.style.display = "block";
        champQteMax.style.display = "block";
        champPrix.style.display = "block";
        champLivraison.style.display = "block";
        champPhoto1.style.display = "block";
        champPhoto2.style.display = "block";
        champPhoto3.style.display = "block";
        champPhoto4.style.display = "block";
        champDescription.style.display = "block";

        champVille.style.display = "block";
        champCommune.style.display = "block";
        // Masquer les autres champs
        champSpecialite.style.display = "none";
        champQteService.style.display = "none";
        champQualification.style.display = "none";

    }
});


//consommations
// Sélection des éléments HTML
const champchooseC = document.getElementById("chooseC");
const champNameC = document.getElementById("floating_nameC");
const champConditionnementC = document.getElementById("floating_conditionnementC");
const champFormatC = document.getElementById("floating_formatC");
const champQteC = document.getElementById("floating_quantiteC");
const champPrixC = document.getElementById("floating_prixC");
const champFreqConsommation = document.getElementById("floating_cons");
const champJourAchat = document.getElementById("floating_jour_achat");
const champQualificationServiceC = document.getElementById("floating_qualifC");
const champSpecialiteC = document.getElementById("floating_specialiteC");
const champDescriptionC = document.getElementById("floating_descriptionC");
const champZoneActivite = document.getElementById("floating_zone_activite");
const champVilleC = document.getElementById("floating_villeC");

// Masquer tous les champs au chargement de la page
champNameC.style.display = "none";
champConditionnementC.style.display = "none";
champFormatC.style.display = "none";
champQteC.style.display = "none";
champPrixC.style.display = "none";
champFreqConsommation.style.display = "none";
champJourAchat.style.display = "none";
champQualificationServiceC.style.display = "none";
champSpecialiteC.style.display = "none";
champDescriptionC.style.display = "none";
champZoneActivite.style.display = "none";
champVilleC.style.display = "none";

// Écouter les changements sur la sélection du type
champchooseC.addEventListener("change", function () {
    if (champchooseC.value === "services") {
        // Si "Service" est sélectionné, afficher les champs
        champNameC.style.display = "block";
        champDescriptionC.style.display = "block";
        champSpecialiteC.style.display = "block";
        champFreqConsommation.style.display = "block";
        champQteC.style.display = "block";
        champZoneActivite.style.display = "block";
        champVilleC.style.display = "block";
        champQualificationServiceC.style.display = "block";
        champPrixC.style.display = "block";
        champQualificationServiceC.style.display = "block";
        champJourAchat.style.display = "block";


        // Masquer les autres champs

        champFreqConsommation.style.display = "block";

        champQualificationServiceC.style.display = "block";

        champConditionnementC.style.display = "none";
        champFormatC.style.display = "none";
    } else {
        // Sinon, afficher tous les champs
        champNameC.style.display = "block";
        champConditionnementC.style.display = "block";
        champFormatC.style.display = "block";
        champFreqConsommation.style.display = "block";
        champJourAchat.style.display = "block";
        champQteC.style.display = "block";
        champFreqConsommation.style.display = "block";
        champDescriptionC.style.display = "block";
        champZoneActivite.style.display = "block";
        champVilleC.style.display = "block";
        champPrixC.style.display = "block";


        // Masquer les autres champs
        champQualificationServiceC.style.display = "none";
        champSpecialiteC.style.display = "none";

    }
});


