<?php
require_once __DIR__ . '/../../config.php';

goIfLoggedInMgmt('/management/index.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(!isset($_POST['username']) || !isset($_POST['password'])) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $password = md5($password);
    
    $userStmt = $db->prepare('SELECT * FROM management WHERE username = ? AND password = ?');
    $userStmt->execute([
        $username,
        $password
    ]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if(!$userData) {
        sendAPIOutput('error', 'Please check your username and password.');
    }

    if($userData['status'] == 0) {
        sendAPIOutput('error', 'This user is inactive.');
    }

    $_SESSION['management'] = $userData;
    sendAPIOutput('success', 'Successfully logged in. You will be redirected soon.');
}

sendAPIOutput('error', 'Only POST method is allowed.');

