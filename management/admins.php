<?php require_once '../config.php'; goIfNotLoggedInMgmt('/management/login.php') ?>
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
        <th>Name</th>
        <th>Surname</th>
        <th>E-mail</th>
        <th>Phone Number</th>
        <th>Actions</th>
    </thead>
    <tbody>
    <?php

    $admins = $db->query('SELECT * FROM locker_admins')->fetchAll(PDO::FETCH_ASSOC);
    foreach($admins as $l) {
        ?>

<tr>
    <td><?php echo $l['id'] ?></td>
    <td><?php echo $l['name'] ?></td>
    <td><?php echo $l['surname'] ?></td>
    <td><?php echo $l['email'] ?></td>
    <td><?php echo $l['phone'] ?></td>
    <td><a href="edit-admin.php?id=<?php echo $l['id'] ?>">Edit</a></td>
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
