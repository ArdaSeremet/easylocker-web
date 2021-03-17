<?php require_once '../config.php'; goIfNotLoggedInAdmin('/admin/login.php') ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>EasyLocker Admin</title>
    <link rel="stylesheet" href="/styles/main.css?v=<?php echo rand() ?>">
</head>
<body>
<header class="management-header">
    <h2>Locker Admin Panel</h2>
    <ul>
        <li><a href=".">My Lockers</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</header>

<main class="management-main">
<table class="lockers">
    <thead>
        <th>#</th>
        <th>Name</th>
        <th>Location</th>
        <th>Country</th>
        <th>Drawer Count</th>
        <th>Actions</th>
    </thead>
    <tbody>
    <?php
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
        echo "cURL Error #:" . $err;
        exit;
    }

    $lockers = $db->query('SELECT * FROM lockers WHERE admin_id = ' . $_SESSION['admin']['id'])->fetchAll(PDO::FETCH_ASSOC);
    foreach($lockers as $l) {

        $scount = $db->query('SELECT COUNT(*) FROM drawers WHERE locker_id = "'.$l['id'].'" AND size = "s"')->fetchColumn();
        $mcount = $db->query('SELECT COUNT(*) FROM drawers WHERE locker_id = "'.$l['id'].'" AND size = "m"')->fetchColumn();
        $lcount = $db->query('SELECT COUNT(*) FROM drawers WHERE locker_id = "'.$l['id'].'" AND size = "l"')->fetchColumn();
        $xlcount = $db->query('SELECT COUNT(*) FROM drawers WHERE locker_id = "'.$l['id'].'" AND size = "xl"')->fetchColumn();
        ?>

<tr>
    <td><?php echo $l['id'] ?></td>
    <td><?php echo $l['name'] ?></td>
    <td><?php echo $l['address'] ?></td>
    <td><?php echo $countries[$l['country_code']]['name'] ?></td>
    <td><?php echo $scount . 'S ' . $mcount . 'M ' . $lcount . 'L ' . $xlcount . 'XL'  ?></td>
    <td><a href="edit-locker.php?id=<?php echo $l['id'] ?>">Edit</a></td>
</tr>

        <?php
    }
    ?>
    </tbody>
</table>
</main>

<script src="/scripts/app.js"></script>
</body>
</html>
