<?php
session_start();
include("../backend/connection.php");
$message = "";
$discount = 0;

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

if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['quantity'])) {
        $product_id = (int)$_POST['id'];
        $quantity = max(1, (int)$_POST['quantity']);

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }

        $subtotal = $_SESSION['cart'][$product_id]['price'] * $quantity;
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if (isset($_SESSION['discount']) && $_SESSION['discount'] > 0) {
            $total -= $total * $_SESSION['discount'];
        }

        echo json_encode([
            'subtotal' => number_format($subtotal, 2, ',', ' '),
            'total' => number_format($total, 2, ',', ' ')
        ]);
        exit();
    }

    if (isset($_POST['discount_code'])) {
        if (strtolower($_POST['discount_code']) === 'rabat10') {
            $_SESSION['discount'] = 0.1;
            $message = "Kod rabatowy zastosowany! Zniżka 10%";
        } else {
            $message = "Nieprawidłowy kod rabatowy.";
        }
        header("Location: cart.php" . ($message ? "?message=" . urlencode($message) : ""));
        exit();
    }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
<header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../index.php">
            <img src="../img/logo.png" alt="Logo Sklepu" class="logo" style="width: 15%;">
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
    <div class="mb-4 text-center bg-danger text-white p-2 rounded">
        Skorzystaj z kodu rabatowego "Rabat10", by uzyskać rabat 10%
    </div>
    <h1 class="text-center mb-4 text-uppercase text-primary">Twój koszyk</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Produkt</th>
                <th>Cena</th>
                <th>Ilość</th>
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
                    <td>
                        <input type="number" class="quantity-input" data-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1">
                    </td>
                    <td class="subtotal" data-id="<?php echo $item['id']; ?>"><?php echo number_format($subtotal, 2, ',', ' '); ?> zł</td>
                    <td>
                        <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="5">
                    <form method="post" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="discount_code" id="rabatText" class="form-control" placeholder="Wprowadź kod rabatowy" value="<?php echo isset($_SESSION['discount']) && $_SESSION['discount'] > 0 ? 'Rabat10' : ''; ?>" <?php echo isset($_SESSION['discount']) && $_SESSION['discount'] > 0 ? 'disabled' : ''; ?>>
                            <button type="submit" class="btn btn-primary" <?php echo isset($_SESSION['discount']) && $_SESSION['discount'] > 0 ? 'disabled' : ''; ?>>Zastosuj</button>
                        </div>
                    </form>
                </td>
            </tr>

            <?php
            if (isset($_SESSION['discount']) && $_SESSION['discount'] > 0) {
                $discount_amount = $total * $_SESSION['discount'];
                $total -= $discount_amount;
            }
            ?>

            <tr>
                <td colspan="3" class="text-end fw-bold">Suma całkowita</td>
                <td colspan="2" class="fw-bold" id="total-amount"><?php echo number_format($total, 2, ',', ' '); ?> zł</td>
            </tr>
            </tbody>
        </table>

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

<script>
$(document).ready(function() {
    $('.quantity-input').on('change', function() {
        const id = $(this).data('id');
        const quantity = $(this).val();

        $.ajax({
            url: 'cart.php',
            method: 'POST',
            data: { id: id, quantity: quantity },
            success: function(response) {
                const data = JSON.parse(response);
                $(`.subtotal[data-id="${id}"]`).text(data.subtotal + ' zł');
                $('#total-amount').text(data.total + ' zł');
            }
        });
    });
});
</script>
</body>
</html>
