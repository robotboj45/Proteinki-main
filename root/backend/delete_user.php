<?php
session_start();
include("connection.php");

$id = $_GET['id'];

if (!empty($id)) {
    $query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($con, $query)) {
        echo "User deleted successfully!";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

header("Location: read_users.php");
die;
?>
