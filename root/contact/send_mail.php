<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobieranie danych z formularza
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Ustawienie adresu e-mail do którego ma zostać wysłana wiadomość
    $to = "admin@grupaj.xce.pl"; // Podaj swój adres e-mail
    $subject = "Wiadomość ze strony kontaktowej";
    
    // Treść wiadomości
    $body = "Imię i nazwisko: $name\n";
    $body .= "Email: $email\n";
    $body .= "Wiadomość:\n$message";
    
    // Nagłówki wiadomości
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Wysłanie e-maila
    if (mail($to, $subject, $body, $headers)) {
        echo "Dziękujemy za kontakt! Twoja wiadomość została wysłana.";
    } else {
        echo "Wystąpił problem podczas wysyłania wiadomości. Spróbuj ponownie.";
    }
}
?>
