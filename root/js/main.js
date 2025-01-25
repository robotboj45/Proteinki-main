document.addEventListener("DOMContentLoaded", function() {
    console.log("Sklep z Suplementami - JavaScript załadowany!");
});

// Dodaj bootstrapowe tooltipy
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('#carouselExampleIndicators');
    if (carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 2500, // Automatyczna zmiana co 4 sekundy
            wrap: true,    // Powtarzanie slajdów w pętli
        });
    }
});
document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById('matrix-canvas');
    const ctx = canvas.getContext('2d');

    // Dopasowanie rozmiaru canvasu
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    // Dane do animacji Matrixa
    const letters = "ABCDEFGHIJKLMNOPQRSTUVXYZ123456789@#$%^&*()*&^%";
    const matrix = letters.split("");
    const fontSize = 14;
    const columns = canvas.width / fontSize;
    const drops = Array.from({ length: columns }).fill(1);

    // Funkcja rysowania
    function draw() {
        ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "#0F0";
        ctx.font = `${fontSize}px monospace`;

        drops.forEach((y, x) => {
            const text = matrix[Math.floor(Math.random() * matrix.length)];
            ctx.fillText(text, x * fontSize, y * fontSize);

            if (y * fontSize > canvas.height && Math.random() > 0.975) {
                drops[x] = 0;
            }

            drops[x]++;
        });
    }

    // Animacja Matrixa
    const interval = setInterval(draw, 50);

    // Efekt zanikania
    setTimeout(() => {
        canvas.style.opacity = '0'; // Powolne zanikanie
        clearInterval(interval);   // Zatrzymanie animacji
        setTimeout(() => {
            canvas.classList.add('hidden'); // Ukrycie canvasu po zaniku
        }, 1000); // Czas trwania zanikania (1 sekunda)
    }, 5000);
});
