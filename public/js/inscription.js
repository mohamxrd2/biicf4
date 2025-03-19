document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("signupForm");
    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const submitBtn = document.getElementById("submitBtn");
    let currentStep = 1;

    // Validation des champs
    function validateStep1() {
        const inputs = step1.querySelectorAll(
            "input[required], select[required]"
        );
        let isValid = true;

        inputs.forEach((input) => {
            if (!input.value) {
                isValid = false;
                showError(input, "Ce champ est requis");
            } else {
                hideError(input);
            }
        });

        return isValid;
    }

    function showError(input, message) {
        const errorSpan = input.nextElementSibling;
        errorSpan.textContent = message;
        errorSpan.classList.remove("hidden");
        input.classList.add("border-red-500");
    }

    function hideError(input) {
        const errorSpan = input.nextElementSibling;
        errorSpan.classList.add("hidden");
        input.classList.remove("border-red-500");
    }

    // Navigation entre les étapes
    nextBtn.addEventListener("click", () => {
        if (currentStep === 1 && validateStep1()) {
            step1.classList.add("hidden");
            step2.classList.remove("hidden");
            prevBtn.classList.remove("hidden");
            nextBtn.classList.add("hidden");
            submitBtn.classList.remove("hidden");
            currentStep = 2;
            updateProgress();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (currentStep === 2) {
            step2.classList.add("hidden");
            step1.classList.remove("hidden");
            prevBtn.classList.add("hidden");
            nextBtn.classList.remove("hidden");
            submitBtn.classList.add("hidden");
            currentStep = 1;
            updateProgress();
        }
    });

    function updateProgress() {
        const steps = document.querySelectorAll(".step-active");
        const lines = document.querySelectorAll(".step-line");

        steps.forEach((step, index) => {
            if (index < currentStep) {
                step.classList.add("bg-blue-600");
                step.classList.remove("bg-gray-200");
            } else {
                step.classList.remove("bg-blue-600");
                step.classList.add("bg-gray-200");
            }
        });

        lines.forEach((line, index) => {
            if (index < currentStep - 1) {
                line.classList.add("bg-blue-600");
                line.classList.remove("bg-gray-200");
            } else {
                line.classList.remove("bg-blue-600");
                line.classList.add("bg-gray-200");
            }
        });
    }
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Validation en temps réel des mots de passe
document
    .getElementById("repeat-password")
    .addEventListener("input", function () {
        const password = document.getElementById("password").value;
        const repeatPassword = this.value;
        const errorSpan = this.nextElementSibling.nextElementSibling;

        if (password !== repeatPassword) {
            errorSpan.textContent = "Les mots de passe ne correspondent pas";
            errorSpan.classList.remove("hidden");
            this.classList.add("border-red-500");
        } else {
            errorSpan.classList.add("hidden");
            this.classList.remove("border-red-500");
        }
    });
