<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config.php';

goIfNotLoggedIn('/user/login.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['lockerId']) ||
        !isset($_POST['drawerSize']) ||
        !isset($_POST['recipientPhone']) ||
        !isset($_POST['recipientEmail']) ||
        !isset($_POST['recipientName']) ||
        !isset($_POST['barcodeNumber'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $lockerId = trim($_POST['lockerId']);
    $drawerSize = trim($_POST['drawerSize']);
    $recipientPhone = trim($_POST['recipientPhone']);
    $recipientEmail = trim($_POST['recipientEmail']);
    $recipientName = trim($_POST['recipientName']);
    $barcodeNumber = trim($_POST['barcodeNumber']);

    if(
        empty($lockerId) ||
        empty($drawerSize) ||
        empty($recipientPhone) ||
        empty($recipientEmail) ||
        empty($recipientName)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $lockerAvailabilityStmt = $db->prepare('SELECT * FROM lockers WHERE id = ?');
    $lockerAvailabilityStmt->execute([$lockerId]);
    $locker = $lockerAvailabilityStmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$locker)
        sendAPIOutput('error', 'This locker is currently not available to use!');

    $drawerStmt = $db->prepare('SELECT * FROM drawers WHERE locker_id = ?');
    $drawerStmt->execute([
        $lockerId
    ]);
    $drawers = $drawerStmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$drawers) {
        sendAPIOutput('error', 'Cannot find any drawers associated with this locker id!');
    }

    $availableDrawers = [];
    foreach($drawers as $d) {
        if($d['status'] != '0') continue;
        if($d['size'] == $drawerSize) $availableDrawers[] = $d['id'];
    }

    if(count($availableDrawers) <= 0)
        sendAPIOutput('error', 'No available drawers are found!');
        
    $randomDrawerId = $availableDrawers[rand(0, count($availableDrawers) - 1)];

    if(empty($barcodeNumber)) {
        $uniqueBarcode = generateBarcode($drawers);
    } else {
        $uniqueBarcode = $barcodeNumber;
    }

    $updateDrawer = $db->prepare('UPDATE drawers SET status = ?, recipient_name = ?, recipient_email = ?, recipient_phone = ?, user_id = ?, barcode = ? WHERE id = ?');
    $updateSuccess = $updateDrawer->execute([
        1,
        $recipientName,
        $recipientEmail,
        $recipientPhone,
        $_SESSION['user']['id'],
        $uniqueBarcode,
        $randomDrawerId
    ]);

    if(!$updateSuccess)
        sendAPIOutput('error', 'Error while communicating with the database.');

    $drawerDetailsStmt = $db->prepare('SELECT drawers.id, lockers.name, lockers.country_code, lockers.address, drawers.locker_id FROM drawers LEFT JOIN lockers ON drawers.locker_id = lockers.id WHERE barcode = ?');
    $drawerDetailsStmt->execute([ $uniqueBarcode ]);
    $drawerDetails = $drawerDetailsStmt->fetch(PDO::FETCH_ASSOC);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://ajayakv-rest-countries-v1.p.rapidapi.com/rest/v1/all",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: ajayakv-rest-countries-v1.p.rapidapi.com",
            "x-rapidapi-key: 72f921eaf4msh5b7db5db0fa7dbap193e34jsneb0d548aa86d"
        ],
    ]);
    
    $countries = json_decode(curl_exec($curl), true);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        sendAPIOutput('error', 'Internal server error.');
    }
    
    $lockerName = $drawerDetails['name'];
    $lockerLocation = $drawerDetails['address'];
    $lockerCountry = $countries[$drawerDetails['country_code']]['name'] ?? 'Unknown';

    $barcodeGenerator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodeHTML = $barcodeGenerator->getBarcode($uniqueBarcode, $barcodeGenerator::TYPE_CODE_128);
    $barcodeHTML = '<img src="data:image/png;base64,' . base64_encode($barcodeHTML) . '">';
    
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";

    $mail->isSMTP();
    $mail->Host       = 'smtp.yandex.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'easylocker@plushwsw.com';
    $mail->Password   = 'Progett1';
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;

    $mail->setFrom('easylocker@plushwsw.com', 'EasyLocker');
    $mail->addAddress($_SESSION['user']['email'], $_SESSION['user']['name'] . ' ' . $_SESSION['user']['surname']);
    
    $mail->Subject = "EasyLocker Drawer Reservation";
    $mail->Body = "Hello {$_SESSION['user']['name']} {$_SESSION['user']['surname']}!<br>
    Your drawer has successfully been reserved. These are the details of your reservation. If you can't see the barcode image, there may be a problem with your email client.<br><br>
    {$barcodeHTML}<br>Barcode Number: {$uniqueBarcode}<br>
    Locker Name: {$lockerName}<br>
    Location: {$lockerLocation}<br>
    Country: {$lockerCountry}<br>
    Recipient Name: {$recipientName}<br>
    Recipient E-mail: {$recipientEmail}<br>
    Recipient Phone: {$recipientPhone}<br><br><br>
    Sincerely,<br>
    EasyLocker Team";
    $mail->isHTML(true);

    $mail->send();

    sendAPIOutput('success', 'Successfully reserved the drawer.', [
        'lockerId' => $lockerId,
        'drawerId' => $randomDrawerId,
        'barcode' => $uniqueBarcode
    ]);
}

sendAPIOutput('error', 'Only POST method is allowed.');

