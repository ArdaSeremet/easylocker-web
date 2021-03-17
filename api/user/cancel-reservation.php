<?php
require_once __DIR__ . '/../../config.php';

goIfNotLoggedIn('/index.php');

if($_SERVER['REQUEST_METHOD'] == "GET") {
    if(!isset($_GET['id'])) {
        header('refresh:0.5;url=/user/reservations.php');
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $id = trim($_GET['id']);

    if(empty($id)) {
        header('refresh:0.5;url=/user/reservations.php');
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }
    
    $updateStmt = $db->prepare('UPDATE drawers SET barcode = ?, user_id = ?, status = ? WHERE id = ?');
    $updateSuccess = $updateStmt->execute([
        0,
        0,
        0,
        $id
    ]);

    if(!$updateSuccess) {
        header('refresh:0.5;url=/user/reservations.php');
        sendAPIOutput('error', 'Database error occured.');
    }

    header("Location: /user/reservations.php");
    sendAPIOutput('success', 'Successfully activated user and logged in. You will be redirected soon.');
}

header('refresh:0.5;url=/user/reservations.php');
sendAPIOutput('error', 'Only GET method is allowed.');

