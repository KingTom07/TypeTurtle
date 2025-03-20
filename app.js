const textElement = document.querySelector("#text");
const inputElement = document.querySelector("#input");
const wpmElement = document.querySelector("#wpm");
const accuracyElement = document.querySelector("#accuracy");
const timeElement = document.querySelector("#time");

let words = [];
let currentWordIndex = 0;
let startTime = null;
let correctCount = 0;
let incorrectCount = 0;
let totalTyped = 0;
let timer = 60;
let timerInterval = null;

// Load words dynamically
const generateWords = () => {
    words = [];
    for (let i = 0; i < 30; i++) {
        const randomIndex = Math.floor(Math.random() * randomWords.length);
        words.push(randomWords[randomIndex]);
    }
    displayWords();
};

// Display words
const displayWords = () => {
    textElement.innerHTML = words
        .map((word, i) => `<span id="word-${i}">${word.split("").map(letter => `<span>${letter}</span>`).join("")}</span>`)
        .join(" ");
};

// Start timer
const startTimer = () => {
    startTime = Date.now();
    timerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        timeElement.textContent = Math.max(60 - elapsed, 0);
        if (elapsed >= 60) endGame();
    }, 1000);
};

// Handle input
inputElement.addEventListener("input", () => {
    if (!startTime) startTimer();

    const typed = inputElement.value.trim();
    totalTyped++;

    const activeWord = words[currentWordIndex];
    const letterSpans = document.querySelectorAll(`#word-${currentWordIndex} span`);

    for (let i = 0; i < activeWord.length; i++) {
        if (typed[i] === activeWord[i]) {
            letterSpans[i].classList.add("correct");
            letterSpans[i].classList.remove("incorrect");
        } else if (typed[i] !== undefined) {
            letterSpans[i].classList.add("incorrect");
            letterSpans[i].classList.remove("correct");
        } else {
            letterSpans[i].classList.remove("correct", "incorrect");
        }
    }

    if (typed === activeWord) {
        correctCount++;
        inputElement.value = "";
        currentWordIndex++;
        if (currentWordIndex >= words.length) generateWords();
    }
    
    updateStats();
});

// Update stats
const updateStats = () => {
    const elapsedMinutes = (Date.now() - startTime) / 60000;
    const wpm = Math.round((correctCount / elapsedMinutes) || 0);
    const accuracy = totalTyped ? Math.round((correctCount / totalTyped) * 100) : 100;

    wpmElement.textContent = wpm;
    accuracyElement.textContent = accuracy + "%";
};

// End game
const endGame = () => {
    clearInterval(timerInterval);
    inputElement.disabled = true;
};

// Start
generateWords();
