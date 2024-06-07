const userTypeSelect = document.getElementById("user_type");
const userSexeInput = document.getElementById("user_sexe");
const userAgeInput = document.getElementById("user_age");
const userStatusInput = document.getElementById("user_status");
const userCompSizeInput = document.getElementById("user_comp_size");
const userServInput = document.getElementById("user_serv");
const userOrgtyp1Input = document.getElementById("user_orgtyp");
const userOrgtyp2Input = document.getElementById("user_orgtyp2");
const userComInput = document.getElementById("user_com");
const userMena1Input = document.getElementById("user_mena1");
const userMena2Input = document.getElementById("user_mena2");

function showInputFields(
  sexe,
  age,
  status,
  compSize,
  serv,
  orgtyp1,
  orgtyp2,
  com,
  mena1,
  mena2
) {
  userSexeInput.style.display = sexe;
  userAgeInput.style.display = age;
  userStatusInput.style.display = status;
  userCompSizeInput.style.display = compSize;
  userServInput.style.display = serv;
  userOrgtyp1Input.style.display = orgtyp1;
  userOrgtyp2Input.style.display = orgtyp2;
  userComInput.style.display = com;
  userMena1Input.style.display = mena1;
  userMena2Input.style.display = mena2;
}

showInputFields(
  "block",
  "block",
  "block",
  "none",
  "none",
  "none",
  "none",
  "none",
  "none",
  "none"
);

userTypeSelect.addEventListener("change", (event) => {
  const selectedOption = event.target.value;

  switch (selectedOption) {
    case "Personne physique":
      showInputFields(
        "block",
        "block",
        "block",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none"
      );
      break;
    case "Personne morale":
      showInputFields(
        "none",
        "none",
        "none",
        "block",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none"
      );
      break;
    case "Service public":
      showInputFields(
        "none",
        "none",
        "none",
        "none",
        "block",
        "none",
        "none",
        "none",
        "none",
        "none"
      );
      break;
    case "Organisme":
      showInputFields(
        "none",
        "none",
        "none",
        "none",
        "none",
        "block",
        "block",
        "none",
        "none",
        "none"
      );
      break;
    case "Communauté":
      showInputFields(
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "block",
        "none",
        "none"
      );
      break;
    case "Menage":
      showInputFields(
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "none",
        "block",
        "block"
      );
      break;
    default:
    // Gérer d'autres options au besoin
  }
});



// Assurez-vous que le DOM est chargé avant d'exécuter le code
document.addEventListener("DOMContentLoaded", () => {
    // Sélection des éléments par leur ID
    const sectorActivitySelector = document.getElementById("sector_activity");
    const industrySelector = document.getElementById("industry");
    const buildingTypeInput = document.getElementById("building_type");
    const commerceSectorSelector = document.getElementById("commerce_sector");
    const transportSectorSelector = document.getElementById("transport_sector");
  
    // Fonction pour masquer un élément
    const hideElement = (element) => {
      if (element) {
        element.style.display = "none";
      }
    };
  
    // Fonction pour afficher un élément
    const showElement = (element) => {
      if (element) {
        element.style.display = "block";
      }
    };
  
    // Masquer tous les éléments sauf le premier par défaut (industry)
    hideElement(buildingTypeInput);
    hideElement(commerceSectorSelector);
    hideElement(transportSectorSelector);
  
    // Écouter les changements sur le sélecteur d'activité
    sectorActivitySelector.addEventListener("change", (event) => {
      const selectedOption = event.target.value;
  
      // Masquer tous les éléments
      hideElement(industrySelector);
      hideElement(buildingTypeInput);
      hideElement(commerceSectorSelector);
      hideElement(transportSectorSelector);
  
      // Afficher l'élément approprié en fonction de l'option sélectionnée
      switch (selectedOption) {
        case "Industrie":
          showElement(industrySelector);
          break;
        case "Construction":
          showElement(buildingTypeInput);
          break;
        case "Commerce":
          showElement(commerceSectorSelector);
          break;
        case "Service":
          showElement(transportSectorSelector);
          break;
        default:
          // Ne rien faire pour les autres options
      }
    });
  });
  
