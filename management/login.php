<?php require_once __DIR__ . '/../config.php'; goIfLoggedInMgmt('/management/index.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Management Login | EasyLocker</title>
	<link rel="stylesheet" href="/styles/main.css">
</head>
<body class="login-page-body">
	<div class="login-header">
		<h1>EasyLocker</h1>
	</div>
	<div class="login-wrapper">
		<div class="login-head">Management Login</div>
		<div class="login-body">
			<div id="errorField" class="error-field" style="display: none;">
			</div>
			<form class="login-form" id="loginForm" role="management" action="/api/management/login.php" method="post">
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
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script src="/scripts/app.js"></script>
</body>
</html>