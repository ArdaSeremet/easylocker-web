<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

try {
    $db = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8", $user, $pass);
} catch(PDOException $e) {
    die("Cannot connect to database.<br>Message: {$e->getMessage()}<pre></pre>");
}

require_once __DIR__ . '/helpers.php';

if(isset($_SESSION['user'])) {
    if(isset($_SESSION['user']['id'])) {
        $isUserStillValid = $db->prepare('SELECT * FROM users WHERE id = ? AND status = ?');
        $isUserStillValid->execute([
            $_SESSION['user']['id'],
            1
        ]);
        $userValidData = $isUserStillValid->fetch(PDO::FETCH_ASSOC);
        if(!$userValidData) {
            unset($_SESSION['user']);
        } else {
            $_SESSION['user'] = $userValidData;
        }
    } else {
        unset($_SESSION['user']);
    }
}