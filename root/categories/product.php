<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product_query = "SELECT p.*, i.image_path FROM products p
                  LEFT JOIN imagesproduct i ON p.id = i.product_id
                  WHERE p.id = ?";
$stmt = $con->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result && $product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
    $product_name = htmlspecialchars($product['name']);
    $product_price = number_format($product['price'], 2, ',', ' ');

    // Zamiast obrazu w bazie danych
    if (!empty($product['image_path'])) {
        $product_image = 'data:image/jpeg;base64,' . base64_encode($product['image_path']);
    } else {
        $product_image = '../img/default_product.jpg';
    }
} else {
    echo "<p>Produkt nie istnieje.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_name; ?> - Sklep z Suplementami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<!-- Your header and navigation here -->

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4"><?php echo $product_name; ?></h2>
        <div class="row g-4">
            <div class="col-md-6">
                <img src="<?php echo $product_image; ?>" class="img-fluid" alt="<?php echo $product_name; ?>">
            </div>
            <div class="col-md-6">
                <h4><?php echo $product_price; ?> zł</h4>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <button class="btn btn-primary">Dodaj do koszyka</button>
            </div>
        </div>
        <br>
        <!-- Przycisk powrotu -->
        <button class="btn btn-secondary" onclick="window.history.back();">Powrót</button>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container text-center">
        <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
