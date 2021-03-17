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
		<div class="login-head">New Locker</div>
		<div class="login-body">
			<div id="errorField" class="error-field" style="display: none;">
			</div>
			<form class="login-form" id="newForm" role="management" action="/api/management/new-locker.php" method="post">
				<label class="form-field">
					<span>Locker Name</span>
					<input type="text" name="name" id="name">
				</label>
                <label class="form-field">
					<span>Unique Hardware ID</span>
					<input type="text" name="ip" id="ip">
				</label>
                <label class="form-field">
					<span>S Count</span>
					<input type="number" name="scount" min="0" step="1" id="scount">
				</label>
                <label class="form-field">
					<span>M Count</span>
					<input type="number" name="scount" min="0" step="1" id="mcount">
				</label>
                <label class="form-field">
					<span>L Count</span>
					<input type="number" name="scount" min="0" step="1" id="lcount">
				</label>
                <label class="form-field">
					<span>XL Count</span>
					<input type="number" name="scount" min="0" step="1" id="xlcount">
				</label>
                <label class="form-field">
					<span>Location</span>
					<input type="text" name="address" id="address">
				</label>

                <label class="form-field">
					<span>Country</span>
					<select name="country" id="country">
                        <option value="">Please select a country</option>
                    </select>
				</label>

                <label class="form-field">
					<span>Manager E-mail (Leave empty for public)</span>
                    <input type="email" name="manager_email" id="manager_email">
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
    const settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://ajayakv-rest-countries-v1.p.rapidapi.com/rest/v1/all",
        "method": "GET",
        "headers": {
            "x-rapidapi-key": "72f921eaf4msh5b7db5db0fa7dbap193e34jsneb0d548aa86d",
            "x-rapidapi-host": "ajayakv-rest-countries-v1.p.rapidapi.com"
        }
    };

    const countrySelector = $("select#country");
    $.ajax(settings).done(function (response) {
        response.forEach((item, id) => {
            countrySelector.append(`<option value="${id}">${item.name}</option>`);
        });
    });


    const newForm = $('form#newForm');
    newForm.submit(function (event) {
        event.preventDefault();

        const targetPath = $(this).attr('action');
        const name = $(this).find('input#name').val().trim();
        const ip = $(this).find('input#ip').val().trim();
        const scount = $(this).find('input#scount').val().trim();
        const mcount = $(this).find('input#mcount').val().trim();
        const lcount = $(this).find('input#lcount').val().trim();
        const xlcount = $(this).find('input#xlcount').val().trim();
        const address = $(this).find('input#address').val().trim();
        const country = $(this).find('select#country').val().trim();
        const managerEmail = $(this).find('input#manager_email').val().trim();
        const errorField = $("#errorField");
        errorField.hide();
        
        if(
            name.length <= 0 ||
            ip.length <= 0 ||
            scount.length <= 0 ||
            mcount.length <= 0 ||
            lcount.length <= 0 ||
            xlcount.length <= 0 ||
            address.length <= 0 ||
            country.length <= 0
        ) {
            return errorField.text('Please fill all the required inputs.').show();
        }

        $.ajax({
            url: targetPath,
            type: 'POST',
            dataType: 'JSON',
            data: {
                name: name,
                ip: ip,
                scount: scount,
                mcount: mcount,
                lcount: lcount,
                xlcount: xlcount,
                address: address,
                country: country,
                managerEmail: managerEmail
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
