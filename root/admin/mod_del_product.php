<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Sprawdzenie, czy formularz usuwania lub modyfikacji został wysłany
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        // Usuwanie produktu
        $product_id = $_POST['product_id'];
        $delete_query = "DELETE FROM products WHERE id = ?";
        $stmt = $con->prepare($delete_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        // Modyfikowanie produktu
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];

        $update_query = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ? WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("ssdii", $name, $description, $price, $category_id, $product_id);
        $stmt->execute();

        // Aktualizacja zdjęcia, jeśli zostało przesłane
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_blob = file_get_contents($_FILES['image']['tmp_name']);
            $update_image_query = "UPDATE products SET image_path = ? WHERE id = ?";
            $stmt_image = $con->prepare($update_image_query);
            $stmt_image->bind_param("si", $image_blob, $product_id);
            $stmt_image->send_long_data(0, $image_blob);
            $stmt_image->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modyfikacja Produktów - Sklep Internetowy</title>
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
                <li class="nav-item"><a class="nav-link" href="manage_products.php">Zarządzanie produktami</a></li>
            </ul>
        </div>
    </div>

    <!-- Edit/Delete Product Section -->
    <section class="py-5">
        <div class="container shadow-lg p-5 bg-white rounded">
            <h2 class="text-center mb-4 text-uppercase text-primary">Modyfikacja Produktów</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Produktu</th>
                            <th>Nazwa</th>
                            <th>Kategoria</th>
                            <th>Cena</th>
                            <th>Opis</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Pobranie danych z bazy
                        $sql = "SELECT products.id, products.name, products.description, categories.name AS category_name, products.price, products.category_id 
                                FROM products
                                JOIN categories ON products.category_id = categories.id";
                        $result = $con->query($sql);

                        // Wyświetlenie produktów
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                echo "<td>" . number_format($row['price'], 2) . " zł</td>";
                                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                echo "<td>
                                        <form method='POST' class='d-inline' enctype='multipart/form-data'>
                                            <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                                            <button type='button' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editModal" . $row['id'] . "'>Edytuj</button>
                                            <button type='submit' name='delete' class='btn btn-sm btn-danger'>Usuń</button>
                                        </form>
                                    </td>";
                                echo "</tr>";

                                // Modal do edycji produktu
                                echo "
                                <div class='modal fade' id='editModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editModalLabel'>Edytuj produkt</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <form method='POST' enctype='multipart/form-data'>
                                                <div class='modal-body'>
                                                    <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                                                    <div class='mb-3'>
                                                        <label for='name' class='form-label'>Nazwa produktu:</label>
                                                        <input type='text' id='name' name='name' value='" . htmlspecialchars($row['name']) . "' class='form-control' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='description' class='form-label'>Opis produktu:</label>
                                                        <textarea id='description' name='description' class='form-control' rows='4' required>" . htmlspecialchars($row['description']) . "</textarea>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='price' class='form-label'>Cena produktu:</label>
                                                        <input type='number' id='price' name='price' step='0.01' value='" . number_format($row['price'], 2) . "' class='form-control' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='category_id' class='form-label'>Kategoria:</label>
                                                        <select id='category_id' name='category_id' class='form-select' required>
                                                            <option value=''>-- Wybierz kategorię --</option>";
                        $categories_query = "SELECT id, name FROM categories ORDER BY name";
                        $categories_result = mysqli_query($con, $categories_query);
                        while($cat = mysqli_fetch_assoc($categories_result)) {
                            echo "<option value='" . htmlspecialchars($cat['id']) . "'" . ($cat['id'] == $row['category_id'] ? " selected" : "") . ">" . htmlspecialchars($cat['name']) . "</option>";
                        }
                        echo "
                                                        </select>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='image' class='form-label'>Zdjęcie produktu:</label>
                                                        <input type='file' id='image' name='image' accept='image/jpeg, image/png, image/webp' class='form-control'>
                                                    </div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Anuluj</button>
                                                    <button type='submit' name='edit' class='btn btn-primary'>Zapisz zmiany</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Brak produktów do wyświetlenia.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
