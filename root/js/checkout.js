document.addEventListener("DOMContentLoaded", function() {
    console.log("Strona zamówienia załadowana");
});

const checkoutButton = document.querySelector(".checkout-button");
checkoutButton.addEventListener("click", function() {
    alert("Dziękujemy za złożenie zamówienia!");
});