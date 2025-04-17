
document.addEventListener("DOMContentLoaded", () => {
    const results = JSON.parse(localStorage.getItem("typingTestResults"));

    if (!results) {
        document.body.innerHTML = "<h2>No results found.</h2>";
        return;
    }

    document.getElementById("summary").innerHTML = `
      WPM: <strong>${results.wpm}</strong> |
      Accuracy: <strong>${results.accuracy}%</strong>
    `;

    const ctx = document.getElementById("resultChart").getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["WPM", "Accuracy"],
            datasets: [{
                label: "Typing Performance",
                data: [results.wpm, results.accuracy],
                backgroundColor: ["#4caf50", "#2196f3"]
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Submit score to backend
    fetch("backend/submit_score.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            wpm: results.wpm,
            accuracy: results.accuracy,
            game_mode: results.mode || "default"
        })
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.createElement("p");
        msg.style.marginTop = "1rem";
        msg.style.fontWeight = "bold";
        msg.textContent = data.success ? "✅ Score submitted!" : "⚠️ Failed to submit score.";
        document.querySelector(".result-container").appendChild(msg);
    })
    .catch(() => {
        const msg = document.createElement("p");
        msg.style.marginTop = "1rem";
        msg.style.fontWeight = "bold";
        msg.style.color = "red";
        msg.textContent = "⚠️ Could not contact server.";
        document.querySelector(".result-container").appendChild(msg);
    });
});
