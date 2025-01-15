function changeImage(src) {
    document.getElementById("mainImage").src = src;
}

function toggleVisibility() {
    const content = document.getElementById("toggleContent");
    content.classList.toggle("hidden");
}

document.addEventListener(
    "livewire:navigate",
    () => {
        console.log(
            "Livewire navigated: Réinitialisation des scripts deatils produit"
        );
        reinitializeUI();
    },
    {
        once: true,
    }
);

// Fonction pour réinitialiser les composants UI
function reinitializeUI() {
    // Initialiser le comportement du bouton toggle
    const toggleForm = document.getElementById("toggleForm");
    const hiddenSection = document.getElementById("hiddenSection");

    if (toggleForm && hiddenSection) {
        // Supprimer les anciens gestionnaires pour éviter les doublons
        toggleForm.replaceWith(toggleForm.cloneNode(true));
        const newToggleForm = document.getElementById("toggleForm");

        newToggleForm.addEventListener("click", function () {
            const arrow = this.querySelector("svg");

            if (!hiddenSection.classList.contains("show")) {
                hiddenSection.style.display = "block";
                // Forcer un reflow
                hiddenSection.offsetHeight;
                hiddenSection.classList.add("show");
                arrow?.classList.add("rotate-180");
            } else {
                hiddenSection.classList.remove("show");
                arrow?.classList.remove("rotate-180");
                setTimeout(() => {
                    if (!hiddenSection.classList.contains("show")) {
                        hiddenSection.style.display = "none";
                    }
                }, 300);
            }
        });
    }

    // Réinitialiser les modals avec un petit délai
    setTimeout(() => {
        const modals = document.querySelectorAll("[data-hs-overlay]");
        modals.forEach((modal) => {
            if (typeof HSOverlay !== "undefined") {
                new HSOverlay(modal);
            }
        });
    }, 100);
}

// Initialiser après le chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    reinitializeUI();
});

// Initialiser après les mises à jour Livewire
document.addEventListener("livewire:initialized", () => {
    reinitializeUI();
});
document.addEventListener("livewire:navigated", () => {
    reinitializeUI();
});
