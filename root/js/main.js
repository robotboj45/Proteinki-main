document.addEventListener("DOMContentLoaded", function() {
    console.log("Sklep z Suplementami - JavaScript załadowany!");
});

// Dodaj bootstrapowe tooltipy
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});