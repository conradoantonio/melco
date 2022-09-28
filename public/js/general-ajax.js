//Autor: Luis Castañeda
//Plagiado por: Conrado Carrillo
function ajaxForm(form_id, config) {
    var formData = new FormData($("form#"+form_id)[0]);
    var button = $("form#"+form_id).find('button.save');
    $.ajax({
        method: $("form#"+form_id).attr('action')? $("form#"+form_id).attr('method') : "POST",
        url: $("form#"+form_id).attr('action'),
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (! config.keepModal ) { $('div.modal').modal('hide'); }
            if ( swal.getState().isOpen ) { swal.close(); }
            unBlockElement(config.element_class);

            if(! config.callback ) {
                swal({
                    title: data.status == 'success' ? 'Bien: ' : 'Error',
                    icon: data.status ? data.status : "success",
                    content: {
                        element: "div",
                        attributes: {
                            innerHTML:"<p class='text-response'>"+data.msg ? data.msg : "¡Cambios guardados exitosamente!"+"</p>"
                        },
                    },
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false,
                    timer: 2000
                }).catch(swal.noop);
            }

            if (config.refresh == 'table') {
                    refreshTable(data.url, config.column, config.table_class, config.container_class);
            } else if (config.refresh == 'galery') {
                    refreshGalery(data.url, config.container_class);
            } else if (config.refresh == 'content') {
                    refreshContent(data.url, config.container_class);
            } else if(config.callback) {
                window[config.callback](data, config);
            } else if(config.redirect) {
                setTimeout( function() {
                    if (data.url) {
                        window.location.href = data.url;
                    }
                }, '2000');
            }
        },
        error: function(xhr, status, error) {
            displayAjaxError(xhr, status, error, config);
        }
    });
}

function ajaxFormModal(form_id, config) {
    console.log(config);
    var formData = new FormData($("form#"+form_id)[0]);
    $.ajax({
        method: $("form#"+form_id).attr('action')? $("form#"+form_id).attr('method') : "POST",
        type: "POST",
        url: $("form#"+form_id).attr('action'),
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: function(data) {
            $('div.modal').modal('hide');
            swal.close();

            if(! config.callback ) {
                swal({
                    title: 'Bien: ',
                    icon: data.status ? data.status : "success",
                    content: {
                        element: "div",
                        attributes: {
                            innerHTML:"<p class='text-response'>"+data.msg ? data.msg : "¡Cambios guardados exitosamente!"+"</p>"
                        },
                    },
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false,
                    timer: 2000
                }).catch(swal.noop);
            }

            if (config.refresh == 'table') {
                    refreshTable(data.url, config.column, config.table_class, config.container_id);
            } else if (config.refresh == 'galery') {
                    refreshGalery(data.url, config.container_id);
            } else if (config.refresh == 'content') {
                    refreshContent(data.url, config.container_id);
            } else if(config.callback) {
                window[config.callback](data, config);
            } else if(config.redirect) {
                setTimeout( function() {
                    if (data.url) {
                        window.location.href = data.url;
                    }
                }, '2000');
            }
        },
        error: function(xhr, status, error) {
            displayAjaxError(xhr, status, error, config);
        }
    });
}

function ajaxSimple(config) {
    $.ajax({
        method: config.method ? config.method : "POST",
        type: config.method ? config.method : "POST",
        url: config.route,
        data: config,
        success: function(data) {
            if (! config.keepModal ) { $('div.modal').modal('hide'); }
            if ( swal.getState().isOpen ) { swal.close(); }
            unBlockElement(config.element_class);

            if(! config.callback ) {
                swal({
                    title: 'Bien: ',
                    icon: data.status ? data.status : "success",
                    content: {
                        element: "div",
                        attributes: {
                            innerHTML:"<p class='text-response'>"+data.msg ? data.msg : "¡Cambios guardados exitosamente!"+"</p>"
                        },
                    },
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false,
                    timer: 2000
                }).catch(swal.noop);
            }

            if (config.refresh == 'table') {
                    refreshTable(data.url, config.column, config.table_class, config.container_id);
            } else if (config.refresh == 'galery') {
                    refreshGalery(data.url, config.container_id);
            } else if (config.refresh == 'content') {
                    refreshContent(data.url, config.container_id);
            } else if(config.callback) {
                window[config.callback](data, config);
            } else if(config.redirect) {
                setTimeout( function() {
                    window.location.href = data.url;
                }, '2000');
            }
        },
        error: function(xhr, status, error) {
            displayAjaxError(xhr, status, error, config);
        }
    });
}

/*function ajaxMSimple(data) {
    url = baseUrl.concat(data.url);
    $.ajax({
        method: "POST",
        url: url,
        data: data,
        success: function(response) {
            fill_text(response, null);
            $("img.product_img").attr("src", baseUrl.concat('/'+response.product_img));
            $("img.customer_img").attr("src", baseUrl.concat('/'+response.customer_img));
            $("a.product_link").prop("href", response.product_link);
            $( ".data-fill" ).find( ".price" ).text('$'+(response.price/100));
            $( ".data-fill" ).find( ".total" ).text('$'+(response.total/100));
            $( ".data-fill" ).find( ".fee" ).text('$'+(response.fee/100));

        },
        error: function(xhr, status, error) {
            displayAjaxError(xhr, status, error, config);
        }
    });
}*/

/*Help to preload data to a modal*/
function fill_text(response, modal_id, prefix, clear = true) {
    clear ? $( ".fill-container" ).addClass('hide') : '';
    var keyNames = Object.keys(response);

    for (var i in keyNames) {
        prop_name = keyNames[i];
        if (response[prop_name]) {
            el_class = prefix ? prefix + prop_name : prop_name;
            $( ".data-fill" ).find( "."+el_class ).text(response[prop_name]);
            $( ".data-fill" ).find( "."+el_class ).closest('.fill-container').removeClass('hide');
        }
    }
    if (modal_id) {
        $('div#'+modal_id).modal();
    }

    //Custom code
    $('.load-bar').addClass('hide');
    $('.detail-fields').removeClass('hide');
}

function displayAjaxError(xhr, status, error, config) {
    if (! config.keepModal ) { $('div.modal').modal('hide'); }
    if ( swal.getState().isOpen ) { swal.close(); }
    unBlockElement();

    if (/^[\],:{}\s]*$/.test(xhr.responseText.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
        display = JSON.parse(xhr.responseText).msg;
        status = JSON.parse(xhr.responseText).status;
    } else {
        display = '';
        status = 'error';
    }
    swal({
        title: '¡Error!',
        icon: status,
        content: {
            element: "div",
            attributes: {
                innerHTML:"Se encontró un problema con ésta petición: <br><span>" + display + "</span><br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>"
            },
        },
    }).catch(swal.noop);
}
