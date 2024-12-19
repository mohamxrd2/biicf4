document.addEventListener("alpine:init", () => {
    Alpine.data("countdownTimer", (oldestCommentDate, serverTime) => ({
        oldestCommentDate: oldestCommentDate
            ? new Date(oldestCommentDate)
            : null,
        serverTime: serverTime ? new Date(serverTime).getTime() : null,
        hours: "--",
        minutes: "--",
        seconds: "--",
        endDate: null,
        interval: null,
        isCountdownActive: false,
        hasSubmitted: false,

        init() {
            if (this.oldestCommentDate) {
                this.endDate = new Date(this.oldestCommentDate);
                this.endDate.setMinutes(this.endDate.getMinutes() + 10);
                this.startCountdown();
            }
        },

        // Methode pour calculer le temps restant
        calculateTimeLeft(endDate, currentDate) {
            return Math.max(0, endDate - currentDate);
        },

        startCountdown() {
            if (this.isCountdownActive) {
                console.log(
                    "Le compte à rebours est déjà actif, pas de redémarrage."
                );
                return;
            }

            if (this.interval) {
                clearInterval(this.interval);
            }

            this.updateCountdown();
            this.interval = setInterval(this.updateCountdown.bind(this), 1000);
            this.isCountdownActive = true;
        },

        updateCountdown() {
            if (!this.serverTime) {
                console.error("L'heure du serveur est manquante.");
                clearInterval(this.interval);
                return;
            }

            const currentDate = new Date(this.serverTime); // Utilisation directe de l'heure serveur
            this.serverTime += 1000; // Incrémente l'heure serveur chaque seconde simulée

            const difference = this.calculateTimeLeft(
                this.endDate,
                currentDate
            );
            console.log("Heure serveur actuelle", currentDate);
            console.log("Différence temporelle", difference);

            if (difference <= 0) {
                clearInterval(this.interval);
                this.endCountdown();
                return;
            }
            if (distance > 0) {
                this.hours = Math.floor(
                    (difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                this.minutes = Math.floor(
                    (difference % (1000 * 60 * 60)) / (1000 * 60)
                );
                this.seconds = Math.floor((difference % (1000 * 60)) / 1000);
            } else {
                this.hours = 0;
                this.minutes = 0;
                this.seconds = 0;
            }
        },

        endCountdown() {
            document.getElementById("countdown").innerText = "Temps écoulé !";
            if (!this.hasSubmitted) {
                setTimeout(() => {
                    Livewire.dispatch("compteReboursFini");
                    this.hasSubmitted = true;
                }, 100);
            }
        },
    }));
});
