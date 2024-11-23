<?php
session_start();
include("backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$is_logged_in = isset($_SESSION['user_id']);
$user_group =  $_SESSION['user_group'];

$username = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep z Suplementami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php"><img src="img/logo.png" alt="Logo Sklepu" class="logo"></a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <i class="fas fa-bars fa-2x"></i>
            </button>
        </div>
    </header>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Strona główna</a></li>
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
                                echo '<li><a class="dropdown-item" href="categories/'.$category_name.'.php">'.$category_name.'</a></li>';
                            }
                        } else {
                            echo '<li><a class="dropdown-item disabled" href="#">Brak kategorii</a></li>';
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="contact/contact.php">Kontakt</a></li>
                <?php if ($user_group === 'admin'):?>
                    <li class="nav-item"><a class="nav-link" href="backend/read_users.php">Admin Panel: Zarządzanie Użytkownikami</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/manage_products.php">Admin Panel: Zarządzanie Produktami</a></li>
                <?php endif; ?>
                <?php if ($is_logged_in): ?>
                    <li class="nav-item"><a class="nav-link" href="account/user_panel.php">Panel Użytkownika</a></li>
                    <li class="nav-item"><a class="nav-link" href="backend/logout.php">Wyloguj się</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="account/login.html">Logowanie</a></li>
                    <li class="nav-item"><a class="nav-link" href="account/register.html">Rejestracja</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="checkout/cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a></li>
            </ul>
        </div>
    </div>

    <section class="banner">
        <div class="container my-4 position-relative">
            <img src="img/banner.jpg" alt="Banner Promocyjny" class="img-fluid rounded shadow">
            <div class="banner-text position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-4 fw-bold">Witamy w naszym sklepie z suplementami!</h1>
                <p class="lead">Zadbaj o swoje zdrowie i osiągnij swoje cele fitness z naszymi produktami.</p>
                <a href="#products" class="btn btn-warning btn-lg mt-3">Przeglądaj Nowości</a>
            </div>
        </div>
    </section>

<!-- Products -->
<section id="products" class="products py-5">
    <div class="container">
        <h2 class="text-center mb-4">Nowości</h2>
        <div class="row g-4">
            <?php
            $products_query = "
                SELECT p.*, i.image_path 
                FROM products p 
                LEFT JOIN imagesproduct i ON p.id = i.product_id 
                ORDER BY p.id DESC
            ";
            $products_result = mysqli_query($con, $products_query);
            if ($products_result && mysqli_num_rows($products_result) > 0) {
                while($product = mysqli_fetch_assoc($products_result)) {
                    $product_id = (int)$product['id'];
                    $product_name = htmlspecialchars($product['name']);
                    $product_price = number_format($product['price'], 2, ',', ' ');

                    if (!empty($product['image_path'])) {
                        $product_image = 'data:image/jpeg;base64,' . base64_encode($product['image_path']);
                    } else {
                        $product_image = 'img/default_product.jpg';
                    }


                    echo '
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <a href="products/product.php?id='. $product_id .'">
                                <img src="'. $product_image .'" class="card-img-top" alt="'. $product_name .'">
                            </a>
                            <div class="card-body text-center">
                                <h5 class="card-title">
                                    <a href="products/product.php?id='. $product_id .'" class="text-dark text-decoration-none">'. $product_name .'</a>
                                </h5>
                                <p class="card-text">'. $product_price .' zł</p>
                                <a href="products/product.php?id='. $product_id .'" class="btn btn-outline-primary">Zobacz więcej</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p class="text-center">Brak dostępnych produktów.</p>';
            }
            mysqli_close($con);
            ?>
        </div>
    </div>
</section>

    <footer class="bg-dark text-white py-5">
        <div class="container text-center">
            <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a class="text-white" href="policy/privacy.php">Polityka prywatności</a></li>
                <li class="list-inline-item"><a class="text-white" href="policy/terms.php">Regulamin sklepu</a></li>
                <li class="list-inline-item"><a class="text-white" href="about/about.php">O nas</a></li>
            </ul>
            <div class="social-icons mt-3">
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-twitter fa-2x"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
