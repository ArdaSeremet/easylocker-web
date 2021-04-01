<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(
        !isset($_POST['lockerId']) ||
        !isset($_POST['barcodeNumber']) ||
        !isset($_POST['email'])
    ) {
        sendAPIOutput('error', 'All parameters not sent.');
    }

    $lockerId = trim($_POST['lockerId']);
    $barcodeNumber = trim($_POST['barcodeNumber']);
    $email = trim($_POST['email']);

    if(
        empty($lockerId) ||
        empty($barcodeNumber) ||
        empty($email)
    ) {
        sendAPIOutput('error', 'Parameters cannot be empty.');
    }

    $drawerDetailsStmt = $db->prepare('SELECT drawers.id, drawers.recipient_name, drawers.recipient_email, lockers.name, lockers.country_code, lockers.address, drawers.locker_id FROM drawers LEFT JOIN lockers ON drawers.locker_id = lockers.id WHERE drawers.locker_id = ? AND drawers.barcode = ?');
    $drawerDetailsStmt->execute([ $lockerId, $barcodeNumber ]);
    $drawerDetails = $drawerDetailsStmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$drawerDetails) {
        sendAPIOutput('error', 'Couldnt found the drawer.');
    }

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

    $uniqueImgId = rand(0, 10000);
    $barcodeGenerator = new Picqer\Barcode\BarcodeGeneratorPNG();
    file_put_contents(__DIR__ . "/../temp/bar_{$uniqueImgId}.png", $barcodeGenerator->getBarcode($barcodeNumber, $barcodeGenerator::TYPE_CODE_128));
    
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";

    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_Host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_Username'];
    $mail->Password   = $_ENV['MAIL_Password'];
    $mail->SMTPSecure = $_ENV['MAIL_SMTPSecure'];
    $mail->Port       = $_ENV['MAIL_Port'];

    $mail->setFrom($_ENV['MAIL_Username'], 'EasyLocker');
    $mail->addAddress($email, 'EasyLocker Offline Reservation');
    $mail->AddEmbeddedImage(__DIR__ . "/../temp/bar_{$uniqueImgId}.png", 'barcode');
    
    $mail->Subject = "EasyLocker Drawer Reservation";
    $mail->Body = "Hello Offline User!<br>
    Your locker has been reserved from EasyLocker. These are the details of the reservation. If you can't see the barcode image, there may be a problem with your email client.<br><br>
    <img src='cid:barcode'><br>Barcode Number: {$barcodeNumber}<br>
    Locker Name: {$lockerName}<br>
    Location: {$lockerLocation}<br>
    Country: {$lockerCountry}<br>
    Recipient Name: {$drawerDetails['recipient_name']}<br>
    Recipient E-mail: {$drawerDetails['recipient_email']}<br>
    Sincerely,<br>
    EasyLocker Team";
    $mail->isHTML(true);

    $mail->send();

    unlink(__DIR__ . "/../temp/bar_{$uniqueImgId}.png");

    sendAPIOutput('success', 'Successfully reserved the drawer.', [
        'lockerId' => $lockerId,
        'drawerId' => $drawerDetails['id']
    ]);
}

sendAPIOutput('error', 'Only POST method is allowed.');