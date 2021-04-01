<?php
require_once __DIR__ . '/../../config.php';

goIfNotLoggedInMgmt('/management/login.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['id'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $id = trim($_POST['id']);

    if(
        empty($id)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }
    
    $removeLockerStmt = $db->prepare('DELETE FROM lockers WHERE id = ?');
    $removeLockerStmt->execute([
        $id
    ]);

    $removeDrawersStmt = $db->prepare('DELETE FROM drawers WHERE locker_id = ?')->execute([ $id ]);

    sendAPIOutput('success', 'Successfully deleted the locker.');
}

sendAPIOutput('error', 'Only POST method is allowed.');