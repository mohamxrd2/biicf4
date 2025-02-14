document.addEventListener("DOMContentLoaded", function () {
    // Récupération des éléments DOM avec vérification
    const elements = {
        amountInput: document.getElementById("amount"),
        durationInput: document.getElementById("duration"),
        durationLabel: document.getElementById("durationLabel"),
        potentialGainDisplay: document.getElementById("potentialGain"),
        endDateDisplay: document.getElementById("endDateDisplay"),
        fraisDeServiceDisplay: document.getElementById("fraisDeSevice"),
        frequencyInputs: document.querySelectorAll('input[name="frequency"]'),
    };

    // Vérification des éléments requis
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
        const today = new Date();
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
                const potentialGain = (duration * amount) - fraisDeService;

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

    // Event listeners
    elements.amountInput?.addEventListener("input", calculatePotentialGain);
    elements.durationInput?.addEventListener("input", calculatePotentialGain);

    elements.frequencyInputs.forEach((input) => {
        input.addEventListener("change", () => {
            updateDurationLabel();
            calculatePotentialGain();
        });
    });

    // Configuration initiale
    updateDurationLabel();
    calculatePotentialGain();
});
