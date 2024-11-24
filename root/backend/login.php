<?php

session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Pobranie i zabezpieczenie danych wejściowych
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Przygotowanie zapytania SQL do bazy danych
        $query = "SELECT * FROM users WHERE user_email = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);

            // Weryfikacja hasła
            if (password_verify($password, $user_data['password'])) {
                // Ustawienie danych sesji
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_name'] = $user_data['name'];

                // Sprawdzenie roli użytkownika
                if ($user_data['user_group'] === 'admin') {
                    $_SESSION['user_group'] = 'admin';
                } else {
                    $_SESSION['user_group'] = 'user';
                }

                // Przekierowanie po zalogowaniu
                header("Location: ../index.php");
                echo "<script>
                        alert('Logowanie zakończone sukcesem! Witamy, $user_name.');
                        window.location.href = 'index.php';
                      </script>";
                exit;
            } else {
                echo "<script>
                        alert('Niepoprawne hasło. Spróbuj ponownie.');
                        window.history.back();
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Użytkownik o podanej nazwie nie istnieje.');
                    window.history.back();
                  </script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>
                alert('Proszę wypełnić wszystkie pola.');
                window.history.back();
              </script>";
}
}
?>
