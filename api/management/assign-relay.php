<?php
require_once __DIR__ . '/../../config.php';

//goIfNotLoggedInMgmt('/management/login.php');

if(!isset($_SESSION['admin']) && !isset($_SESSION['management'])) {
    header('Location: /');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['relayNumber']) ||
        !isset($_POST['drawerId'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $relayNumber = trim($_POST['relayNumber']);
    $drawerId = trim($_POST['drawerId']);
    if(
        empty($relayNumber) ||
        empty($drawerId)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $relayNumber = intval($relayNumber);

    if($relayNumber < 1 || $relayNumber > 16) {
        sendAPIOutput('error', 'Relay number is invalid.');
    }

    $updateStmt = $db->prepare('UPDATE drawers SET relay_number = ? WHERE id = ?');
    $updateStmt->execute([
        $relayNumber, $drawerId
    ]);
    
    sendAPIOutput('success', 'Successfully assigned the relay.');
}

sendAPIOutput('error', 'Only POST method is allowed.');

