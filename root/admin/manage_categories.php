<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_group =  $_SESSION['user_group'];
if ($user_group != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Obsługa usuwania kategorii
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM categories WHERE id = ?";
    $stmt = $con->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: manage_categories.php"); 
    } else {
        echo "Błąd podczas usuwania kategorii: " . $con->error;
    }
    $stmt->close();
}

// Obsługa edycji kategorii
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $sql_edit = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $con->prepare($sql_edit);
    $stmt->bind_param("si", $category_name, $category_id);
    if ($stmt->execute()) {
        header("Location: manage_categories.php"); 
    } else {
        echo "Błąd podczas edycji kategorii: " . $con->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie Produktami - Sklep Internetowy</title>
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
            <li class="nav-item"><a class="nav-link" href="manage_products.php">Zarządzanie produktami</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_orders.html">Zarządzanie zamówieniami</a></li>
            <li class="nav-item"><a class="nav-link" href="add_product.php">Dodawanie produktu</a></li>
        </ul>
    </div>
</div>

<!-- Manage Products Section -->
<section class="py-5">
    <div class="container shadow-lg p-5 bg-white rounded">
        <h2 class="text-center mb-4 text-uppercase text-primary">Zarządzanie Kategoriami</h2>
        <p class="lead text-center">Lista kategorii dostępnych w sklepie.</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Kategorii</th>
                        <th>Nazwa</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Pobranie danych z bazy
                    $sql = "SELECT id, name FROM categories";
                    $result = $con->query($sql);

                    // Wyświetlenie kategorii
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>
                                    <a href='#' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editModal" . $row['id'] . "'>Edytuj</a>
                                    <a href='?delete_id=" . $row['id'] . "' class='btn btn-sm btn-danger'>Usuń</a>
                                  </td>";
                            echo "</tr>";
                            // Modal do edycji kategorii
                            echo "
                                <div class='modal fade' id='editModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editModalLabel'>Edytuj kategorię</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <form method='POST'>
                                                    <input type='hidden' name='category_id' value='" . $row['id'] . "'>
                                                    <div class='mb-3'>
                                                        <label for='category_name' class='form-label'>Nazwa kategorii</label>
                                                        <input type='text' class='form-control' name='category_name' value='" . $row['name'] . "' required>
                                                    </div>
                                                    <button type='submit' name='edit_category' class='btn btn-primary'>Zapisz zmiany</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Brak kategorii do wyświetlenia.</td></tr>";
                    }

                    // Zamknięcie połączenia z bazą
                    $con->close();
                    ?>
                </tbody>
            </table>
        </div>
        <a href="add_category.php" class="btn btn-success mt-4">Dodaj nową kategorię</a>
        <a href="dashboard.html" class="btn btn-success mt-4">Powrót</a>
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
