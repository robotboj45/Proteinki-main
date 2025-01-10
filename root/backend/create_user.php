
<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashowanie hasła jest OK
    $user_group = mysqli_real_escape_string($con, $_POST['user_group']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $date = date("Y-m-d H:i:s");

    if (!empty($user_id) && !empty($password) && !empty($email)) {
        $query = "INSERT INTO users (user_id, user_name, password, mobile, user_email, user_group) VALUES ('$user_id', '$user_name','$password', '$mobile', '$email','$user_group')";
        if (mysqli_query($con, $query)) {
            echo "User created successfully!";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Please fill all required fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Create User</h1>
</header>

<div class="container">
    <h2>Create a New User</h2>
    <a href="read_users.php">Go back to user list</a>
    <form method="POST" action="">
        <label>User ID:</label>
        <input type="text" name="user_id" required>

        <label>User Name:</label>
        <input type="text" name="user_name" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>User Group:</label>
        <input type="text" name="user_group" required>

        <label>Mobile:</label>
        <input type="text" name="mobile">

        <label>Email:</label>
        <input type="email" name="email" required>

        <input type="submit" value="Create User">
    </form>
    <a href="read_users.php" class="btn btn-success mt-4">Powrót</a>
</div>

</body>
</html>
