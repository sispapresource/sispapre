var elixir = require('laravel-elixir');
// require('laravel-elixir-image-optimize');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.copy('resources/assets/images','public/images');
    mix.copy('resources/assets/img','public/img');
    mix.copy('resources/assets/fonts','public/fonts');
    mix.copy('resources/assets/css-images','public/css');

    mix.copy([
        'node_modules/animate.css/animate.css',
        'node_modules/bootstrap/dist/css/bootstrap.css',
        'node_modules/chosen-js/chosen.css',
        'node_modules/chosen-js/chosen.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css',
        'node_modules/multiple-select/multiple-select.css',
        'node_modules/font-awesome/css/font-awesome.css',
        'node_modules/datatables/media/css/jquery.dataTables.css',
        'node_modules/toastr/build/toastr.css',
        'node_modules/jquery-autocomplete/jquery.autocomplete.css'
        // 'node_modules/free-jqgrid/dist/css/ui.jqgrid.css'
    ],'resources/assets/plugins/vendor/css');
    mix.copy([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/chosen-js/chosen.jquery.js',
        'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
        'node_modules/multiple-select/multiple-select.js',
        'node_modules/datatables/media/js/jquery.dataTables.js',
        'node_modules/toastr/toastr.js',
        'node_modules/jquery-autocomplete/jquery.autocomplete.js'
        // 'node_modules/free-jqgrid/dist/jquery.jqgrid.src.js',
        // 'node_modules/free-jqgrid/dist/i18n/grid.locale-en.js'
    ],'resources/assets/plugins/vendor/js');
    
    mix.styles([
        '../plugins/vendor/css/chosen.css',
        '../plugins/vendor/css/animate.css',
        '../plugins/vendor/css/bootstrap.css',
        '../plugins/vendor/css/bootstrap-datepicker3.css',
        '../plugins/vendor/css/multiple-select.css',
        '../plugins/vendor/css/font-awesome.css',
        '../plugins/vendor/css/jquery.dataTables.css',
        '../plugins/vendor/css/jquery.autocomplete.css',
        '../plugins/css/toastr.min.css',
        '../plugins/css/sweetalert.css',
        '../plugins/css/jquery-ui-1.12.1.min.css',
        '../plugins/css/ui.jqgrid.css',
        'app.css',
        'style.css'
    ]);
    mix.scripts([
        '../plugins/vendor/js/jquery.js',
        '../plugins/vendor/js/bootstrap.js',
        '../plugins/vendor/js/chosen.jquery.js',
        '../plugins/vendor/js/bootstrap-datepicker.js',
        '../plugins/vendor/js/multiple-select.js',
        '../plugins/vendor/js/jquery.dataTables.js',
        '../plugins/vendor/js/jquery.autocomplete.js',
        '../plugins/js/toastr.min.js',
        '../plugins/js/sweetalert.min.js',
        '../plugins/js/grid.locale-en.js',
        '../plugins/js/jquery.jqGrid.min.js'
        // 'jquery-ui-1.10.4.custom.js',
    ]);

    mix.scripts('adenda_crear.js');
    mix.scripts('adenda_editar.js');
    mix.scripts('ajustes.js');
    mix.scripts('bitacora.js');
    mix.scripts('dashboard.js');
    mix.scripts('detail.js');
    mix.scripts('details-edit.js');
    mix.scripts('gastado.js');
    mix.scripts('hallazgos.js');
    mix.scripts('home_adendas.js');
    mix.scripts('inspecciones.js');
    mix.scripts('item_crear.js');
    mix.scripts('item_detail.js');
    mix.scripts('items.js');
    mix.scripts('log_login.js');
    mix.scripts('scripts.js');
    mix.scripts('seguridad.js');
    mix.scripts('update.js');
    mix.scripts('updateProyecto.js');
    mix.scripts('rol.js'); 
    
    /* mix.version([
        'public/css/all.css',
        'public/js/all.js'
    ]); */
});




