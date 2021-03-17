<?php
require_once __DIR__ . '/../../config.php';

goIfLoggedInAdmin('/admin/index.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(!isset($_POST['email']) || !isset($_POST['password'])) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $password = md5($password);
    
    $userStmt = $db->prepare('SELECT * FROM locker_admins WHERE email = ? AND password = ?');
    $userStmt->execute([
        $email,
        $password
    ]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if(!$userData) {
        sendAPIOutput('error', 'Please check your e-mail and password.');
    }

    $_SESSION['admin'] = $userData;
    sendAPIOutput('success', 'Successfully logged in. You will be redirected soon.');
}

sendAPIOutput('error', 'Only POST method is allowed.');

