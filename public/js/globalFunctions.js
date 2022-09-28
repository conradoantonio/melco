$(function() {
    $.blockUI.defaults.css = {
        padding:        "15px",
        margin:         0,
        width:          'auto',
        top:            '50%',
        left:           '50%',
        textAlign:      'center',
        color:          'inherit',
        border:         '1px solid #aaa',
        backgroundColor:'#fff',
        cursor:         'wait',
        borderRadius:   '0.25rem',
    };
    
    //Set up the ajax to include csrf token on header
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Fade in the containers
    setTimeout(function() {
        $('div.content-container, div.rows-container, div.row-fluid, div.form-container').fadeIn('low');
    }, 500);

    $('.data-table').DataTable({
        //DataTable Options
    });

    /*toastr.options = {
        "positionClass": "toast-bottom-right",
    }*/

    //Verify if the button for delete multiple can be clickable
    $('body').delegate('.checkMultiple','click', function() {
        var ids_lenght = [];
        $("input.checkMultiple").each(function() {
            if ($(this).is(':checked')) {
                ids_lenght.push($(this).parent().parent().siblings("td:nth-child(2)").text());
            }
        });

        $('.delete-rows, .reject-rows, .disable-rows, .enable-rows').attr('disabled', ids_lenght.length > 0 ? false : true);
    });

    //Set up the tooltip element
    $('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });

    //Set up the select 2 inputs
    $("select.select2").select2({
    });

    //Set up the timepicker inputs
    $(".timepicker").timepicker({
        showInputs: false,
        //defaultTime: false
    });

     //Set up the clockpicker inputs
    /*$('.clockpicker ').clockpicker({
        autoclose: true
    });*/

    //Set up the datepiciker inputs
    $( ".date-picker" ).datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "yyyy-mm-dd",
    });

    $('body').delegate('.refresh-content','click', function() {
        var url = $('div.general-info').data('url');
        var refresh = $('div.general-info').data('refresh');
        var element = $('div.general-info').data('el-loader');

        var config = {
            "element"         : element,
            "refresh"         : refresh,
            "method"          : 'get',
            "container_class" : "rows-container",
            "table_class"     : "data-table",
            "route"           : url,
            "callback"        : "fill_table",
        }

        blockElement(config);
        refreshTable(url);
        unBlockElement();
    });

    $('.cstm-switch-input').change(function() {
        id = $(this).data('row-id');
        el_class = 'checkbox'+$(this).data('row-id');
        url = $('div.general-info').data('url');
        
        var config = {
            "id"        : id,
            "activo"    : $(this).is(":checked") ? 1 : 0,
            "route"     : url.concat('/change-status'),
            "callback"  : 'checkbox_listener',
            "el_class"  : el_class,
        }

        $('.'+el_class).parent().parent().block({
            message:''
        });
        
        ajaxSimple(config);
    });

    //Set up the button to download the excel file
    $('body').delegate('.export-rows', 'click', function() {
        var url = $('div.general-info').data('url')+'/excel/export?';
        var config = {
            "container_id" : "rows-container",
            "table_class"  : "data-table",
            "route"        : url.concat('/filter'),
            "callback"     : "fillTable",
        }

        var filters = $('div.filter-section');

        filters.find('input, select, textarea').each(function(i,e) {
            val = $(this).val();
            name = $(this).attr('name');
            console.log(name,val);

            /*Atribute name must exist*/
            if ( name !== undefined ) {
                if ( i == 0 ) {//First iteration
                    url = url.concat(name+'='+val);
                } else {
                    url = url.concat('&'+name+'='+val);
                }
            }
        });
        window.location.href = url;
    });

    //Configure the modal and form properties to upload files
    $('body').delegate('.upload-content','click', function() {
        var row_id = $(this).data('row-id');
        var path = $(this).data('path');
        var rename = $(this).data('rename');
        var resize = $(this).data('resize');
        var action = $(this).data('route-action');

        //console.info('Ruta: '+path, '\nRenombrar: '+rename, '\nDimensiones: '+resize, '\nRuta: '+action);
        var myDropzone = Dropzone.forElement(".myDropzone");

        if (typeof $(this).data('resize') !== 'undefined') {
            $('#rule-container').find('p').removeClass('hide');
            $('#rule-container').children('p').find('strong').text(resize.width+'x'+resize.height+ ' px');
        }

        myDropzone.options.url = action;
        console.log(row_id);
        $('form#dropzone-form').find('input#row_id').val(row_id);
        $('form#dropzone-form').find('input#path').val(path);
        $('form#dropzone-form').find('input#rename').val(rename);
        $('form#dropzone-form').find('input#resize').val(JSON.stringify(resize));
        /*myDropzone.on("queuecomplete", function(file) {
            if (typeof $('button.upload-content').data('refresh') !== 'undefined') {
                refreshGalery(window.location.href)
            }
        });*/
    });

    //Configure the modal to clean the files and reload the galery if neccesary when this is closed by the user
    $('body').delegate('div#modal-upload-content','hidden.bs.modal', function() {
        var myDropzone = Dropzone.forElement(".myDropzone");
        var route = $('.upload-content').data('reload-url') ? $('.upload-content').data('reload-url') : $('div.general-info').data('url');
        //First check if files where uploaded, if so, refresh the galery
        if ( typeof $('.upload-content').data('refresh') !== 'undefined' ) {
            if ( myDropzone.files.length > 0 ) {
                if ( $('.upload-content').data('refresh') == 'table' ) {
                    refreshTable( route );
                } else if ( $('.upload-content').data('refresh') == 'content' ) {
                    refreshGalery( route );
                } else if ( $('.upload-content').data('refresh') == 'galery' ) {
                    refreshGalery( route );
                }
            }
        }

        //Clear dropzone files
        myDropzone.removeAllFiles();
        $('#rule-container').find('p').addClass('hide');
        $(this).find('input.form-control').val('');
    });

    //Configure the modal and form properties to import with excel
    $('body').delegate('.import-excel','click', function() {
        var action = $('div.general-info').data('url')+'/excel/import';
        var fields = $(this).data('fields');
        $('form#form-import').get(0).setAttribute('action', action);
        $('form#form-import').find('strong#fields').text(fields);
    });

    //Clear modal inputs
    $('div.modal').on('hidden.bs.modal', function (e) {
        $(this).find('div.form-group').removeClass('has-error');
        $(this).find("input.form-control").val('');
        $(this).find("textarea.form-control").val('');
        $(this).find("select.form-control").val(0);
        /*$('#foto_perfil').croppie('destroy');
        $('.upload-cr-pic').croppie('destroy');*/
    });

    //Clear button
    $(".clear-filters").on('click',function(){
        $('div.filter-section').find("input.form-control").val('');
        $('div.filter-section').find("select.form-control").val("");
    })

    //Send a request for disable/enable a row
    $('body').delegate('.disable-rows, .enable-rows','click', function() {
        var route = $('div.general-info').data('url')+'/change-status';
        var refresh = $('div.general-info').data('refresh');
        if ($(this).data('text_msg')){//Custom message in swal
            var swal_msg = $(this).data('text_msg');
        } else {
            var swal_msg = change_to == 1 ? 'habilitarán' : 'inhabilitarán';
        }
        var reload_url = $(this).data('reload-url');
        var ids_array = [];
        var change_to = $(this).data('change-to');

        $("input.checkMultiple").each(function() {
            if ($(this).is(':checked')) {
                ids_array.push($(this).parent().parent().siblings("td:nth-child(2)").text());
            }
        });
        if (ids_array.length > 0) {
            swal({
                title: 'Se '+swal_msg+ ' '+ids_array.length+' registro(s), ¿Está seguro de continuar?',
                icon: 'warning',
                buttons:["Cancelar", "Aceptar"],
                dangerMode: true,
            }).then((accept) => {
                if (accept) {
                    config = {
                        'route'     : route,
                        'ids'       : ids_array,
                        'refresh'   : refresh,
                        'change_to' : change_to,
                    }
                    if (reload_url !== undefined) {//If required, we can manipulate the reload url
                        config["reload_url"] = reload_url;
                    }
                    loadingMessage();
                    ajaxSimple(config);
                }
            }).catch(swal.noop);
        }
    });
    

    //Send a request for a single delete
    $('body').delegate('.enable-row, .disable-row','click', function() {
        var route = $('div.general-info').data('url')+'/change-status';
        var refresh = $('div.general-info').data('refresh');
        var ids_array = [];
        var row_id = $(this).hasClass('special-row') ? $(this).data('row-id') : $(this).parent().siblings("td:nth-child(1)").text();
        var row_name = $(this).hasClass('special-row') ? $(this).data('row-name') : $(this).parent().siblings("td:nth-child(2)").text();
        var change_to = $(this).data('change-to');
        var swal_msg = change_to == 1 ? 'habilitado' : 'inhabilitado';
        var reload_url = $(this).data('reload-url');
        ids_array.push(row_id);

        swal({
            title: 'Se marcará el registro '+row_name+' con el status de "'+swal_msg+'" ¿Está seguro de continuar?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'     : route,
                    'ids'       : ids_array,
                    'change_to' : change_to,
                    'refresh'   : refresh,
                }
                if ( reload_url !== undefined ) {//If required, we can manipulate the reload url
                    config["reload_url"] = reload_url;
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    //Send a request for a single delete
    $('body').delegate('.delete-row','click', function() {
        var route = $('div.general-info').data('url')+'/delete';
        var refresh = $('div.general-info').data('refresh');
        var ids_array = [];
        var row_id = $(this).hasClass('special-row') ? $(this).data('row-id') : $(this).parent().siblings("td:nth-child(1)").text();
        ids_array.push(row_id);

        swal({
            title: 'Se dará de baja el registro con el ID '+row_id+', ¿Está seguro de continuar?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'     : route,
                    'ids'       : ids_array,
                    'refresh'   : refresh,
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    //Send a request for delete a galery
    $('body').delegate('.delete-galery','click', function() {
        var route = $('div.general-info').data('url')+'/delete';
        var refresh = $('div.general-info').data('refresh');
        var ids_array = [];
        var row_id = $(this).parent().attr('id');
        ids_array.push(row_id);

        swal({
            title: 'Se eliminará la imagen con el ID '+row_id+', ¿Está seguro de continuar?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'     : route,
                    'ids'       : ids_array,
                    'refresh'   : refresh,
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });
        
    //Send a request for multiple delete
    $('body').delegate('.delete-rows','click', function() {
        var route = $('div.general-info').data('url')+'/delete';
        var refresh = $('div.general-info').data('refresh');
        var ids_array = [];
        $("input.checkMultiple").each(function() {
            if($(this).is(':checked')) {
                ids_array.push($(this).parent().parent().siblings("td:nth-child(2)").text());
            }
        });
        if (ids_array.length > 0) {
            
            swal({
                title: 'Se dará de baja '+ids_array.length+' registro(s), ¿Está seguro de continuar?',
                icon: 'warning',
                buttons:["Cancelar", "Aceptar"],
                dangerMode: true,
            }).then((accept) => {
                if (accept) {
                    config = {
                        'route'     : route,
                        'ids'       : ids_array,
                        'refresh'   : refresh,
                    }
                    loadingMessage();
                    ajaxSimple(config);
                }
            }).catch(swal.noop);
        }
    });

    //For cancellations
    $('body').delegate('.cancel-row', 'click', function() {
        id = $(this).data('row-id');
        url = $('div.general-info').data('url');
        $('div#modal-cancell-order form input[name=row_id]').val(id);
        $('div#modal-cancell-order').modal();
    });

    //For refund an order
    $('body').delegate('.refund-row', 'click', function() {
        id = $(this).data('row-id');
        url = $('div.general-info').data('url');
        $('div#modal-refund-order form input[name=row_id]').val(id);
        $('div#modal-refund-order').modal();
    });

    //Filter functions
    $('body').delegate('.filter-rows', 'click', function() {
        var url = $('div.general-info').data('url');
        var config = {
            "container_id" : "rows-container",
            "table_class"  : "data-table",
            "route"        : url.concat('/filter'),
            "callback"     : "fillTable",
        }

        var filters = $('div.filter-section');

        filters.find('input, select, textarea').each(function(i,e) {
            name = $(this).attr('name');
            //Name must exist
            if ( name !== undefined ) {
                config[name] = $(this).val();
            }
        });

        loadingMessage();
        ajaxSimple(config);
    });

    //Pusher code, it verifies if is neccesary to reload some page content.
    Pusher.logToConsole = false;

    var pusher = new Pusher('899b26aeff40ad874297', {
        cluster: 'us2',
        forceTLS: true
    });

    var channel = pusher.subscribe('refresh-channel');
    channel.bind('refresh-event', function(e) {
        console.log(e);
        if ( e.data.url == window.location.href ) {
            if ( e.data.refresh == 'table' ) {
                refreshTable( e.data.url );
                if ( e.data.toastr ) {
                    toastr.info('Tienes un nuevo pedido por atender');
                }
            } 
            //Maybe add toastr to notify :D
        } else {
            console.info(e.data);
            console.log('There is not any element to reload \nURL:' +e.data.url);
        }
    });
});

/*Callback for refresh table*/
function fillTable(response, config)
{
    refreshHtml(response, config.container_id, config.table_class);
}

/*Callback for refresh galery*/
function fillGalery(response, config) 
{
    //Reset array of photos
    id_photos = [];
    $('.delete-content').addClass('disabled');
    refreshGalery(response.url);
}

function blockElement(config) {
    elem = config.element ? config.element : 'card';
    msg = config.msg ? config.msg : 'Por favor, espere...'
    $("."+elem).block({
        /*timeout:   2000,*/
        message:'<div class="spinner-grow spinner-grow-sm" role="status">\n' +
        '  <span class="sr-only">Cargando...</span>\n' +
        '</div> <span class="ml-2">'+msg+'</span>'
    });
}

function unBlockElement(element_class) {
    element_class ? $("div."+element_class).unblock() : $("div.card").unblock();
}

//Shows the loading swal
function loadingMessage(msg = null) {
    swal({
        title: msg ? msg : 'Espere un momento porfavor',
        buttons: false,
        closeOnEsc: false,
        closeOnClickOutside: false,
        content: {
            element: "div",
            attributes: {
                innerHTML:"<i class='mdi mdi-48px mdi-spin mdi-loading'></i>"
            },
        }
    }).catch(swal.noop);
}

//Reload a table, then initializes it as datatable
function refreshTable(url, column, table_class, container_class) {
    $('.delete-rows, .disable-rows, .reject-rows, .enable-rows').attr('disabled', true);
    var table = table_class ? $("table."+table_class).DataTable() : $("table.data-table").DataTable();
    var container = container_class ? $("div."+container_class) : $('div.rows-container');
    table.destroy();
    container.fadeOut();
    container.empty();
    container.load(url, function() {
        container.fadeIn();
        $(table_class ? "table."+table_class : "table.data-table").DataTable({
            "aaSorting": [[ column ? column : 0, "desc" ]]
        });
        $('#example3_wrapper .dataTables_filter input').addClass("input-medium form-control");
        $('#example3_wrapper .dataTables_length select').addClass("select2-wrapper span12 form-control"); 
    });
    container.addClass('table-responsive');
}

//Reload an html section
function refreshHtml(html, container_class, table_class = false, column = false) {
    var container = container_class ? $("div."+container_class) : $('div.content-container');
    container.fadeOut();
    container.empty();
    container.html(html);

    if ( table_class ) {
        $(table_class ? "table."+table_class : "table.data-table").DataTable({
            "aaSorting": [[ column ? column : 0, "desc" ]]
        });
    }
    container.fadeIn();
}

//Reload a galery module
function refreshGalery(url, container_class) {
    var container = container_class ? $("div."+container_class) : $('div.galery-container');
    container.fadeOut();
    container.empty();
    container.load(url, function() {
        container.fadeIn();
    });
}

//Reload a galery module
function refreshContent(url, container_class) {
    var container = container_class ? $("div."+container_class) : $('div.content-container');
    container.fadeOut();
    container.empty();
    container.load(url, function() {
        container.fadeIn();
    });
}

//Change the src of a img label
function readURL(input) {
    console.log('crea una imagen')
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.cr-image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

function checkbox_listener(data, config) {
    $('.'+config.el_class).parent().parent().unblock();
}
