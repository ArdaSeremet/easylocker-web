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
<div class="login-wrapper">
		<div class="login-head">New Locker Admin</div>
		<div class="login-body">
			<div id="errorField" class="error-field" style="display: none;">
			</div>
			<form class="login-form" id="newForm" role="management" action="/api/management/new-admin.php" method="post">
				<label class="form-field">
					<span>Name</span>
					<input type="text" name="name" id="name">
				</label>
                <label class="form-field">
					<span>Surname</span>
					<input type="text" name="surname" id="surname">
				</label>
                <label class="form-field">
					<span>E-mail</span>
					<input type="email" name="email" id="email">
				</label>
                <label class="form-field">
					<span>Phone Number</span>
					<input type="text" name="phone" id="phone">
				</label>
                <label class="form-field">
					<span>Password</span>
					<input type="password" name="password" id="password">
				</label>

				<label class="form-field">
					<button type="submit" value="0">Create</button>
				</label>
			</form>
		</div>
	</div>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/scripts/app.js"></script>
<script>
$(() => {
    const newForm = $('form#newForm');
    newForm.submit(function (event) {
        event.preventDefault();

        const targetPath = $(this).attr('action');
        const name = $(this).find('input#name').val().trim();
        const surname = $(this).find('input#surname').val().trim();
        const email = $(this).find('input#email').val().trim();
        const phone = $(this).find('input#phone').val().trim();
        const password = $(this).find('input#password').val().trim();
        const errorField = $("#errorField");
        errorField.hide();
        
        if(
            name.length <= 0 ||
            surname.length <= 0 ||
            email.length <= 0 ||
            password.length <= 0 ||
            phone.length <= 0
        ) {
            return errorField.text('Please fill all the required inputs.').show();
        }

        $.ajax({
            url: targetPath,
            type: 'POST',
            dataType: 'JSON',
            data: {
                name: name,
                surname: surname,
                email: email,
                phone: phone,
                password: password
            },
            success: function (data) {
                if(data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location = 'index.php';
                    });
                } else {
                    return errorField.text(data.message).show();
                }
            }, error: function () {
                return errorField.text('Internal system error occured.').show();
            }
        });
    });
});
</script>
</body>
</html>
