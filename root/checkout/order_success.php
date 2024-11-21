<?php
include("../backend/connection.php");

if (!isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

$order_query = "SELECT * FROM orders WHERE id = ?";
$order_stmt = mysqli_prepare($con, $order_query);
mysqli_stmt_bind_param($order_stmt, "i", $order_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: ../index.php");
    exit();
}

$order_product_query = "SELECT * FROM order_product WHERE order_id = ?";
$order_product_stmt = mysqli_prepare($con, $order_product_query);
mysqli_stmt_bind_param($order_product_stmt, "i", $order_id);
mysqli_stmt_execute($order_product_stmt);
$order_product_result = mysqli_stmt_get_result($order_product_stmt);
$order_products = mysqli_fetch_all($order_product_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamówienie Zakończone Sukcesem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .section {
            margin-bottom: 2rem;
        }
        .is-invalid + .invalid-feedback {
            display: block;
        }
        .success-icon {
            font-size: 3rem;
            color: green;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../index.php">
                <img src="../img/logo.png" alt="Logo Sklepu" class="logo">
            </a>
            <nav>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../index.php">Strona główna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../policy/privacy.php">Polityka prywatności</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../policy/terms.php">Regulamin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="container my-5 shadow-lg p-5 bg-white rounded text-center">
        <div class="mb-4">
            <span class="success-icon">&#10004;</span>
        </div>
        <h1 class="text-center mb-4 text-uppercase text-success">Zamówienie Zakończone Sukcesem</h1>
        <p>Dziękujemy za zakupy! Twoje zamówienie zostało pomyślnie złożone.</p>
        <p>Numer zamówienia: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>

        <div class="section">
            <h4 class="mb-3">Dane Klienta</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>Imię:</strong> <?php echo htmlspecialchars($order['first_name']); ?></li>
                <li class="list-group-item"><strong>Nazwisko:</strong> <?php echo htmlspecialchars($order['last_name']); ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></li>
                <li class="list-group-item"><strong>Telefon:</strong> <?php echo htmlspecialchars($order['phone']); ?></li>
                <li class="list-group-item"><strong>Adres:</strong> <?php echo htmlspecialchars($order['voivodeship'] . ", " . $order['postal_code'] . ", " . $order['city'] . ", " . $order['street'] . " " . $order['house_number'] . ($order['apartment_number'] ? "/" . $order['apartment_number'] : "")); ?></li>
                <li class="list-group-item"><strong>Metoda wysyłki:</strong> <?php echo htmlspecialchars($order['shipping_method']); ?></li>
                <li class="list-group-item"><strong>Metoda płatności:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></li>
            </ul>
        </div>

        <div class="section">
            <h4 class="mb-3">Produkty w Zamówieniu</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Cena</th>
                        <th>Ilość</th>
                        <th>Razem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo number_format($product['price'], 2, ',', ' '); ?> zł</td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo number_format($product['price'] * $product['quantity'], 2, ',', ' '); ?> zł</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Suma całkowita</td>
                        <td class="fw-bold"><?php echo number_format($order['total'], 2, ',', ' '); ?> zł</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section text-center">
            <a href="../index.php" class="btn btn-primary">Powrót do sklepu</a>
        </div>
    </section>

    <footer class="footer bg-dark text-white py-4 mt-auto">
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
