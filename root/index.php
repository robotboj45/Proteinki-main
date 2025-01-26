<?php

session_start();
include("backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$is_logged_in = isset($_SESSION['user_id']);
$user_group = $_SESSION['user_group'] ?? 'guest';
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
 <canvas id="matrix-canvas"></canvas>
<audio id="intro-sound" src="sounds/matrix.mp3"></audio>

<script>
    window.addEventListener('load', () => {
        const audioElement = document.getElementById('intro-sound');
        audioElement.play().catch(error => {
            console.warn('Automatyczne odtwarzanie dźwięku zostało zablokowane:', error);
        });
    });
</script>
<header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php"><img src="img/logo.png" alt="Logo Sklepu" class="logo"></a>
        <div class="welcome-message mx-3 d-none d-sm-block">
            <p class="mb-0">Witaj w naszym sklepie!</p>
            <p class="mb-0">U nas znajdziesz wszystkie składniki zdrowej suplementacji!</p>
        </div>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <i class="fas fa-bars fa-2x"></i>
        </button>
    </div>
    <header class="bg-dark text-white py-3">
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
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo isset($username) ? $username : 'Konto'; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                    <?php if($user_group === "admin") { ?>
                    <li><a class="dropdown-item" href="admin/manage_products.php">Zarządzaj produktami</a></li>
                    <li><a class="dropdown-item" href="backend/read_users.php">Zarządzaj użytkownikami</a></li>
                    <?php } ?>
                    <?php if($is_logged_in): ?>
                        <li><a class="dropdown-item" href="account/user_panel.php">Twój Profil</a></li>
                        <li><a class="dropdown-item" href="backend/logout.php">Wyloguj się</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="account/login.html">Logowanie</a></li>
                        <li><a class="dropdown-item" href="account/register.html">Rejestracja</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="checkout/cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a></li>
        </ul>
    </div>
</div>

<section class="hero-banner">
    <div id="customCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/slajder1.jpg" class="d-block w-100" alt="Slajder 1">
            </div>
            <div class="carousel-item">
                <img src="img/slajder2.jpg" class="d-block w-100" alt="Slajder 2">
            </div>
            <div class="carousel-item">
                <img src="img/slajder3.jpg" class="d-block w-100" alt="Slajder 3">
            </div>
        </div>
    </div>
    <div class="hero-content">
        <h1>Odkryj Najlepsze Suplementy</h1>
        <p>Twój partner w zdrowiu i kondycji. Sprawdź nasze najnowsze produkty już dziś!</p>
        <a href="#products" class="btn btn-primary">Zobacz Produkty</a>
    </div>
</section>

<section class="features text-center py-5">
    <div class="container">
        <h2 class="mb-4">Dlaczego warto nas wybrać?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-icon"><i class="fas fa-truck"></i></div>
                <h5>Szybka dostawa</h5>
                <p>Zapewniamy błyskawiczną dostawę w każde miejsce w Polsce.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon"><i class="fas fa-star"></i></div>
                <h5>Najwyższa jakość</h5>
                <p>Wszystkie nasze produkty są w 100% oryginalne i certyfikowane.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon"><i class="fas fa-heart"></i></div>
                <h5>Zadowoleni klienci</h5>
                <p>Tysiące pozytywnych opinii od naszych zadowolonych klientów.</p>
            </div>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="container">
        <h2 class="text-center mb-4">Co mówią nasi klienci?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="review p-3">
                    <p>"Fantastyczny sklep! Produkty wysokiej jakości, a obsługa klienta jest na najwyższym poziomie!"</p>
                    <p><strong>- Anna K.</strong></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="review p-3">
                    <p>"Dzięki suplementom z tego sklepu czuję się lepiej niż kiedykolwiek. Polecam każdemu!"</p>
                    <p><strong>- Piotr M.</strong></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="review p-3">
                    <p>"Zawsze szybka dostawa i świetne promocje. Nigdy się nie zawiodłam."</p>
                    <p><strong>- Ewa L.</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="products" class="products py-5">
    <div class="container">
        <h2 class="text-center mb-4">Nowości</h2>
        <div class="row g-4">
            <?php
            $products_query = "SELECT * FROM products ORDER BY id DESC";
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
            <li class="list-inline-item"><a class="text-white" href="policy/privacy.html">Polityka prywatności</a></li>
            <li class="list-inline-item"><a class="text-white" href="policy/terms.html">Regulamin sklepu</a></li>
            <li class="list-inline-item"><a class="text-white" href="about/about.html">O nas</a></li>
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
