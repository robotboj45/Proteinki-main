<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Sklep Internetowy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header -->
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
                        <a class="nav-link text-white" href="../checkout/cart.php"><i class="fas fa-shopping-cart"></i> Koszyk</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="container my-5 shadow-lg p-5 bg-white rounded">
        <h1 class="text-center mb-4 text-uppercase text-primary">Kontakt</h1>
        <form action="#" method="post" class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Imię i nazwisko:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="col-12">
                <label for="message" class="form-label">Wiadomość:</label>
                <textarea id="message" name="message" rows="4" class="form-control" required></textarea>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary btn-lg">Wyślij</button>
            </div>
        </form>
    </section>

    <!-- Footer -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
