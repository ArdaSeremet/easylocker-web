<?php require_once __DIR__ . '/../config.php'; goIfLoggedIn('/user/index.php') ?>
<?php $pageTitle = 'User Login' ?>
<?php require_once __DIR__ . '/partials/header.php' ?>
<div class="login-wrapper">
    <div class="login-head">User Login</div>
    <div class="login-body">
        <div id="errorField" class="error-field" style="display: none;">
        </div>
        <form class="login-form" id="loginForm" role="management" action="/api/user/login.php" method="post">
            <label class="form-field">
                <span>Username</span>
                <input type="text" name="username" id="username">
            </label>
            <label class="form-field">
                <span>Password</span>
                <input type="password" name="password" id="password">
            </label>
            <label class="form-field">
                <button type="submit" value="0">Login</button>
            </label>
        </form>
        <a class="after-form-link" href="register.php">Don't have one? Register!</a>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php' ?>