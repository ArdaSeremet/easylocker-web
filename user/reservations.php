<?php require_once __DIR__ . '/../config.php'; goIfNotLoggedIn('/user/login.php') ?>
<?php $pageTitle = 'Reservations' ?>
<?php require_once __DIR__ . '/partials/header.php' ?>
<div class="login-wrapper">
    <div class="login-head">Reservations</div>
    <div class="login-body">
        <div class="reservation-table-wrapper">
            <table class="lockers reservations">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barcode Number</th>
                        <th>Locker Name</th>
                        <th>Status</th>
                        <th>Country</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

$searchStmt = $db->prepare('SELECT drawers.id, drawers.barcode, drawers.status, drawers.user_id, lockers.country_code, lockers.name FROM drawers LEFT JOIN lockers ON drawers.locker_id = lockers.id WHERE user_id = ? AND status != ?');
$searchStmt->execute([ $_SESSION['user']['id'], 0 ]);
$reservations = $searchStmt->fetchAll(PDO::FETCH_ASSOC);

if(count($reservations) <= 0) {
    echo '<tr><td colspan="6">No reservations have been found.</td></tr>';
} else {
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
        die('Internal system error.');
    }

    foreach($reservations as $r) {
        $statusStr = "Unknown";
        switch($r['status']) {
            case 1:
                $statusStr = "Waiting for package";
                break;
            case 2:
                $statusStr = "Picked up by recipient";
                break;
        }
?>

                    <tr>
                        <td><?php echo $r['id'] ?></td>
                        <td><?php echo $r['barcode'] ?></td>
                        <td><?php echo $r['name'] ?></td>
                        <td><?php echo $statusStr ?></td>
                        <td><?php echo $countries[$r['country_code']]['name'] ?? 'Unknown' ?></td>
                        <td>
                            <a href="/api/user/cancel-reservation.php?id=<?php echo $r['id'] ?>">Cancel/Remove</a>
                        </td>
                    </tr>

                    <?php
    }
}

?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php' ?>