<?php require_once __DIR__ . '/config.php'; goIfLoggedIn('/user/index.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login | EasyLocker</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>

<body class="login-page-body">
    <div class="login-page-menu">
        <ul>
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
        <div class="login-head">Drawer Reservation</div>
        <div class="login-body">
            <div id="errorField" class="error-field" style="display: none;">
            </div>
            <form class="login-form" action="/user/register.php" method="post">
                <label class="form-field">
                    <span>Available Lockers</span>
                    <select name="locker" id="lockerReservationSelect">
                        <option value="0">Please select a locker.</option>
                        <?php
                        $lockersStmt = $db->prepare('SELECT * FROM lockers WHERE admin_id = 0');
                        $lockersStmt->execute();
                        $lockers = $lockersStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($lockers as $l) {
                            echo '<option value="'. $l['id'] .'">'. $l['name'] .'</option>';
                        }
                    ?>
                    </select>
                </label>
                <div class="form-field" id="lockerDataPreview" style="display: none;">
                    <span>Available Sizes: <span id="lockerAvailableSizes">*</span></span><br>
                    <span>Location: <span id="lockerLocation">*</span></span><br>
                    <span>Country: <span id="lockerCountry">*</span></span>
                </div>
                <div class="form-field">
                    <button type="submit" value="0">Register To Reserve</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/scripts/app.js?v=<?php echo rand(); ?>"></script>
</body>

</html>