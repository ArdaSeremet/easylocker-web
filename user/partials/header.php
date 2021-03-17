<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Untitled' ?> | EasyLocker</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>

<body class="login-page-body">
    <div class="login-page-menu">
        <ul>
            <li><a href="/">
                    <span><i class="fas fa-home"></i></span>
                    <span>Homepage</span>
                </a></li>
            <li><a href="/user/index.php">
                    <span><i class="fas fa-user"></i></span>
                    <span>User Panel</span>
                </a></li>
            <li><a href="/user/reservations.php">
                    <span><i class="fas fa-user"></i></span>
                    <span>Reservations</span>
                </a></li>
            <?php if(isset($_SESSION['user'])): ?>
            <li><a href="/user/logout.php">
                    <span><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a></li>
            <?php else: ?>
            <li><a href="/user/login.php">
                    <span><i class="fas fa-sign-in-alt"></i></span>
                    <span>Login</span>
                </a></li>
            <li><a href="/user/register.php">
                    <span><i class="fas fa-user-plus"></i></span>
                    <span>Register</span>
                </a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="login-header">
        <h1>EasyLocker</h1>
    </div>