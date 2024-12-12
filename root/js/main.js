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
