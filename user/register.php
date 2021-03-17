<?php require_once __DIR__ . '/../config.php'; goIfLoggedIn('/user/index.php') ?>
<?php $pageTitle = 'User Registration' ?>
<?php require_once __DIR__ . '/partials/header.php' ?>
<div class="login-wrapper">
    <div class="login-head">User Register</div>
    <div class="login-body">
        <div id="errorField" class="error-field" style="display: none;">
        </div>
        <form class="login-form" id="loginForm" role="user-register" action="/api/user/register.php" method="post">
            <label class="form-field">
                <span>Name</span>
                <input type="text" name="name" id="name">
            </label>
            <label class="form-field">
                <span>Surname</span>
                <input type="text" name="surname" id="surname">
            </label>
            <label class="form-field">
                <span>Username</span>
                <input type="text" name="username" id="username">
            </label>
            <label class="form-field">
                <span>E-mail</span>
                <input type="text" name="email" id="email">
            </label>
            <label class="form-field">
                <span>Password</span>
                <input type="password" name="password" id="password">
            </label>
            <label class="form-field">
                <button type="submit" value="0">Register</button>
            </label>
        </form>
        <a class="after-form-link" href="login.php">Already have one? Login!</a>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php' ?>