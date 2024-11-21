<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php/PHPMailer/src/Exception.php';
require 'php/PHPMailer/src/PHPMailer.php';
require 'php/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'proteinkibot@outlook.com';
        $mail->Password   = 'Proteiny123#';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('proteinkibot@outlook.com', 'Sklep Internetowy');
        $mail->addAddress('proteinki@outlook.com', 'Odbiorca');

        $mail->isHTML(true);
        $mail->Subject = 'Nowa wiadomość z formularza kontaktowego';
        $mail->Body    = "
            <h2>Nowa wiadomość z formularza kontaktowego</h2>
            <p><strong>Imię i nazwisko:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Wiadomość:</strong><br>{$message}</p>
        ";

        $mail->send();
        header("Location: kontakt.php?success=1");
        exit();
    } catch (Exception $e) {
        header("Location: kontakt.php?success=0");
        exit();
    }
}
?>
