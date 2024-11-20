<?php
session_start();
include("../backend/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: account/login.html");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: user_panel.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

$order_query = "SELECT o.* FROM orders o JOIN order_users ou ON o.id = ou.order_id WHERE o.id = ? AND ou.user_id = ?";
$order_stmt = mysqli_prepare($con, $order_query);
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: user_panel.php");
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
    <title>Szczegóły Zamówienia - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .section {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../index.php"><img src="../img/logo.png" alt="Logo Sklepu" class="logo"></a>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Strona główna</a></li>
                    <li class="nav-item"><a class="nav-link" href="../policy/privacy.php">Polityka prywatności</a></li>
                    <li class="nav-item"><a class="nav-link" href="../policy/terms.php">Regulamin</a></li>
                    <li class="nav-item"><a class="nav-link" href="../cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_panel.php">Panel Użytkownika</a></li>
                    <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Wyloguj się</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="container my-5 shadow-lg p-5 bg-white rounded">
        <h1 class="text-center mb-4 text-uppercase text-primary">Szczegóły Zamówienia</h1>
        
        <div class="section">
            <h4 class="mb-3">Dane Zamówienia</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>ID Zamówienia:</strong> <?php echo htmlspecialchars($order['id']); ?></li>
                <li class="list-group-item"><strong>Data:</strong> <?php echo htmlspecialchars($order['created_at']); ?></li>
                <li class="list-group-item"><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></li>
                <li class="list-group-item"><strong>Razem:</strong> <?php echo number_format($order['total'], 2, ',', ' '); ?> zł</li>
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
            <a href="user_panel.php" class="btn btn-primary">Powrót do Panelu Użytkownika</a>
        </div>
    </section>

    <footer class="footer bg-dark text-white py-5 mt-auto">
        <div class="container text-center">
            <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a class="text-white" href="../policy/privacy.php">Polityka prywatności</a></li>
                <li class="list-inline-item"><a class="text-white" href="../policy/terms.php">Regulamin sklepu</a></li>
                <li class="list-inline-item"><a class="text-white" href="../about/about.php">O nas</a></li>
            </ul>
            <div class="social-icons mt-3">
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-twitter fa-2x"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
