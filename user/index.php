<?php require_once __DIR__ . '/../config.php'; goIfNotLoggedIn('/user/login.php') ?>
<?php $pageTitle = 'Home' ?>
<?php require_once __DIR__ . '/partials/header.php' ?>
<div class="login-wrapper">
    <div class="login-head">Drawer Reservation</div>
    <div class="login-body">
        <div id="errorField" class="error-field" style="display: none;">
        </div>
        <form class="login-form" action="/api/user/reserve.php" id="loggedInReservation" method="post">
            <label class="form-field">
                <span>Available Lockers</span>
                <select name="locker" id="lockerReservationSelect">
                    <option value="0">Please select a locker.</option>
                    <?php
                        $lockersStmt = $db->prepare('SELECT * FROM lockers');
                        $lockersStmt->execute();
                        $lockers = $lockersStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($lockers as $l) {
                            echo '<option value="'. $l['id'] .'">'. $l['name'] .'</option>';
                        }
                    ?>
                </select>
            </label>
            <div class="form-field" class="locker-data-preview" id="lockerDataPreview" style="display: none;">
                <span>Available Sizes: <span id="lockerAvailableSizes">*</span></span><br>
                <span>Location: <span id="lockerLocation">*</span></span><br>
                <span>Country: <span id="lockerCountry">*</span></span>
            </div>
            <label class="form-field">
                <span>Requested Drawer Size</span>
                <select name="drawerSize">
                    <option value="s">Small(200x100mm)</option>
                    <option value="m">Medium(200x200mm)</option>
                    <option value="l">Large(300x300mm)</option>
                    <option value="xl">Extra Large(400x400mm)</option>
                </select>
            </label>
            <label class="form-field">
                <span>Barcode Number [not required]</span>
                <input type="text" name="barcodeNumber">
            </label>
            <label class="form-field">
                <span>Recipient Name</span>
                <input type="text" name="recipientName">
            </label>
            <label class="form-field">
                <span>Recipient Phone Number</span>
                <input type="text" name="recipientPhone">
            </label>
            <label class="form-field">
                <span>Recipient E-mail</span>
                <input type="email" name="recipientEmail">
            </label>
            <div class="form-field">
                <button type="submit" value="0">Reserve This Drawer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php' ?>