<?php
require_once __DIR__ . '/../../config.php';

goIfNotLoggedInMgmt('/management/login.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['name']) ||
        !isset($_POST['surname']) ||
        !isset($_POST['phone']) ||
        !isset($_POST['email']) ||
        !isset($_POST['password'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(
        empty($name) ||
        empty($surname) ||
        empty($phone) ||
        empty($email) ||
        empty($password)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $password = md5($password);
    
    $insertStmt = $db->prepare('INSERT INTO locker_admins SET
    name = ?,
    surname = ?,
    email = ?,
    phone = ?,
    password = ?
    ');
    $insertStmt->execute([
        $name,
        $surname,
        $email,
        $phone,
        $password
    ]);

    sendAPIOutput('success', 'Successfully created the locker admin.');
}

sendAPIOutput('error', 'Only POST method is allowed.');

