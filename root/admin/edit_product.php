<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Sprawdzenie uprawnień
if (!isset($_SESSION['user_group']) || $_SESSION['user_group'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Pobranie danych produktu na podstawie ID
$product = null;
$categories = [];
if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Pobranie danych produktu
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product) {
        echo "Nie znaleziono produktu.";
        exit;
    }

    // Pobranie kategorii
    $category_query = "SELECT id, name FROM categories";
    $category_result = mysqli_query($con, $category_query);
    while ($row = mysqli_fetch_assoc($category_result)) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Pobranie danych z formularza
    $product_id = mysqli_real_escape_string($con, $_POST['id']); 
    $product_name = mysqli_real_escape_string($con, $_POST['name']);
    $product_description = mysqli_real_escape_string($con, $_POST['description']);
    $product_price = mysqli_real_escape_string($con, $_POST['price']);
    $category = mysqli_real_escape_string($con, $_POST['category_id']);
    
    // Obsługa zdjęcia
    $image_blob = null;
    $update_image = false; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_blob = file_get_contents($_FILES['image']['tmp_name']);
        $update_image = true; 
    }

    // Przygotowanie zapytania SQL
    if (!empty($product_id) && !empty($product_name) && !empty($product_description) && !empty($product_price) && !empty($category)) {
        if ($update_image) {
            // Aktualizacja wszystkich danych, w tym zdjęcia
            $query = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image_path = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ssdisi", $product_name, $product_description, $product_price, $category, $image_blob, $product_id);
        } else {
            // Aktualizacja bez zmiany zdjęcia
            $query = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ssdii", $product_name, $product_description, $product_price, $category, $product_id);
        }


        if (mysqli_stmt_execute($stmt)) {
            echo "Produkt został zaktualizowany pomyślnie!";
        } else {
            echo "Błąd podczas aktualizacji: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Proszę wypełnić wszystkie wymagane pola!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Produkt - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
<div class="container py-5">
    <h2 class="text-center text-primary mb-4">Edytuj Produkt</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nazwa produktu</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Opis</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Cena</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo $product['price']; ?>" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Kategoria</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <!-- Generowanie opcji -->
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Zdjęcie</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/webp">
            <?php if (!empty($product['image_path'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image_path']); ?>" alt="Aktualne zdjęcie" class="img-thumbnail mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
    </form>
    <a href="manage_products.php" class="btn btn-success mt-4">Powrót</a>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
