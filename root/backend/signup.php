<?php
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Pobranie danych z formularza
    $user_name = trim($_POST['user_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']); // Dodane pole
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);

    // Sprawdzenie, czy wszystkie pola są wypełnione
    if (!empty($user_name) && !empty($password) && !empty($confirm_password) && !empty($mobile) && !empty($email)) {
        // Sprawdzenie, czy hasło i powtórzone hasło są zgodne
        if ($password === $confirm_password) {
            // Sprawdzanie, czy email już istnieje
            $check_sql = "SELECT * FROM users WHERE user_email = ?";
            $check_stmt = mysqli_prepare($con, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                echo "<script>
                        alert('Posiadasz już konto w naszym sklepie. Proszę zalogować się.');
                        window.location.href = '../account/login.html';
                      </script>";
            } else {
                // Generowanie unikalnego ID użytkownika
                $user_id = random_num(20);

                // Hashowanie hasła
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Przygotowanie zapytania do dodania nowego użytkownika
                $insert_sql = "INSERT INTO users (user_id, user_name, password, mobile, user_email) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = mysqli_prepare($con, $insert_sql);
                mysqli_stmt_bind_param($insert_stmt, "sssss", $user_id, $user_name, $hashed_password, $mobile, $email);

                if (mysqli_stmt_execute($insert_stmt)) {
                    echo "<script>
                            alert('Nowe konto zostało utworzone pomyślnie!');
                            window.location.href = '../account/login.html';
                          </script>";
                    exit;
                } else {
                    echo "Wystąpił błąd podczas tworzenia konta. Spróbuj ponownie później.";
                }
            }

            // Zamknięcie zapytań
            mysqli_stmt_close($check_stmt);
            if (isset($insert_stmt)) {
                mysqli_stmt_close($insert_stmt);
            }
        } else {
            echo "<script>
                    alert('Hasła nie są zgodne. Spróbuj ponownie.');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "Proszę wypełnić wszystkie pola formularza!";
    }
}
?>

	
?>


<!DOCTYPE html>
<html>
<head>
	<title>Signup</title>
</head>
<body>

	<style type="text/css">

	#text{

		height: 25px;
		border-radius: 5px;
		padding: 4px;
		border: solid thin #aaa;
		width: 100%;
	}

	#button{

		padding: 10px;
		width: 100px;
		color: white;
		background-color: lightblue;
		border: none;
	}

	#box{

		background-color: grey;
		margin: auto;
		width: 300px;
		padding: 20px;
	}

	/* </style>

	<div id="box">

		<form method="post">
			<div style="font-size: 20px;margin: 10px;color: white;">Signup</div>
            Login:
			<input id="text" type="text" name="user_name"><br><br>
            Password:
			<input id="text" type="password" name="password"><br><br>
			Powtórz hasło:
   			<input type="password" id="confirm_password" name="confirm_password" required><br><br>
            Nr telefonu:
            <input id="text" type="text" name="mobile"><br><br>
            Email:
            <input id="text" type="text" name="email"><br><br>

			<input id="button" type="submit" value="Signup"><br><br>

			<a href="login.php">Click to Login</a><br><br>
		</form>
	</div>
</body>
</html> */
