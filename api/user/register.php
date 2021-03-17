<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config.php';

goIfLoggedIn('/index.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['name']) ||
        !isset($_POST['surname']) ||
        !isset($_POST['email']) ||
        !isset($_POST['username']) ||
        !isset($_POST['password'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(
        empty($name) ||
        empty($surname) ||
        empty($email) ||
        empty($username) ||
        empty($password)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $userExistsStmt = $db->prepare('SELECT * FROM users WHERE email = ? OR username = ?');
    $userExistsStmt->execute([
        $email, $username
    ]);
    $userExists = $userExistsStmt->fetch(PDO::FETCH_ASSOC);

    if($userExists) {
        sendAPIOutput('error', 'A user with this email or username is already registered.');
    }

    $password = md5($password);
    $activationCode = md5(sha1(md5(rand())));
    
    $userStmt = $db->prepare('INSERT INTO users SET
    name = ?, surname = ?, username = ?, email = ?, password = ?, status = ?, activation_code = ?
    ');
    $userStmt->execute([
        $name, $surname, $username, $email, $password, 0, $activationCode
    ]);

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_Host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_Username'];
    $mail->Password   = $_ENV['MAIL_Password'];
    $mail->SMTPSecure = $_ENV['MAIL_SMTPSecure'];
    $mail->Port       = $_ENV['MAIL_Port'];

    $mail->setFrom($_ENV['MAIL_Username'], 'EasyLocker');
    $mail->addAddress($email, $name . ' ' . $surname);
    
    $mail->isHTML(true);
    $mail->Subject = "EasyLocker User Registration";
    $mail->Body = "Hello {$name} {$surname}!<br>
    Thank you for registering on EasyLocker system. To activate your account please <a href=\"http://easylocker.plushwsw.com/api/user/activation.php?id={$db->lastInsertId()}&code={$activationCode}\">click here</a>.";

    $mail->send();

    sendAPIOutput('success', 'Successfully registered. You will be redirected soon.');
}

sendAPIOutput('error', 'Only POST method is allowed.');