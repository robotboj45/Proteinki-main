<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Odczyt danych z POST
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$category_name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';

// Budowanie zapytania SQL
$sql = "SELECT p.*, i.image_path 
        FROM products p 
        LEFT JOIN imagesproduct i ON p.id = i.product_id 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";

// Dodanie wyszukiwania po nazwie produktu
if (!empty($query)) {
    $query = mysqli_real_escape_string($con, $query);
    $sql .= " AND p.name LIKE '%$query%'";
}

// Dodanie warunku dla kategorii
if (!empty($category_name)) {
    $category_name = mysqli_real_escape_string($con, $category_name);
    $sql .= " AND c.name = '$category_name'";
}

$sql .= " ORDER BY p.id DESC";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($product = mysqli_fetch_assoc($result)) {
        $product_id = (int)$product['id'];
        $product_name = htmlspecialchars($product['name']);
        $product_price = number_format($product['price'], 2, ',', ' ');
        $product_image = !empty($product['image_path']) ? 'data:image/jpeg;base64,' . base64_encode($product['image_path']) : '../img/default_product.jpg';

        echo '
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="' . $product_image . '" class="card-img-top" alt="' . $product_name . '">
                <div class="card-body text-center">
                    <h5 class="card-title">' . $product_name . '</h5>
                    <p class="card-text">' . $product_price . ' zł</p>
                    <a href="../products/product.php?id=' . $product_id . '" class="btn btn-outline-primary">Zobacz więcej</a>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<p class="text-center">Nie znaleziono produktów.</p>';
}

mysqli_close($con);
?>
