fetch("https://restcountries.com/v3.1/all")
    .then((response) => response.json())
    .then((data) => {
        const countryDropdown = document.getElementById("country");

        // Ajouter la Côte d'Ivoire en tant que première option
        const optionIvoryCoast = document.createElement("option");
        optionIvoryCoast.value = "Ivory Coast";
        optionIvoryCoast.textContent = "Ivory Coast";
        countryDropdown.appendChild(optionIvoryCoast);

        // Ajouter les autres pays
        data.forEach((country, index) => {
            const option = document.createElement("option");
            option.value = "option" + (index + 2);
            option.textContent = country.name.common;
            countryDropdown.appendChild(option);
        });
    })
    .catch((error) =>
        console.error("Erreur lors de la récupération des pays", error)
    );

function populateCountryDropdown() {
    const countryDropdown = document.getElementById("country");
    fetch("https://restcountries.com/v2/all")
        .then((response) => response.json())
        .then((data) => {
            data.forEach((country) => {
                if (
                    country.hasOwnProperty("callingCodes") &&
                    country.callingCodes.length > 0
                ) {
                    const countryCode = country.callingCodes[0];
                    const countryName = country.name;
                    const option = document.createElement("option");
                    option.value = countryCode;
                    option.textContent = `${countryName} (+${countryCode})`;
                    countryDropdown.appendChild(option);
                }
            });
        })
        .catch((error) =>
            console.error("Erreur lors de la récupération des pays", error)
        );
}

populateCountryDropdown();
