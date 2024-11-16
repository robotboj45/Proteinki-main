<?php
// Konfiguracja bazy danych
$host = '127.0.0.1';
$db = 'image_storage';
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

// Obsługa przesłanego pliku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageData = file_get_contents($imageTmpName);

    // Zapis do bazy danych
    $stmt = $pdo->prepare("INSERT INTO images (image_name, image_data) VALUES (:image_name, :image_data)");
    $stmt->bindParam(':image_name', $imageName);
    $stmt->bindParam(':image_data', $imageData, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        echo "Image uploaded successfully!";
    } else {
        echo "Failed to upload image.";
    }
} else {
    echo "No image uploaded.";
}
?>
