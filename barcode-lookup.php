<?php require_once __DIR__ . '/config.php'; ?>
<?php

if(!isset($_GET['barcode'])) {
    header('refresh:1;url=/lookup.php');
    die('Invalid data.');
}

$barcode = $_GET['barcode'];

if($barcode == '') {
    header('refresh:1;url=/lookup.php');
    die('Invalid barcode format.');
}

$barcodeLookupStmt = $db->prepare('SELECT * FROM drawers WHERE barcode = ?');
$barcodeLookupStmt->execute([
    $barcode
]);
$drawer = $barcodeLookupStmt->fetch(PDO::FETCH_ASSOC);
if(!$drawer) {
    header('refresh:1;url=/lookup.php');
    die('No drawers found with this barcode number.');
}

$lockerStmt = $db->prepare('SELECT * FROM lockers WHERE id = ?');
$lockerStmt->execute([
    $drawer['locker_id']
]);
$locker = $lockerStmt->fetch(PDO::FETCH_ASSOC);
if(!$locker) {
    header('refresh:1;url=/lookup.php');
    die('Error while communciating with the database!');
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
    header('refresh:1;url=/lookup.php');
    die('Internal system error.');
}

$lockerCountry = $countries[$locker['country_code']]['name'] ?? 'Unknown';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Lookup | EasyLocker</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>

<body class="login-page-body">
    <div class="login-page-menu">
        <ul>
            <li><a href="/">
                    <span><i class="fas fa-home"></i></span>
                    <span>Homepage</span>
                </a></li>
            <li><a href="/lookup.php">
                    <span><i class="fas fa-search"></i></span>
                    <span>Lookup</span>
                </a></li>
            <li><a href="/user/login.php">
                    <span><i class="fas fa-sign-in-alt"></i></span>
                    <span>Login</span>
                </a></li>
            <li><a href="/user/register.php">
                    <span><i class="fas fa-user-plus"></i></span>
                    <span>Register</span>
                </a></li>
        </ul>
    </div>

    <div class="login-header">
        <h1>EasyLocker</h1>
    </div>
    <div class="login-wrapper">
        <div class="login-head">Barcode Lookup</div>
        <div class="login-body">
            <svg id="barcode" jsbarcode-format="CODE128" jsbarcode-value="<?php echo $barcode; ?>"
                jsbarcode-textmargin="0" jsbarcode-fontoptions="bold"></svg><br>
            <span class="gap"><strong>Locker Name:</strong> <?php echo $locker['name'] ?></span>
            <span class="gap"><strong>Locker Location:</strong> <?php echo $locker['address'] ?></span>
            <span class="gap"><strong>Locker Country:</strong> <?php echo $lockerCountry ?></span>
            <span class="gap"><strong>Recipient Name:</strong> <?php echo $drawer['recipient_name'] ?></span>
            <span class="gap"><strong>Recipient Email:</strong> <?php echo $drawer['recipient_email'] ?></span>
            <span class="gap"><strong>Recipient Phone:</strong> <?php echo $drawer['recipient_phone'] ?></span>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.3/JsBarcode.all.min.js"
        integrity="sha512-TLB7v1Y4YHGy/EHUu5VZ2bl6sC/WvXh/NFdjEZ7JmbpsUG87dirXAOFSAS3O6Tn3rsZljFTcTdMz9PDM4mV26g=="
        crossorigin="anonymous"></script>
    <script>
    JsBarcode("#barcode").init();
    </script>
    <script src="/scripts/app.js?v=<?php echo rand(); ?>"></script>
</body>

</html>