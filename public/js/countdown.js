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
                this.endDate.setMinutes(this.endDate.getMinutes() + 5);
                this.startCountdown();
            }

            Echo.channel("oldest-comment").listen(
                "OldestCommentUpdated",
                (e) => {
                    console.log("Événement OldestCommentUpdated reçu", e);
                    if (e.oldestCommentDate) {
                        const newDate = new Date(e.oldestCommentDate);

                        if (
                            !this.oldestCommentDate ||
                            this.oldestCommentDate.getTime() !==
                                newDate.getTime()
                        ) {
                            this.oldestCommentDate = newDate;
                            this.endDate = new Date(this.oldestCommentDate);
                            this.endDate.setMinutes(
                                this.endDate.getMinutes() + 5
                            );
                            this.startCountdown();
                        } else {
                            console.log("Le compte à rebours est déjà à jour.");
                        }
                    } else {
                        console.error(
                            "oldestCommentDate est null ou incorrect !",
                            e
                        );
                    }
                }
            );

            console.log(
                "Initialisation du oldestCommentDate",
                this.oldestCommentDate
            );
            console.log("Initialisation du endDate", this.endDate);
            console.log("Initialisation du serverTime", this.serverTime);
        },
        
        countdownUpdated(data) {
            if (!data || !data.oldestCommentDate || !data.serverTime) {
                console.error("Invalid data received from Livewire:", data);
                return;
            }

            const newServerTime = new Date(data.serverTime).getTime();
            if (isNaN(newServerTime)) {
                console.error("Invalid server time received");
                return;
            }

            this.oldestCommentDate = new Date(data.oldestCommentDate);
            this.serverTime = newServerTime;
            this.endDate = new Date(this.oldestCommentDate);
            this.endDate.setMinutes(
                this.endDate.getMinutes() + this.COUNTDOWN_MINUTES
            );
            this.startCountdown();
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

            this.hours = Math.floor(
                (difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            this.minutes = Math.floor(
                (difference % (1000 * 60 * 60)) / (1000 * 60)
            );
            this.seconds = Math.floor((difference % (1000 * 60)) / 1000);
        },

        endCountdown() {
            document.getElementById("countdown").innerText = "Temps écoulé !";

            if (!this.hasSubmitted) {
                Livewire.dispatch("compteReboursFini");
                this.hasSubmitted = true;
            }
        },
    }));
});
