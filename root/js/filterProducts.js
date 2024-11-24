function filterProducts() {
    let searchValue = document.getElementById("searchInput").value.toLowerCase();
    let filterValue = document.getElementById("filterSelect").value;
    let products = document.querySelectorAll(".product-card");

    products.forEach(product => {
        let title = product.querySelector(".card-title").textContent.toLowerCase();
        let category = product.classList.contains(filterValue) || filterValue === "all";

        if (title.includes(searchValue) && category) {
            product.style.display = "block";
        } else {
            product.style.display = "none";
        }
    });
}