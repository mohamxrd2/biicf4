document.addEventListener("DOMContentLoaded", function () {
    const toggleSwitch = document.getElementById("toggleSwitch");
    const durationSection = document.getElementById("durationSection");
    const gainSection = document.getElementById("gainSection");
    const cotisationText = document.getElementById("cotisationText");
    const frequencyOptions = document.querySelectorAll(".frequency-option");
    const frequencyLabel = document.getElementById("frequencyLabel");
    const frequencyLabel2 = document.getElementById("frequencyLabel2");
    const frequencyCount = document.getElementById("frequencyCount");

    // Ajout d'une propriété "count" pour chaque fréquence
    const frequencyValues = {
        quotidienne: { label: "jours", days: 1, count: 30 }, // 30 jours
        hebdomadaire: { label: "semaines", days: 7, count: 4 }, // 4 semaines
        mensuelle: { label: "mois", days: 30, count: 1 }, // 12 mois
    };

    // Récupération des éléments DOM avec vérification
    const elements = {
        amountInput: document.getElementById("amount"),
        durationInput: document.getElementById("duration"),
        durationLabel: document.getElementById("durationLabel"),
        potentialGainDisplay: document.getElementById("potentialGain"),
        endDateDisplay: document.getElementById("endDateDisplay"),
        endDateDisplay2: document.getElementById("endDateDisplay2"),
        fraisDeServiceDisplay: document.getElementById("fraisDeSevice"),
        frequencyInputs: document.querySelectorAll('input[name="frequency"]'),
    };

    // Get the server time from the data attribute
    const container = document.querySelector("[data-server-time]");
    const serverTime = container
        ? new Date(container.dataset.serverTime)
        : new Date();

    const FREQUENCY_DAYS = {
        quotidienne: 1,
        hebdomadaire: 7,
        mensuelle: 30,
    };

    const DURATION_LABELS = {
        quotidienne: "Nombre de jours",
        hebdomadaire: "Nombre de semaines",
        mensuelle: "Nombre de mois",
    };

    const DURATION_PLACEHOLDERS = {
        quotidienne: "Entrez le nombre de jours",
        hebdomadaire: "Entrez le nombre de semaines",
        mensuelle: "Entrez le nombre de mois",
    };

    function formatDate(date) {
        const options = {
            day: "numeric",
            month: "long",
            year: "numeric",
        };
        return date.toLocaleDateString("fr-FR", options);
    }

    function formatMontant(montant) {
        return new Intl.NumberFormat("fr-FR").format(montant) + " FCFA";
    }

    function calculateEndDate(duration, frequency) {
        // Use the server time instead of new Date()
        const today = serverTime;
        const durationInDays = duration * FREQUENCY_DAYS[frequency];
        const endDate = new Date(today);
        endDate.setDate(today.getDate() + durationInDays);
        return endDate;
    }

    function getSelectedFrequency() {
        const checkedInput = document.querySelector(
            'input[name="frequency"]:checked'
        );
        return checkedInput ? checkedInput.value : null;
    }

    function calculateFraisDeService(amount, duration, frequency) {
        if (!amount || !duration || !frequency) return 0;
        const montantTotal = amount * duration;
        return Math.round(montantTotal / 30); // 1/30ème du montant total
    }

    function calculatePotentialGain() {
        try {
            const amount = parseFloat(elements.amountInput.value) || 0;
            const duration = parseInt(elements.durationInput.value) || 0;
            const frequency = getSelectedFrequency();

            // Valeurs par défaut
            let displayAmount = "0 FCFA";
            let displayDate = "-";
            let displayFrais = "0 FCFA";

            if (amount && duration && frequency) {
                // Calculs
                const endDate = calculateEndDate(duration, frequency);
                const fraisDeService = calculateFraisDeService(
                    amount,
                    duration,
                    frequency
                );
                const potentialGain = duration * amount - fraisDeService;

                // Mise à jour de l'affichage
                displayAmount = formatMontant(potentialGain);
                displayDate = formatDate(endDate);
                displayFrais = formatMontant(fraisDeService);
            }

            // Mise à jour sécurisée de l'interface
            elements.potentialGainDisplay.textContent = displayAmount;
            elements.endDateDisplay.textContent = displayDate;
            elements.fraisDeServiceDisplay.textContent = displayFrais;
        } catch (error) {
            console.error("Erreur lors du calcul:", error);
            // Réinitialisation en cas d'erreur
            elements.potentialGainDisplay.textContent = "0 FCFA";
            elements.endDateDisplay.textContent = "-";
            elements.fraisDeServiceDisplay.textContent = "0 FCFA";
        }
    }

    function updateDurationLabel() {
        try {
            const frequency = getSelectedFrequency();
            if (frequency && DURATION_LABELS[frequency]) {
                elements.durationLabel.textContent = DURATION_LABELS[frequency];
                elements.durationInput.placeholder =
                    DURATION_PLACEHOLDERS[frequency];
            }
        } catch (error) {
            console.error("Erreur lors de la mise à jour du label:", error);
        }
    }

    function updateEndDate() {
        const frequency =
            document.querySelector('input[name="frequency"]:checked')?.value ||
            "mensuelle";
        const duration = frequencyValues[frequency].count || null;
        if (duration > 0) {
            const endDate = calculateEndDate(duration, frequency);
            displayDate = formatDate(endDate);
        }
        elements.endDateDisplay2.textContent = displayDate;
    }

    // Event listeners for frequency options
    frequencyOptions.forEach((option) => {
        option.addEventListener("change", function () {
            const selectedValue = this.value;
            if (frequencyValues[selectedValue]) {
                frequencyLabel.textContent =
                    frequencyValues[selectedValue].label;
            }
        });
    });

    // Event listener for toggle switch
    toggleSwitch?.addEventListener("change", function () {
        if (this.checked) {
            // Cacher Durée & Gain Potentiel + Afficher le texte
            durationSection.classList.add("hidden");
            gainSection.classList.add("hidden");
            cotisationText.classList.remove("hidden");
        } else {
            // Afficher Durée & Gain Potentiel + Cacher le texte
            durationSection.classList.remove("hidden");
            gainSection.classList.remove("hidden");
            cotisationText.classList.add("hidden");
        }
    });

    // Event listeners for inputs
    elements.amountInput?.addEventListener("input", calculatePotentialGain);
    elements.durationInput?.addEventListener("input", calculatePotentialGain);
    // Écouteurs d'événements
    elements.frequencyInputs.forEach((input) =>
        input.addEventListener("change", updateEndDate)
    );

    elements.frequencyInputs.forEach((input) => {
        input.addEventListener("change", () => {
            updateDurationLabel();
            calculatePotentialGain();
        });
    });

    // Check if required elements exist before proceeding
    const requiredElements = [
        "amountInput",
        "durationInput",
        "durationLabel",
        "potentialGainDisplay",
        "endDateDisplay",
        "fraisDeServiceDisplay",
    ];

    const missingElements = requiredElements.filter(
        (elementName) => !elements[elementName]
    );

    if (missingElements.length > 0) {
        console.error("Éléments manquants dans le DOM:", missingElements);
        return;
    }

    // Configuration initiale
    updateDurationLabel();
    calculatePotentialGain();
    // Initialisation
    updateEndDate();
});
