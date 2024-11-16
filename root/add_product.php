<?php
// Konfiguracja bazy danych
$host = '127.0.0.1';
$db = 'proteinki_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Pobranie kategorii z bazy
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category_id = $_POST['category_id'] ?? 0;
    
    // Obsługa przesyłania pliku
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $imageData = file_get_contents($image['tmp_name']);
        
        // Walidacja zdjęcia
        if (in_array($image['type'], ['image/jpeg', 'image/png', 'image/webp'])) {
            // Wstawianie produktu
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':category_id' => $category_id,
            ]);
            
            // Pobranie ID ostatnio dodanego produktu
            $productId = $pdo->lastInsertId();

            // Wstawianie zdjęcia
            $stmt = $pdo->prepare("INSERT INTO imagesproduct (product_id, image_path) VALUES (:product_id, :image_path)");
            $stmt->execute([
                ':product_id' => $productId,
                ':image_path' => $imageData,
            ]);

            echo "<p>Produkt z obrazkiem został dodany pomyślnie!</p>";
        } else {
            echo "<p>Proszę przesłać poprawny plik graficzny (JPG, PNG, WebP).</p>";
        }
    } else {
        echo "<p>Proszę przesłać zdjęcie!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Produkt</title>
</head>
<body>
    <h1>Dodaj Produkt</h1>
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <label for="name">Nazwa produktu:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Opis produktu:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="price">Cena produktu (w zł):</label><br>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="category_id">Kategoria:</label><br>
        <select id="category_id" name="category_id" required>
            <option value="">-- Wybierz kategorię --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="image">Wybierz zdjęcie produktu:</label><br>
        <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/webp" required><br><br>

        <button type="submit">Dodaj Produkt</button>
    </form>
</body>
</html>
