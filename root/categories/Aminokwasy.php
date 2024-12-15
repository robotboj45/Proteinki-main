<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoria 1 - Sklep z Suplementami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Header -->
<header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../index.php">
            <img src="../img/logo.png" alt="Logo Sklepu" class="logo">
        </a>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <i class="fas fa-bars fa-2x"></i>
        </button>
    </div>
</header>
<!-- Offcanvas Menu -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="../index.php">Strona główna</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kategorie</a>
                <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                    <?php
                    $categories_query = "SELECT * FROM categories";
                    $categories_result = mysqli_query($con, $categories_query);
                    if ($categories_result) {
                        while($row = mysqli_fetch_assoc($categories_result)) {
                            $category_id = htmlspecialchars($row['id']);
                            $category_name = htmlspecialchars($row['name']);
                            echo '<li><a class="dropdown-item" href="'.$category_name.'.php">'.$category_name.'</a></li>';
                        }
                    } else {
                        echo '<li><a class="dropdown-item disabled" href="#">Brak kategorii</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="../contact/contact.php">Kontakt</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo isset($user_data['username']) ? htmlspecialchars($user_data['username']) : 'Konto'; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                    <?php if(isset($user_data['username'])): ?>
                        <li><a class="dropdown-item" href="../backend/logout.php">Wyloguj się</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="../account/login.html">Logowanie</a></li>
                        <li><a class="dropdown-item" href="../account/register.html">Rejestracja</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="../checkout/cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a></li>
        </ul>
    </div>
</div>

<!-- Category Section -->
<section class="py-5">
    <div class="container">

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Szukaj produktów..." onkeyup="filterProducts()">
            </div>

        </div>

        <!-- Products -->
        <section id="products" class="products py-5">
            <div class="container">
                <h2 class="text-center mb-4">Aminokwasy</h2>
                <div class="row g-4" id="productList">
                    <?php
                    $category_id = 3; // Przykładowe ID kategorii
                    $products_query = "
                    SELECT p.*, i.image_path 
                    FROM products p
                    LEFT JOIN imagesproduct i ON p.id = i.product_id
                    WHERE p.category_id = $category_id
                    ORDER BY p.id DESC
                    ";
                    $products_result = mysqli_query($con, $products_query);
                    if ($products_result && mysqli_num_rows($products_result) > 0) {
                        while($product = mysqli_fetch_assoc($products_result)) {
                            $product_id = (int)$product['id'];
                            $product_name = htmlspecialchars($product['name']);
                            $product_price = number_format($product['price'], 2, ',', ' ');
                            $product_image = !empty($product['image_path']) ? 'data:image/jpeg;base64,' . base64_encode($product['image_path']) : '../img/default_product.jpg';

                            echo '
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm border-0">
                                    <img src="'. $product_image .'" class="card-img-top" alt="'. $product_name .'">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">'. $product_name .'</h5>
                                        <p class="card-text">'. $product_price .' zł</p>
                                        <a href="../products/product.php?id='. $product_id .'" class="btn btn-outline-primary">Zobacz więcej</a>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-center">Brak dostępnych produktów.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container text-center">
        <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    function filterProducts() {
        const searchQuery = document.getElementById("searchInput").value.toLowerCase();
        const categoryName = document.querySelector("h2").textContent.trim(); // Pobiera nazwę kategorii z nagłówka

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../backend/filterProducts.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("productList").innerHTML = xhr.responseText;
            } else {
                console.error("Błąd podczas filtrowania produktów.");
            }
        };

        xhr.send(
            "query=" + encodeURIComponent(searchQuery) +
            "&category_name=" + encodeURIComponent(categoryName)
        );
    }

</script>


</body>
</html>
