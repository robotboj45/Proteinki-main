<?php
session_start();
include("connection.php");

$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Read Users</title>
</head>
<body>
Create user: <a href="create_user.php">Kliknij</a>
<h2>List of Users</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>User Name</th>
        <th>User Group</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php while($user = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo $user['user_name']; ?></td>
            <td><?php echo $user['user_group']; ?></td>
            <td><?php echo $user['mobile']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['date']; ?></td>
            <td>
                <a href="update_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
