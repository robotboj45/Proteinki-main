<?php
session_start();
include("../backend/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: account/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = mysqli_prepare($con, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);

$order_users_query = "SELECT order_id FROM order_users WHERE user_id = ?";
$order_users_stmt = mysqli_prepare($con, $order_users_query);
mysqli_stmt_bind_param($order_users_stmt, "i", $user_id);
mysqli_stmt_execute($order_users_stmt);
$order_users_result = mysqli_stmt_get_result($order_users_stmt);
$order_ids = [];
while ($row = mysqli_fetch_assoc($order_users_result)) {
    $order_ids[] = $row['order_id'];
}

$orders = [];
if (!empty($order_ids)) {
    $order_ids_placeholder = implode(',', array_fill(0, count($order_ids), '?'));
    $types = str_repeat('i', count($order_ids));
    $orders_query = "SELECT * FROM orders WHERE id IN ($order_ids_placeholder) ORDER BY created_at DESC";
    $orders_stmt = mysqli_prepare($con, $orders_query);
    mysqli_stmt_bind_param($orders_stmt, $types, ...$order_ids);
    mysqli_stmt_execute($orders_stmt);
    $orders_result = mysqli_stmt_get_result($orders_stmt);
    while ($order = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $order;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Użytkownika - Sklep Internetowy</title>
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
        <h1 class="text-center mb-4 text-uppercase text-primary">Panel Użytkownika</h1>
        
        <div class="section">
            <h4 class="mb-3">Dane Użytkownika</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>ID:</strong> <?php echo htmlspecialchars($user['user_id']); ?></li>
                <li class="list-group-item"><strong>Nazwa Użytkownika:</strong> <?php echo htmlspecialchars($user['user_name']); ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['user_email']); ?></li>
                <li class="list-group-item"><strong>Telefon:</strong> <?php echo htmlspecialchars($user['mobile']); ?></li>
                <li class="list-group-item"><strong>Grupa Użytkownika:</strong> <?php echo htmlspecialchars($user['user_group']); ?></li>
                <li class="list-group-item"><strong>Data Rejestracji:</strong> <?php echo htmlspecialchars($user['date']); ?></li>
            </ul>
        </div>

        <div class="section">
            <h4 class="mb-3">Historia Zamówień</h4>
            <?php if (!empty($orders)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Zamówienia</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Razem</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo number_format($order['total'], 2, ',', ' '); ?> zł</td>
                            <td><a href="order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-primary btn-sm">Szczegóły</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nie masz jeszcze żadnych zamówień.</p>
            <?php endif; ?>
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
