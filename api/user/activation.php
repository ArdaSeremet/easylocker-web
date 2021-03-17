<?php
require_once __DIR__ . '/../../config.php';

goIfLoggedIn('/index.php');

if($_SERVER['REQUEST_METHOD'] == "GET") {
    if(!isset($_GET['id']) || !isset($_GET['code'])) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $id = trim($_GET['id']);
    $code = trim($_GET['code']);

    if(empty($id) || empty($code)) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }
    
    $userStmt = $db->prepare('SELECT * FROM users WHERE id = ? AND activation_code = ? AND status = ?');
    $userStmt->execute([
        $id,
        $code,
        '0'
    ]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if(!$userData) {
        sendAPIOutput('error', 'Activation code is invalid. Please contact system administrator.');
    }

    $userUpdateStmt = $db->prepare('UPDATE users SET status = ? WHERE id = ?');
    $userUpdateSuccess = $userUpdateStmt->execute([
        1,
        $id
    ]);

    if(!$userUpdateSuccess) {
        sendAPIOutput('error', 'System error while activating the user. Try again later.');
    }

    $_SESSION['user'] = $userData;
    
    header("refresh:2;url=/user");
    sendAPIOutput('success', 'Successfully activated user and logged in. You will be redirected soon.');
}

sendAPIOutput('error', 'Only GET method is allowed.');

