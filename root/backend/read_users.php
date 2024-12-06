<?php
session_start();
include("connection.php");
$user_group =  $_SESSION['user_group'];
if ($user_group != 'admin') {
    header('Location: ../index.php');
    exit; // Ważne, aby zatrzymać dalsze wykonywanie kodu
}

$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">Lista użytkowników</h1>
        <a href="create_user.php" class="btn btn-success">Dodaj nowego użytkownika</a>
        <a href="../index.php" class="btn btn-info">Wróć do strony głównej</a>
    </div>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Nazwa użytkownika</th>
            <th>Grupa</th>
            <th>Telefon</th>
            <th>Email</th>
            <th>Data</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>
        <?php while($user = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo $user['user_name']; ?></td>
                <td><?php echo $user['user_group']; ?></td>
                <td><?php echo $user['mobile']; ?></td>
                <td><?php echo $user['user_email']; ?></td>
                <td><?php echo $user['date']; ?></td>
                <td>
                    <a href="update_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edytuj</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
