<?php
session_start();
include("connection.php");

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $user_group = $_POST['user_group'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    if (!empty($user_id) && !empty($email)) {
        $query = "UPDATE users SET user_id = '$user_id', user_name = '$user_name', user_group = '$user_group', mobile = '$mobile', email = '$email'";

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = '$hashed_password'";
        }

        $query .= " WHERE id = '$id'";

        if (mysqli_query($con, $query)) {
            echo "User updated successfully!";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Please fill all required fields!";
    }
} else {
    $query = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
</head>
<body>
<h2>Update User</h2>
<form method="POST">
    User ID: <input type="text" name="user_id" value="<?php echo $user['user_id']; ?>" required><br><br>
    User Name: <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>" required><br><br>
    Password: <input type="password" name="password" placeholder="Leave blank to keep current"><br><br>
    User Group: <input type="text" name="user_group" value="<?php echo $user['user_group']; ?>" required><br><br>
    Mobile: <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>"><br><br>
    Email: <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>
    <input type="submit" value="Update User">
</form>
</body>
</html>
