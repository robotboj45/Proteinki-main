<?php
include('connection.php');
session_start();
$user_group =  $_SESSION['user_group'];
if ($user_group != 'admin') {
    header('Location: ../index.php');
    exit; // Ważne, aby zatrzymać dalsze wykonywanie kodu
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['productName']);
    $description = mysqli_real_escape_string($con, $_POST['productDescription']);
    $price = mysqli_real_escape_string($con, $_POST['productPrice']);
    $category_id = mysqli_real_escape_string($con, $_POST['productCategory']);

    $insert_product = "INSERT INTO products (name, description, price, category_id) VALUES ('$name', '$description', '$price', '$category_id')";
    if (mysqli_query($con, $insert_product)) {
        $product_id = mysqli_insert_id($con);

        $image_count = count($_FILES['productImage']['name']);
        for ($i = 0; $i < $image_count; $i++) {
            $image_name = $_FILES['productImage']['name'][$i];
            $image_tmp = $_FILES['productImage']['tmp_name'][$i];

            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_new_name = uniqid() . '.' . $image_ext;
            $image_path = '../uploads/' . $image_new_name;

            if (move_uploaded_file($image_tmp, $image_path)) {
                $insert_image = "INSERT INTO imagesproduct (product_id, image_path) VALUES ('$product_id', '$image_path')";
                mysqli_query($con, $insert_image);
            }
        }
        header('Location: ../admin/dashboard.html');
    } else {
        echo "Błąd: " . mysqli_error($con);
    }
}
?>
