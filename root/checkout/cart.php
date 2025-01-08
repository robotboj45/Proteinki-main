<?php
session_start();
include("../backend/connection.php");

$message = "";
$discount = 0;  // Domyślnie brak rabatu

// Dodawanie produktu do koszyka
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        $product_query = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($con, $product_query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            $_SESSION['cart'][$product_id] = array(
                "id" => $product['id'],
                "name" => $product['name'],
                "price" => $product['price'],
                "quantity" => 1
            );
        } else {
            header("Location: ../index.php");
            exit();
        }
    }
    header("Location: cart.php");
    exit();
}

// Usuwanie produktu z koszyka
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

// Usunięcie kodu rabatowego
if (isset($_GET['action']) && $_GET['action'] == 'remove_discount') {
    unset($_SESSION['discount']);  // Usunięcie rabatu
    $message = "Kod rabatowy został usunięty.";
    header("Location: cart.php?message=" . urlencode($message));
    exit();
}

// Obsługa formularza z aktualizacją ilości oraz kodu rabatowego
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        if (isset($_SESSION['cart'])) {
            foreach ($_POST['quantities'] as $product_id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity < 1) {
                    $_SESSION['cart'][$product_id]['quantity'] = 1;
                    $message = "Nieprawidłowa ilość dla produktu ID $product_id. Ilość została ustawiona na 1.";
                } else {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                }
            }
        }
    }

    // Obsługa kodu rabatowego
    if (isset($_POST['discount_code']) && $_POST['discount_code'] === 'rabat') {
        $_SESSION['discount'] = 0.1;  // Zapisz rabat 10%
        $message = "Kod rabatowy zastosowany! Zniżka 10%";
    }

    header("Location: cart.php" . ($message ? "?message=" . urlencode($message) : ""));
    exit();
}

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .quantity-column {
            width: 100px;
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
                    <a class="nav-link text-white" href="../policy/privacy.html">Polityka prywatności</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../policy/terms.html">Regulamin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<section class="container my-5 shadow-lg p-5 bg-white rounded">
    <h1 class="text-center mb-4 text-uppercase text-primary">Twój koszyk</h1>
    <h2 class="text-center my-4">
        <span class="text-danger">Tylko teraz!</span> Użyj kodu <span class="text-danger">"rabat"</span> by otrzymać <span class="text-danger">10% zniżki</span> na całe zamówienie!
    </h2>

    <?php if ($message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <form method="post" action="cart.php">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena</th>
                    <th class="quantity-column">Ilość</th>
                    <th>Razem</th>
                    <th>Akcja</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price'], 2, ',', ' '); ?> zł</td>
                        <td class="text-center">
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-sm" required>
                        </td>
                        <td><?php echo number_format($subtotal, 2, ',', ' '); ?> zł</td>
                        <td>
                            <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php
                // Zastosowanie rabatu, jeśli został zapisany w sesji
                if (isset($_SESSION['discount']) && $_SESSION['discount'] > 0) {
                    $discount_amount = $total * $_SESSION['discount']; // Zastosowanie rabatu
                    $total -= $discount_amount;
                }
                ?>

                <tr>
                    <td colspan="3" class="text-end fw-bold">Suma całkowita</td>
                    <td colspan="2" class="fw-bold"><?php echo number_format($total, 2, ',', ' '); ?> zł</td>
                </tr>
                </tbody>
            </table>

            <?php if (isset($_SESSION['discount'])): ?>
                <div class="alert alert-info">
                    <p>Kod rabatowy: <strong>rabat</strong> zastosowany. Zniżka 10%!</p>
                    <a href="cart.php?action=remove_discount" class="btn btn-danger">Usuń kod rabatowy</a>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="discount_code" class="form-label">Kod rabatowy:</label>
                <input type="text" class="form-control" id="discount_code" name="discount_code" placeholder="Wpisz kod rabatowy">
                <button type="submit" name="update_cart" class="btn btn-primary mt-2">Zastosuj kod rabatowy</button>
            </div>

            <div class="text-end mb-3">
                <button type="submit" name="update_cart" class="btn btn-primary">Aktualizuj ilości</button>
            </div>
        </form>

        <div class="text-end">
            <a href="../index.php" class="btn btn-secondary">Kontynuuj zakupy</a>
            <a href="checkout.php" class="btn btn-success">Przejdź do kasy</a>
        </div>
    <?php else: ?>
        <div class="text-center">
            <p class="lead">Twój koszyk jest pusty.</p>
            <a href="../index.php" class="btn btn-secondary btn-lg mt-3">Kontynuuj zakupy</a>
        </div>
    <?php endif; ?>
</section>

<footer class="footer bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
        <p>&copy; 2024 Sklep z Suplementami. Wszelkie prawa zastrzeżone.</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a class="text-white" href="../policy/privacy.html">Polityka prywatności</a></li>
            <li class="list-inline-item"><a class="text-white" href="../policy/terms.html">Regulamin sklepu</a></li>
            <li class="list-inline-item"><a class="text-white" href="../about/about.html">O nas</a></li>
        </ul>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
