<?php
include('../backend/connection.php');

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Pobranie danych z formularza
    $product_name = mysqli_real_escape_string($con, $_POST['name']);
    $product_description = mysqli_real_escape_string($con, $_POST['description']);
    $product_price = mysqli_real_escape_string($con, $_POST['price']);
    $category = mysqli_real_escape_string($con, $_POST['category_id']);
    
    // Obsługa zdjęcia
    $image_blob = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_blob = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Wstawienie danych do bazy
    if (!empty($product_name) && !empty($product_description) && !empty($product_price) && !empty($category) && !empty($image_blob)) {
        $query = "INSERT INTO products (name, description, price, category_id, image_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssdis", $product_name, $product_description, $product_price, $category, $image_blob);

        if (mysqli_stmt_execute($stmt)) {
            echo "Produkt dodany pomyślnie!";
        } else {
            echo "Błąd: " . mysqli_error($con);
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
    <title>Dodawanie Produktu - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
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
                <li class="nav-item"><a class="nav-link" href="../admin/dashboard.html">Panel administratora</a></li>
            </ul>
        </div>
    </div>

    <!-- Add Product Section -->
    <section class="py-5">
        <div class="container shadow-lg p-5 bg-white rounded">
            <h2 class="text-center mb-4 text-uppercase text-primary h-format">Dodaj nowy produkt</h2>
            <form method="POST" action="add_product.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nazwa produktu:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Opis produktu:</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                </div>
            
                <div class="mb-3">
                    <label for="price" class="form-label">Cena produktu (w zł):</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategoria:</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">-- Wybierz kategorię --</option>
                        <?php
                        $categories_query = "SELECT id, name FROM categories ORDER BY name";
                        $categories_result = mysqli_query($con, $categories_query);
                        while ($row = mysqli_fetch_assoc($categories_result)) {
                            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Zdjęcie produktu:</label>
                    <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/webp" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Dodaj Produkt</button>
                <a href="dashboard.html" class="btn btn-success mt-4">Powrót</a>
            </form>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
