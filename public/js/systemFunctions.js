$(function() {
    //Log out swal
    $('body').delegate('.log-out','click', function() {
        swal({
            title: '¿Desea cerrar la sesión?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                window.location.href = baseUrl.concat('/logout');
            }
        }).catch(swal.noop);
    });

    //Display a swal to change the password
    $('body').delegate('.change-password','click', function() {
        swal({
            title: 'Complete los siguientes campos: ',
            buttons: {
                cancel: "Cancelar",
                confirm_p: {
                    text: "Aceptar",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: false
                },
            },
            content: {
                element: "form",
                attributes: {
                    innerHTML:
                        "<form>"+
                            "<div class='row'>"+
                                "<div class='col-sm-12 col-xs-12'>"+
                                    "<div class='form-group'>"+
                                        "<label>Contraseña actual</label>"+
                                        "<input type='text' class='form-control pass-font' id='current-password' name='current-password'>"+
                                    "</div>"+
                                "</div>"+
                                "<div class='col-sm-12 col-xs-12'>"+
                                    "<div class='form-group'>"+
                                        "<label>Nueva contraseña</label>"+
                                        "<input type='text' class='form-control pass-font' id='new-password' name='new-password'>"+
                                    "</div>"+
                                "</div>"+
                                "<div class='col-sm-12 col-xs-12'>"+
                                    "<div class='form-group'>"+
                                        "<label>Confirmar contraseña</label>"+
                                        "<input type='text' class='form-control pass-font' id='confirm-password' name='confirm-password'>"+
                                    "</div>"+
                                "</div>"+
                            "</div>"+
                            "<ul class='error_list'>"+
                                "<li style='display: none;' id='error-fields'>Complete todos los campos</li>"+
                                "<li style='display: none;' id='error-pass'>Contraseña errónea</li>"+
                                "<li style='display: none;' id='error-pass-different'>No coinciden las contraseñas</li>"+
                            "</ul>"+
                        "</form>"
                },
            }
        }).catch(swal.noop);
    });

    //Validate the modal for change the password
    $('body').delegate('.swal-button--confirm_p','click', function() {
        current_pass = $('#current-password').val();
        new_pass = $('#new-password').val();
        confirm_pass = $('#confirm-password').val();

        if (!current_pass || !new_pass || !confirm_pass) {//Empty fields
            $('li#error-fields').fadeIn();
            swal.stopLoading();
        } else if (!($('#confirm-password').val() == $('#new-password').val())) {//Different password
            $('li#error-pass-different').fadeIn();
            swal.stopLoading();
        } else {//Everything ok
            config = {
                'current_pass'  : current_pass,
                'new_pass'      : new_pass,
                'confirm_pass'  : confirm_pass,
                'route'         : baseUrl.concat('/system/change-password'),
                'method'        : 'POST',
            }
            requestNewPassword(config);
        }
    });

    //Verify if all fields are filled
    $('body').delegate('.pass-font','blur', function() {
        if (!$('#current-password').val() || !$('#new-password').val() || !$('#confirm-password').val()) {
            $('li#error-fields').fadeIn();
        } else {
            $('li#error-fields').fadeOut();
        }
    });

    //Verify if the new password is the same that confirm password
    $('body').delegate('#confirm-password, #new-password','blur', function() {
        if ($('#confirm-password').val() == $('#new-password').val()) {
            $('li#error-pass-different').fadeOut();
        } else {
            $('li#error-pass-different').fadeIn();
        }
    });

    //Ajax to change the password
    function requestNewPassword(config) {
        $.ajax({
            method: config.method ? config.method : "POST",
            type: "POST",
            url: config.route,
            data: config,
            success: function(data) {
                swal.stopLoading();
                if (data.status == 'ok') {
                    $('li#error-pass').fadeOut();
                    swal({
                        title: data.msg,
                        icon: "success",
                        buttons: [false, "Aceptar"],
                        timer: 2000
                    }).catch(swal.noop);
                } else {
                    if (data.status == 'error') {
                        $('li#error-pass').fadeIn();
                    }
                }
            },
            error: function(xhr, status, error) {
                swal.stopLoading();
                displayAjaxError(xhr, status, error);
            }
        });
    }
});
