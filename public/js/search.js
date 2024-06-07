function searchTable() {
    // Récupérer la valeur saisie dans le champ de recherche
    var input = document.getElementById("searchInput").value.toUpperCase();

    // Récupérer les lignes du tableau
    var rows = document.getElementsByTagName("tr");

    var hasResult = false; // Variable pour vérifier si au moins un résultat est trouvé

    // Parcourir les lignes du tableau
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];

        // Ignorer le traitement de la première ligne (en-tête) du tableau
        if (i === 0) {
            continue;
        }

        var cells = row.querySelectorAll("th, td"); // Sélectionner à la fois les balises <th> et <td>
        var found = false;

        // Parcourir les cellules de chaque ligne
        for (var j = 0; j < cells.length; j++) {
            var cell = cells[j];
            if (cell) {
                var textValue = cell.textContent || cell.innerText;
                if (textValue.toUpperCase().indexOf(input) > -1) {
                    found = true;
                    hasResult = true;
                    break;
                }
            }
        }

        // Afficher ou masquer la ligne en fonction du résultat de la recherche
        if (found) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }

    // Afficher un message si aucun résultat n'est trouvé
    if (!hasResult) {
        var noResultMessage = document.getElementById("noResultMessage");
        if (noResultMessage) {
            noResultMessage.style.display = "";
        }
    } else {
        var noResultMessage = document.getElementById("noResultMessage");
        if (noResultMessage) {
            noResultMessage.style.display = "none";
        }
    }
}
