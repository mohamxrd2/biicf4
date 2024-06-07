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
