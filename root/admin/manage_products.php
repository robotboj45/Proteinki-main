<?php
session_start();
include("../backend/connection.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_group = $_SESSION['user_group'];
if ($user_group != 'admin') {
    header('Location: ../index.php');
    exit; // Ważne, aby zatrzymać dalsze wykonywanie kodu
}

// Obsługa usuwania produktu
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM products WHERE id = ?";
    $stmt = $con->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: manage_products.php"); // Przekierowanie po usunięciu
    } else {
        echo "Błąd podczas usuwania produktu: " . $con->error;
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
        <h2 class="text-center mb-4 text-uppercase text-primary">Zarządzanie Produktami</h2>
        <p class="lead text-center">Lista produktów dostępnych w sklepie.</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Produktu</th>
                        <th>Nazwa</th>
                        <th>Kategoria</th>
                        <th>Cena</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Pobranie danych z bazy
                    $sql = "SELECT products.id, products.name, categories.name AS category_name, products.price, products.category_id
                            FROM products
                            JOIN categories ON products.category_id = categories.id";
                    $result = $con->query($sql);

                    // Wyświetlenie produktów
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['category_name'] . "</td>";
                            echo "<td>" . number_format($row['price'], 2) . " zł</td>";
                            echo "<td>
                                <a href='edit_product.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edytuj</a>
                                <a href='?delete_id=" . $row['id'] . "' class='btn btn-sm btn-danger'>Usuń</a>
                                </td>";

                            echo "</tr>";

                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Brak produktów do wyświetlenia.</td></tr>";
                    }

                    $con->close();
                    ?>
                </tbody>
            </table>
        </div>
        <a href="add_product.php" class="btn btn-success mt-4">Dodaj Nowy Produkt</a>
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
