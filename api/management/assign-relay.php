<?php
require_once __DIR__ . '/../../config.php';

//goIfNotLoggedInMgmt('/management/login.php');

if(!isset($_SESSION['admin']) && !isset($_SESSION['management'])) {
    header('Location: /');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['slaveId']) ||
        !isset($_POST['relayNumber']) ||
        !isset($_POST['drawerId'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $slaveId = trim($_POST['slaveId']);
    $relayNumber = trim($_POST['relayNumber']);
    $drawerId = trim($_POST['drawerId']);
    if(
        empty($relayNumber) ||
        empty($slaveId) ||
        empty($drawerId)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $relayNumber = intval($relayNumber);
    $slaveId = intval($slaveId);

    if($relayNumber < 1 || $relayNumber > 14) {
        sendAPIOutput('error', 'Relay number is invalid.');
    }

    if($slaveId < 1) {
        sendAPIOutput('error', 'Slave board number is invalid.');
    }

    $updateStmt = $db->prepare('UPDATE drawers SET slave_id = ?, relay_number = ? WHERE id = ?');
    $updateStmt->execute([
        $slaveId, $relayNumber, $drawerId
    ]);
    
    sendAPIOutput('success', 'Successfully assigned the relay.');
}

sendAPIOutput('error', 'Only POST method is allowed.');