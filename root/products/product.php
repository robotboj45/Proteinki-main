<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}



$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product_query = "SELECT p.*, c.name AS category_name 
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = ?";

$stmt = $con->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    echo "<p class='text-center'>Produkt nie znaleziony.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Sklep z Suplementami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../index.php"><img src="../img/logo.png" alt="Logo Sklepu" class="logo"></a>
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
                <li class="nav-item"><a class="nav-link" href="../contact/contact.php">Kontakt</a></li>
                <li class="nav-item"><a class="nav-link" href="../checkout/cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a></li>
            </ul>
        </div>
    </div>

    <!-- Product Details Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <?php
if (!empty($product['image_path'])) {
    $mime_type = finfo_buffer(finfo_open(), $product['image_path'], FILEINFO_MIME_TYPE);
    echo '<img src="data:' . $mime_type . ';base64,' . base64_encode($product['image_path']) . '" alt="' . htmlspecialchars($product['name']) . '" class="img-fluid rounded shadow">';
} else {
    echo '<img src="../img/default_product.jpg" alt="' . htmlspecialchars($product['name']) . '" class="img-fluid rounded shadow">';
}

                    ?>
                </div>
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-muted">Kategoria: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p class="lead mb-4">
                        <?php echo number_format($product['price'], 2, ',', ' ') . ' zł'; ?>
                    </p>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <a href="../checkout/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary mt-3">Dodaj do koszyka</a>

                    <?php
                        if ($_SESSION['user_group'] == 'admin')
                        {
                            echo "<a href='../admin/edit_product.php?id=" . $product_id . "' class='btn btn-primary mt-3'>Edytuj</a>" ;
                        exit;
                        }
                    ?>
                </div>

            </div>
            <button class="btn btn-secondary" onclick="window.history.back();">Powrót</button>
        </div>
    </section>



    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container text-center">
            <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a class="text-white" href="../policy/privacy.php">Polityka prywatności</a></li>
                <li class="list-inline-item"><a class="text-white" href="../policy/terms.php">Regulamin sklepu</a></li>
                <li class="list-inline-item"><a class="text-white" href="../about/about.php">O nas</a></li>
            </ul>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
