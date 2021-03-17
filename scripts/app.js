$(() => {
    const loginForm = $('form#loginForm');
    loginForm.submit(function (event) {
        event.preventDefault();
        let username = "";
        const targetPath = $(this).attr('action');
        const formRole = $(this).attr('role');
        if(formRole != 'admin') { username = $(this).find('input#username').val().trim(); }
        const password = $(this).find('input#password').val().trim();
        const errorField = $("#errorField");
        errorField.hide();
        
        if(formRole != 'admin' && username.length <= 0) {
            return errorField.text('Username cannot be empty.').show();
        }

        if(password.length <= 0) {
            return errorField.text('Password cannot be empty.').show();
        }

        let postData;
        if(formRole == 'user-register') {
            const name = $(this).find('input#name').val().trim();
            const surname = $(this).find('input#surname').val().trim();
            const email = $(this).find('input#email').val().trim();

            if(name.length <= 0) {
                return errorField.text('Name cannot be empty.').show();
            }
            
            if(surname.length <= 0) {
                return errorField.text('Surname cannot be empty.').show();
            }
            
            if(email.length <= 0) {
                return errorField.text('E-mail cannot be empty.').show();
            }
            
            postData = {
                name: name,
                surname: surname,
                email: email,
                username: username,
                password: password
            };
        } else if(formRole == 'admin') {
            const email = $(this).find('input#email').val().trim();
            
            if(email.length <= 0) {
                return errorField.text('E-mail cannot be empty.').show();
            }

            postData = {
                email: email,
                password: password
            }
        } else {
            postData = {
                username: username,
                password: password
            };
        }

        $.ajax({
            url: targetPath,
            type: 'POST',
            dataType: 'JSON',
            data: postData,
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

    const assignRelayToDrawer = (drawerId, value) => {
        $.ajax({
            url: '/api/management/assign-relay.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
                drawerId: drawerId,
                relayNumber: value
            },
            success: function (data) {
                if(data.status == 'success') {
                    Swal.fire('Success', data.message, 'success').then(() => { window.location = ''; });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }, error: () => {
                Swal.fire('Error', "Internal system error occured.", 'error');
            }
        });
    };

    let lockerRelayAssigner = $('.lockerRelayAssigner');
    lockerRelayAssigner.click(function (e) {
        e.preventDefault();

        const lockerId = $(this).attr('data-locker-id');
        const drawerId = $(this).attr('data-drawer-id');

        Swal.fire({
            title: 'Assign a Relay',
            input: 'select',
            inputOptions: {
                1: "Relay 1",
                2: "Relay 2",
                3: "Relay 3",
                4: "Relay 4",
                5: "Relay 5",
                6: "Relay 6",
                7: "Relay 7",
                8: "Relay 8",
                9: "Relay 9",
                10: "Relay 10",
                11: "Relay 11",
                12: "Relay 12",
                13: "Relay 13",
                14: "Relay 14",
                15: "Relay 15",
                16: "Relay 16"
            },
            inputPlaceholder: 'Select a relay number.',
            showCancelButton: true,
            inputValidator: (value) => {
                if (value >= 1 && value <= 16) {
                    if(relayAssignments != undefined && relayAssignments.includes(value)) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This relay is attached with another drawer!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Continue!'
                          }).then((result) => {
                            if (result.isConfirmed) {            
                                assignRelayToDrawer(drawerId, value);
                            }
                          })
                    } else {    
                        assignRelayToDrawer(drawerId, value);
                    }
                } else {
                    Swal.fire('Error', 'You need to select one of the relays.', 'error');
                }
            }
          });
    });

    let lockerReservationForm = $("form#loggedInReservation");
    lockerReservationForm.submit(function (e) {
        e.preventDefault();

        const lockerId = $(this).find('select[name=locker]').val();
        const drawerSize = $(this).find('select[name=drawerSize]').val();
        const recipientName = $(this).find('input[name=recipientName]').val();
        const recipientEmail = $(this).find('input[name=recipientEmail]').val();
        const recipientPhone = $(this).find('input[name=recipientPhone]').val();
        const barcodeNumber = $(this).find('input[name=barcodeNumber]').val();
        const errorField = $("#errorField");
        const drawerSizes = ['s', 'm', 'l', 'xl'];

        if(
            lockerId == '0' ||
            !(drawerSizes.includes(drawerSize))
        ) {
            errorField.text('Please select a locker and a drawer.').show();
            return;
        }

        if(
            lockerId == '' ||
            drawerSize == '' ||
            recipientName == '' ||
            recipientEmail == '' ||
            recipientPhone == ''
        ) {
            errorField.text('Please fill all the areas.').show();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': $(this).attr('action'),
            'dataType': 'JSON',
            'data': {
                'lockerId': lockerId,
                'drawerSize': drawerSize,
                'recipientName': recipientName,
                'recipientPhone': recipientPhone,
                'recipientEmail': recipientEmail,
                'barcodeNumber': barcodeNumber
            },
            success: (data) => {
                if(data.status == 'success') {
                    errorField.hide();

                    Swal.fire('Success!', data.message, 'success').then(e => {
                        window.location = '/barcode-lookup.php?barcode=' + data.data.barcode;
                    });
                } else {
                    errorField.text(data.message).show();
                    return;
                }
            },
            error: () => {
                errorField.text('Internal server error.').show();
                return;
            }
        });

    });

    let countriesCache = '';
    let lockerReservationSelect = $('select#lockerReservationSelect');
    lockerReservationSelect.change(function (e) {
        const currentValue = $(this).val();
        const lockerDataPreview = $('#lockerDataPreview');
        const errorField = $("#errorField");

        if(currentValue == '0') {
            lockerDataPreview.hide();
            errorField.hide();
            return;
        }

        $.ajax({
            method: "POST",
            url: "/api/get-locker-data.php",
            data: {
                id: currentValue
            },
            dataType: 'JSON',
            success: (data) => {
                if(data.status == 'success') {
                    errorField.hide();
                    if(countriesCache == '') {
                        $.ajax({
                            "async": true,
                            "crossDomain": true,
                            "url": "https://ajayakv-rest-countries-v1.p.rapidapi.com/rest/v1/all",
                            "method": "GET",
                            "headers": {
                                "x-rapidapi-key": "72f921eaf4msh5b7db5db0fa7dbap193e34jsneb0d548aa86d",
                                "x-rapidapi-host": "ajayakv-rest-countries-v1.p.rapidapi.com"
                            }
                        }).done(result => {
                            countriesCache = result;
                            $("#lockerAvailableSizes").text(`S: ${data.data.sAvab} M: ${data.data.mAvab} L: ${data.data.lAvab} XL: ${data.data.xlAvab}`);
                            $("#lockerLocation").text(data.data.address);
                            $("#lockerCountry").text(result[data.data.country_code].name);
                            lockerDataPreview.show();
                        });
                    } else {
                        $("#lockerAvailableSizes").text(`S: ${data.data.sAvab} M: ${data.data.mAvab} L: ${data.data.lAvab} XL: ${data.data.xlAvab}`);
                        $("#lockerLocation").text(data.data.address);
                        $("#lockerCountry").text(countriesCache[data.data.country_code].name);
                        lockerDataPreview.show();
                    }
                } else {
                    lockerDataPreview.hide();
                    errorField.html(data.message)
                            .show();
                    return;
                }
            }, error: () => {
                lockerDataPreview.hide();
                errorField.html('Internal system error occured.')
                          .show();
                return;
            }
        });
    });

});