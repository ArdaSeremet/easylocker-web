<?php require_once '../config.php'; goIfNotLoggedInMgmt('/management/login.php') ?>
<?php if(!isset($_GET['id'])) header('Location: .') ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>EasyLocker Management</title>
    <link rel="stylesheet" href="/styles/main.css?v=<?php echo rand() ?>">
</head>

<body>
    <header class="management-header">
        <h2>Locker Management</h2>
        <ul>
            <li><a href=".">Lockers</a></li>
            <li><a href="new.php">New Locker</a></li>
            <li><a href="admins.php">Locker Admins</a></li>
            <li><a href="new_admin.php">New Locker Admin</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </header>

    <main class="management-main">
        <table class="lockers">
            <thead>
                <th>#</th>
                <th>Size</th>
                <th>Slave Board #</th>
                <th>Solenoid #</th>
                <th>Status</th>
                <th>User ID</th>
                <th>Barcode</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php

$sizes = [
    's' => 'Small',
    'm' => 'Medium',
    'l' => 'Large',
    'xl' => 'Extra Large'
];

$statuses = [
    0 => 'Available',
    1 => 'Awaiting Carrier',
    2 => 'Awaiting Pickup'
];

$drStmt = $db->prepare('SELECT * FROM drawers WHERE locker_id = ?');
$drStmt->execute([$_GET['id']]);
$drawers = $drStmt->fetchAll(PDO::FETCH_ASSOC);

if(count($drawers) <= 0) {
?>
                <tr>
                    <td colspan="6">No drawers are found for this locker.</td>
                </tr>
                <?php
} else {
    foreach($drawers as $d) {
        ?>

                <tr>
                    <td><?php echo $d['id'] ?></td>
                    <td><?php echo $sizes[$d['size']]  ?></td>
                    <td><?php echo (intval($d['slave_id']) > 0) ? $d['slave_id'] : 'NONE'  ?></td>
                    <td><?php echo (strlen($d['relay_number'])) ? $d['relay_number'] : 'NONE'  ?></td>
                    <td><?php echo $statuses[$d['status']] ?></td>
                    <td><?php echo $d['user_id'] ?></td>
                    <td><?php echo $d['barcode'] ?></td>
                    <td>
                        <a href="#" class="lockerRelayAssigner" data-drawer-id="<?php echo $d['id'] ?>"
                            data-locker-id="<?php echo $d['locker_id'] ?>">Assign Relay</a>
                        <a href="#" class="lockerDrawerRemover" data-drawer-id="<?php echo $d['id'] ?>"
                            data-locker-id="<?php echo $d['locker_id'] ?>">Delete</a>
                    </td>
                </tr>

                <?php
    }
}
?>
            </tbody>
        </table>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
    const relayAssignments =
        <?php $result = []; foreach($drawers as $d): if(intval($d['relay_number']) > 0): $result[] = [intval($d['slave_id']), intval($d['relay_number'])]; endif; endforeach; echo json_encode($result); ?>;
    </script>
    <script src="/scripts/app.js?v=<?php echo rand(); ?>"></script>
</body>

</html>