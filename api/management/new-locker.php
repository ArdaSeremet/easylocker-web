<?php
require_once __DIR__ . '/../../config.php';

goIfNotLoggedInMgmt('/management/login.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['name']) ||
        !isset($_POST['ip']) ||
        !isset($_POST['scount']) ||
        !isset($_POST['mcount']) ||
        !isset($_POST['lcount']) ||
        !isset($_POST['xlcount']) ||
        !isset($_POST['address']) ||
        !isset($_POST['country']) ||
        !isset($_POST['managerEmail'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $name = trim($_POST['name']);
    $ip = trim($_POST['ip']);
    $scount = trim($_POST['scount']) ?? '0';
    $mcount = trim($_POST['mcount']) ?? '0';
    $lcount = trim($_POST['lcount']) ?? '0';
    $xlcount = trim($_POST['xlcount']) ?? '0';
    $address = trim($_POST['address']);
    $country = trim($_POST['country']);
    $manager_email = trim($_POST['managerEmail']);

    if(
        empty($name) ||
        empty($ip) ||
        empty($address) ||
        empty($country)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $locker_admin_id = 0;

    if(!empty($manager_email)) {
        $adminIdStmt = $db->prepare('SELECT id FROM locker_admins WHERE email = ?');
        $adminIdStmt->execute([ $manager_email ]);
        $result = $adminIdStmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            $locker_admin_id = $result['id'];
        } else {
            sendAPIOutput('error', 'Any locker admin with this email address does not exist.');
        }
    }
    
    $insertStmt = $db->prepare('INSERT INTO lockers SET
    name = ?,
    province_iso_code = ?,
    country_code = ?,
    address = ?,
    unique_hardware_id = ?,
    json_data = ?,
    admin_id = ?,
    whitelist = ?
    ');
    $insertStmt->execute([
        $name,
        0,
        $country,
        $address,
        $ip,
        json_encode([]),
        $locker_admin_id,
        json_encode([])
    ]);

    $lockerId = $db->lastInsertId();

    for($i = 0; $i < intval($scount); $i++) {
        $db->prepare('INSERT INTO drawers SET size = ?, locker_id = ?')->execute([ 's', $lockerId ]);
    }
    for($i = 0; $i < intval($mcount); $i++) {
        $db->prepare('INSERT INTO drawers SET size = ?, locker_id = ?')->execute([ 'm', $lockerId ]);
    }
    for($i = 0; $i < intval($lcount); $i++) {
        $db->prepare('INSERT INTO drawers SET size = ?, locker_id = ?')->execute([ 'l', $lockerId ]);
    }
    for($i = 0; $i < intval($xlcount); $i++) {
        $db->prepare('INSERT INTO drawers SET size = ?, locker_id = ?')->execute([ 'xl', $lockerId ]);
    }

    sendAPIOutput('success', 'Successfully created the locker.');
}

sendAPIOutput('error', 'Only POST method is allowed.');

