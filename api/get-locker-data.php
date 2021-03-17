<?php
require_once __DIR__ . '/../config.php';

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
    
    $selectStmt = $db->prepare('SELECT * FROM lockers WHERE id = ?');
    $selectStmt->execute([
        $id
    ]);
    $lockerData = $selectStmt->fetch(PDO::FETCH_ASSOC);

    if(!$lockerData) {
        sendAPIOutput('error', 'Cannot find any records associated with this locker id!');
    }

    
    $sAvab = 0;
    $mAvab = 0;
    $lAvab = 0;
    $xlAvab = 0;

    $drawerStmt = $db->prepare('SELECT * FROM drawers WHERE locker_id = ?');
    $drawerStmt->execute([
        $id
    ]);
    $drawers = $drawerStmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$drawers) {
        sendAPIOutput('error', 'Cannot find any drawers associated with this locker id!');
    }

    foreach($drawers as $d) {
        if($d['status'] != '0') continue;

        switch($d['size']) {
            case 's':
                $sAvab++;
                break;
            case 'm':
                $mAvab++;
                break;
            case 'l':
                $lAvab++;
                break;
            case 'xl':
                $xlAvab++;
                break;
        }
    }

    $lockerData['sAvab'] = $sAvab;
    $lockerData['mAvab'] = $mAvab;
    $lockerData['lAvab'] = $lAvab;
    $lockerData['xlAvab'] = $xlAvab;
    sendAPIOutput('success', 'Successfully created the locker.', $lockerData);
}

sendAPIOutput('error', 'Only POST method is allowed.');

