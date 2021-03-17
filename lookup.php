<?php require_once __DIR__ . '/config.php'; ?>
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
            <li><a href="/">
                    <span><i class="fas fa-home"></i></span>
                    <span>Homepage</span>
                </a></li>
            <?php if(!isset($_SESSION['user'])): ?>
            <li><a href="/user/login.php">
                    <span><i class="fas fa-sign-in-alt"></i></span>
                    <span>Login</span>
                </a></li>
            <li><a href="/user/register.php">
                    <span><i class="fas fa-user-plus"></i></span>
                    <span>Register</span>
                </a></li>
            <?php else: ?>
            <li><a href="/user/index.php">
                    <span><i class="fas fa-user"></i></span>
                    <span>User Panel</span>
                </a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="login-header">
        <h1>EasyLocker</h1>
    </div>
    <div class="login-wrapper">
        <div class="login-head">Barcode Lookup</div>
        <div class="login-body">
            <div id="errorField" class="error-field" style="display: none;">
            </div>
            <form class="login-form" action="barcode-lookup.php" method="get">
                <label class="form-field">
                    <span>Barcode Number</span>
                    <input required type="text" name="barcode" id="barcode">
                </label>
                <label class="form-field">
                    <button type="submit" value="0">Look it up!</button>
                </label>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/scripts/app.js"></script>
</body>

</html>