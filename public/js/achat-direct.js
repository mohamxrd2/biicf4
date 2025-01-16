
function togglePeriodSelect() {
    const timeInput = document.getElementById("timePickerStart");
    const periodSelect = document.getElementById("dayPeriod");
    const periodSelect2 = document.getElementById("dayPeriodFin");
    periodSelect.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
    periodSelect2.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
}

function togglePeriodSelect2() {
    const timeInput = document.getElementById("timePickerEnd");
    const periodSelect = document.getElementById("dayPeriodFin");
    const periodSelect2 = document.getElementById("dayPeriod");
    periodSelect.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
    periodSelect2.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
}

function toggleTimeInput2() {
    const timeInput = document.getElementById("timePickerEnd");
    const timeInput2 = document.getElementById("timePickerStart");
    const periodSelect = document.getElementById("dayPeriodFin");
    timeInput.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
    timeInput2.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
}

function toggleTimeInput() {
    const timeInput = document.getElementById("timePickerStart");
    const timeInput2 = document.getElementById("timePickerEnd");
    const periodSelect = document.getElementById("dayPeriod");
    timeInput.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
    timeInput2.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
}

function toggleVisibility() {
    const contentDiv = document.getElementById("toggleContent");

    if (contentDiv.classList.contains("hidden")) {
        contentDiv.classList.remove("hidden");
        // Forcing reflow to enable transition
        contentDiv.offsetHeight;
        contentDiv.classList.add("show");
    } else {
        contentDiv.classList.remove("show");
        contentDiv.addEventListener(
            "transitionend",
            () => {
                contentDiv.classList.add("hidden");
            },
            {
                once: true,
            }
        );
    }
}
