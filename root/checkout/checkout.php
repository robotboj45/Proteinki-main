<?php
session_start();
include("../backend/connection.php");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $voivodeship = trim($_POST['voivodeship']);
    $postal_code = trim($_POST['postal_code']);
    $city = trim($_POST['city']);
    $street = trim($_POST['street']);
    $house_number = trim($_POST['house_number']);
    $apartment_number = trim($_POST['apartment_number']);
    $shipping_method = trim($_POST['shipping_method']);
    $payment_method = trim($_POST['payment_method']);
    $consent_data = isset($_POST['consent_data']) ? 1 : 0;
    $consent_terms = isset($_POST['consent_terms']) ? 1 : 0;

    $card_number = null;
    $expiry_date = null;
    $cvv = null;

    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($voivodeship) || empty($postal_code) || empty($city) || empty($street) || empty($house_number) || empty($shipping_method) || empty($payment_method) || !$consent_data || !$consent_terms) {
        $message = "Wszystkie pola muszą być wypełnione, a zgody zaznaczone.";
    } elseif (!preg_match('/^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/', $first_name) || !preg_match('/^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/', $last_name)) {
        $message = "Imię i nazwisko muszą zaczynać się od dużej litery.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Nieprawidłowy adres email.";
    } elseif (!preg_match('/^\+48\s\d{3}\s\d{3}\s\d{3}$/', $phone)) {
        $message = "Numer telefonu musi mieć format +48 111 222 333.";
    } elseif (!preg_match('/^\d{2}-\d{3}$/', $postal_code)) {
        $message = "Kod pocztowy musi mieć format XX-XXX.";
    } else {
        if ($payment_method === 'karta') {
            $card_number = trim($_POST['card_number']);
            $expiry_date = trim($_POST['expiry_date']);
            $cvv = trim($_POST['cvv']);

            if (empty($card_number) || empty($expiry_date) || empty($cvv)) {
                $message = "Wszystkie dane karty kredytowej muszą być wypełnione.";
            } elseif (!preg_match('/^\d{16}$/', $card_number)) {
                $message = "Numer karty kredytowej musi zawierać 16 cyfr.";
            } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
                $message = "Data ważności musi mieć format MM/YY.";
            } else {
                $current_year = date('y');
                $current_month = date('m');
                list($exp_month, $exp_year) = explode('/', $expiry_date);
                if ($exp_year < $current_year || ($exp_year == $current_year && $exp_month < $current_month)) {
                    $message = "Data ważności karty kredytowej jest nieprawidłowa.";
                } elseif (!preg_match('/^\d{3}$/', $cvv)) {
                    $message = "CVV musi zawierać 3 cyfry.";
                }
            }
        }

        if (empty($message)) {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            if (isset($_SESSION['discount']) && $_SESSION['discount'] > 0) {
                $total -= $total * $_SESSION['discount'];
            }
            $order_query = "INSERT INTO orders (first_name, last_name, email, phone, voivodeship, postal_code, city, street, house_number, apartment_number, shipping_method, payment_method, consent_data, consent_terms, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $order_query);
            mysqli_stmt_bind_param($stmt, "sssssssssssiiid", $first_name, $last_name, $email, $phone, $voivodeship, $postal_code, $city, $street, $house_number, $apartment_number, $shipping_method, $payment_method, $consent_data, $consent_terms, $total);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($con);
            foreach ($_SESSION['cart'] as $item) {
                $item_query = "INSERT INTO order_product (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
                $item_stmt = mysqli_prepare($con, $item_query);
                mysqli_stmt_bind_param($item_stmt, "iisdi", $order_id, $item['id'], $item['name'], $item['price'], $item['quantity']);
                mysqli_stmt_execute($item_stmt);
            }
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $order_users_query = "INSERT INTO order_users (user_id, order_id) VALUES (?, ?)";
                $order_users_stmt = mysqli_prepare($con, $order_users_query);
                if ($order_users_stmt) {
                    mysqli_stmt_bind_param($order_users_stmt, "si", $user_id, $order_id);
                    mysqli_stmt_execute($order_users_stmt);
                    mysqli_stmt_close($order_users_stmt);
                } else {
                    $message = "Wystąpił problem z powiązaniem zamówienia z użytkownikiem.";
                }
            }
            $_SESSION['cart'] = array();
            unset($_SESSION['discount']);
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
        }
        header("Location: checkout.php" . ($message ? "?message=" . urlencode($message) : ""));
        exit();
    }

    if (isset($_GET['message'])) {
        $message = htmlspecialchars($_GET['message']);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasa - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .section {
            margin-bottom: 2rem;
        }
        .is-invalid + .invalid-feedback {
            display: block;
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

    <section class="container my-5 shadow-lg p-5 bg-white rounded">
        <h1 class="text-center mb-4 text-uppercase text-primary">Kasa</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form method="post" action="checkout.php" id="checkoutForm">
            <div class="section">
                <h4 class="mb-3">Produkty w zamówieniu</h4>
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
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price'], 2, ',', ' '); ?> zł</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($subtotal, 2, ',', ' '); ?> zł</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php
                        if (isset($_SESSION['discount']) && $_SESSION['discount'] > 0) {
                            $discount_amount = $total * $_SESSION['discount'];
                            $total -= $discount_amount;
                        }
                        ?>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Suma całkowita</td>
                            <td class="fw-bold"><?php echo number_format($total, 2, ',', ' '); ?> zł</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h4 class="mb-3">Dane klienta</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">Imię <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                        <div class="invalid-feedback">
                            Imię musi zaczynać się od dużej litery i zawierać tylko litery.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Nazwisko <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                        <div class="invalid-feedback">
                            Nazwisko musi zaczynać się od dużej litery i zawierać tylko litery.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Adres Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Wprowadź prawidłowy adres email.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Numer Telefonu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="+48 111 222 333" required>
                        <div class="invalid-feedback">
                            Numer telefonu musi mieć format +48 111 222 333.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h4 class="mb-3">Adres wysyłki</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="voivodeship" class="form-label">Województwo <span class="text-danger">*</span></label>
                        <select class="form-select" id="voivodeship" name="voivodeship" required>
                            <option value="" selected disabled>Wybierz województwo</option>
                            <option value="dolnośląskie">Dolnośląskie</option>
                            <option value="kujawsko-pomorskie">Kujawsko-Pomorskie</option>
                            <option value="lubelskie">Lubelskie</option>
                            <option value="lubuskie">Lubuskie</option>
                            <option value="łódzkie">Łódzkie</option>
                            <option value="małopolskie">Małopolskie</option>
                            <option value="mazowieckie">Mazowieckie</option>
                            <option value="opolskie">Opolskie</option>
                            <option value="podkarpackie">Podkarpackie</option>
                            <option value="podlaskie">Podlaskie</option>
                            <option value="pomorskie">Pomorskie</option>
                            <option value="śląskie">Śląskie</option>
                            <option value="świętokrzyskie">Świętokrzyskie</option>
                            <option value="warmińsko-mazurskie">Warmińsko-Mazurskie</option>
                            <option value="wielkopolskie">Wielkopolskie</option>
                            <option value="zachodniopomorskie">Zachodniopomorskie</option>
                        </select>
                        <div class="invalid-feedback">
                            Wybierz województwo.
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="postal_code" class="form-label">Kod pocztowy <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="XX-XXX" maxlength="6" required>
                        <div class="invalid-feedback">
                            Kod pocztowy musi mieć format XX-XXX.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="city" class="form-label">Miasto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" required>
                        <div class="invalid-feedback">
                            Wprowadź miasto.
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="street" class="form-label">Ulica <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="street" name="street" required>
                        <div class="invalid-feedback">
                            Wprowadź ulicę.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="house_number" class="form-label">Numer domu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="house_number" name="house_number" required>
                        <div class="invalid-feedback">
                            Wprowadź numer domu.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="apartment_number" class="form-label">Numer mieszkania</label>
                        <input type="text" class="form-control" id="apartment_number" name="apartment_number">
                        <div class="invalid-feedback">
                            Wprowadź numer mieszkania.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="shipping_method" class="form-label">Metoda wysyłki <span class="text-danger">*</span></label>
                        <select class="form-select" id="shipping_method" name="shipping_method" required>
                            <option value="" selected disabled>Wybierz metodę wysyłki</option>
                            <option value="Inpost">Inpost</option>
                            <option value="DPD">DPD</option>
                            <option value="DHL">DHL</option>
                            <option value="Poczta Polska">Poczta Polska</option>
                        </select>
                        <div class="invalid-feedback">
                            Wybierz metodę wysyłki.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h4 class="mb-3">Metoda płatności</h4>
                <select class="form-select" name="payment_method" id="payment_method" required>
                    <option value="" selected disabled>Wybierz metodę płatności</option>
                    <option value="przelew">Przelew bankowy</option>
                    <option value="karta">Karta kredytowa</option>
                    <option value="paypal">PayPal</option>
                    <option value="za_pobraniem">Za pobraniem</option>
                </select>
                <div class="invalid-feedback">
                    Wybierz metodę płatności.
                </div>
            </div>

            <div class="section" id="creditCardFields" style="display: none;">
                <h4 class="mb-3">Dane karty kredytowej</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="card_number" class="form-label">Numer karty <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="card_number" name="card_number" maxlength="16">
                        <div class="invalid-feedback">
                            Numer karty kredytowej musi zawierać 16 cyfr.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="expiry_date" class="form-label">Data ważności (MM/YY) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                        <div class="invalid-feedback">
                            Data ważności musi mieć format MM/YY i być w przyszłości.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="cvv" class="form-label">CVV <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3">
                        <div class="invalid-feedback">
                            CVV musi zawierać 3 cyfry.
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="section">
                <h4 class="mb-3">Zgody</h4>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="consent_data" name="consent_data" required>
                    <label class="form-check-label" for="consent_data">
                        Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z <a href="../policy/privacy.php" target="_blank">Polityką prywatności</a> <span class="text-danger">*</span>.
                    </label>
                    <div class="invalid-feedback">
                        Musisz wyrazić zgodę na przetwarzanie danych.
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="consent_terms" name="consent_terms" required>
                    <label class="form-check-label" for="consent_terms">
                        Akceptuję <a href="../policy/terms.php" target="_blank">Regulamin sklepu</a> <span class="text-danger">*</span>.
                    </label>
                    <div class="invalid-feedback">
                        Musisz zaakceptować regulamin.
                    </div>
                </div>
            </div>
            
            <div class="section text-end">
                <button type="submit" name="place_order" class="btn btn-success">Złóż zamówienie</button>
                <a href="cart.php" class="btn btn-secondary">Wróć do koszyka</a>
            </div>
        </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkoutForm');
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const postalCode = document.getElementById('postal_code');
            const voivodeship = document.getElementById('voivodeship');
            const city = document.getElementById('city');
            const street = document.getElementById('street');
            const houseNumber = document.getElementById('house_number');
            const payment_method = document.getElementById('payment_method');
            const shippingMethod = document.getElementById('shipping_method');
            const consentData = document.getElementById('consent_data');
            const consentTerms = document.getElementById('consent_terms');
            const creditCardFields = document.getElementById('creditCardFields');
            const cardNumber = document.getElementById('card_number');
            const expiryDate = document.getElementById('expiry_date');
            const cvv = document.getElementById('cvv');

            function toggleCreditCardFields() {
                if (payment_method.value === 'karta') {
                    creditCardFields.style.display = 'block';
                } else {
                    creditCardFields.style.display = 'none';
                    cardNumber.classList.remove('is-invalid');
                    expiryDate.classList.remove('is-invalid');
                    cvv.classList.remove('is-invalid');
                }
            }

            function validateFirstName() {
                const regex = /^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/;
                if (!regex.test(firstName.value.trim())) {
                    firstName.classList.add('is-invalid');
                    return false;
                } else {
                    firstName.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateLastName() {
                const regex = /^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/;
                if (!regex.test(lastName.value.trim())) {
                    lastName.classList.add('is-invalid');
                    return false;
                } else {
                    lastName.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateEmail() {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regex.test(email.value.trim())) {
                    email.classList.add('is-invalid');
                    return false;
                } else {
                    email.classList.remove('is-invalid');
                    return true;
                }
            }

            function validatePhone() {
                const regex = /^\+48\s\d{3}\s\d{3}\s\d{3}$/;
                if (!regex.test(phone.value.trim())) {
                    phone.classList.add('is-invalid');
                    return false;
                } else {
                    phone.classList.remove('is-invalid');
                    return true;
                }
            }

            function validatePostalCode() {
                const regex = /^\d{2}-\d{3}$/;
                if (!regex.test(postalCode.value.trim())) {
                    postalCode.classList.add('is-invalid');
                    return false;
                } else {
                    postalCode.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateVoivodeship() {
                if (voivodeship.value.trim() === '') {
                    voivodeship.classList.add('is-invalid');
                    return false;
                } else {
                    voivodeship.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateCity() {
                if (city.value.trim() === '') {
                    city.classList.add('is-invalid');
                    return false;
                } else {
                    city.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateStreet() {
                if (street.value.trim() === '') {
                    street.classList.add('is-invalid');
                    return false;
                } else {
                    street.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateHouseNumber() {
                if (houseNumber.value.trim() === '') {
                    houseNumber.classList.add('is-invalid');
                    return false;
                } else {
                    houseNumber.classList.remove('is-invalid');
                    return true;
                }
            }

            function validatePayment() {
                if (payment_method.value.trim() === '') {
                    payment_method.classList.add('is-invalid');
                    return false;
                } else {
                    payment_method.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateShippingMethod() {
                if (shippingMethod.value.trim() === '') {
                    shippingMethod.classList.add('is-invalid');
                    return false;
                } else {
                    shippingMethod.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateCardNumber() {
                const regex = /^\d{16}$/;
                if (payment_method.value === 'karta') {
                    if (!regex.test(cardNumber.value.trim())) {
                        cardNumber.classList.add('is-invalid');
                        return false;
                    } else {
                        cardNumber.classList.remove('is-invalid');
                        return true;
                    }
                }
                return true;
            }

            function validateExpiryDate() {
                const regex = /^(0[1-9]|1[0-2])\/\d{2}$/;
                if (payment_method.value === 'karta') {
                    if (!regex.test(expiryDate.value.trim())) {
                        expiryDate.classList.add('is-invalid');
                        return false;
                    } else {
                        const today = new Date();
                        const current_year = today.getFullYear() % 100;
                        const current_month = today.getMonth() + 1;
                        const [exp_month, exp_year] = expiryDate.value.split('/');
                        if (parseInt(exp_year) < current_year || (parseInt(exp_year) === current_year && parseInt(exp_month) < current_month)) {
                            expiryDate.classList.add('is-invalid');
                            return false;
                        }
                        expiryDate.classList.remove('is-invalid');
                        return true;
                    }
                }
                return true;
            }

            function validateCVV() {
                const regex = /^\d{3}$/;
                if (payment_method.value === 'karta') {
                    if (!regex.test(cvv.value.trim())) {
                        cvv.classList.add('is-invalid');
                        return false;
                    } else {
                        cvv.classList.remove('is-invalid');
                        return true;
                    }
                }
                return true;
            }

            firstName.addEventListener('input', validateFirstName);
            lastName.addEventListener('input', validateLastName);
            email.addEventListener('input', validateEmail);
            phone.addEventListener('input', validatePhone);
            postalCode.addEventListener('input', function(e) {
                let value = postalCode.value.replace(/-/g, '');
                if (value.length > 2) {
                    value = value.slice(0,2) + '-' + value.slice(2,5);
                }
                postalCode.value = value;
                validatePostalCode();
            });
            voivodeship.addEventListener('change', validateVoivodeship);
            city.addEventListener('input', validateCity);
            street.addEventListener('input', validateStreet);
            houseNumber.addEventListener('input', validateHouseNumber);
            payment_method.addEventListener('change', function() {
                validatePayment();
                toggleCreditCardFields();
                validateCardNumber();
                validateExpiryDate();
                validateCVV();
            });
            shippingMethod.addEventListener('change', validateShippingMethod);
            cardNumber.addEventListener('input', validateCardNumber);
            expiryDate.addEventListener('input', validateExpiryDate);
            cvv.addEventListener('input', validateCVV);

            form.addEventListener('submit', function(e) {
                let valid = true;
                if (!validateFirstName()) valid = false;
                if (!validateLastName()) valid = false;
                if (!validateEmail()) valid = false;
                if (!validatePhone()) valid = false;
                if (!validatePostalCode()) valid = false;
                if (!validateVoivodeship()) valid = false;
                if (!validateCity()) valid = false;
                if (!validateStreet()) valid = false;
                if (!validateHouseNumber()) valid = false;
                if (!validateShippingMethod()) valid = false;
                if (!validatePayment()) valid = false;
                if (payment_method.value === 'karta') {
                    if (!validateCardNumber()) valid = false;
                    if (!validateExpiryDate()) valid = false;
                    if (!validateCVV()) valid = false;
                }
                if (!consentData.checked) {
                    consentData.classList.add('is-invalid');
                    valid = false;
                } else {
                    consentData.classList.remove('is-invalid');
                }
                if (!consentTerms.checked) {
                    consentTerms.classList.add('is-invalid');
                    valid = false;
                } else {
                    consentTerms.classList.remove('is-invalid');
                }
                if (!valid) {
                    e.preventDefault();
                }
            });

            toggleCreditCardFields();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
