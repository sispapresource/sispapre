<?php
/*
|--------------------------------------------------------------------------
| Application Routes adendas.list
|--------------------------------------------------------------------------
|coreuser
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', 'HomeController@index');
Route::any('/back_login', 'NewloginController@login');
Route::auth();
Route::get('/home', 'HomeController@index');
Route::get('/home_adendas', 'AdendaController@index');
Route::get('/upload_data', 'CargarDatosController@index');
Route::get('/details', 'DetailsController@index');
Route::get('/details-edit', [
    'uses'=>'DetailsController@editarPresupuesto',
    'as'=>'editar.presupuesto'
]);
Route::post('/agregarCuenta/{centro}',[
    'uses'=>'DetailsController@agregarCuenta',
    'as'=>'agregar.cuenta.centro'
]);
Route::post('/guardarEdit','DetailsController@guardarEditPresupuesto');
Route::get('/verTrazabilidad','DetailsController@verTrazabilidad');
Route::get('/totales/{centro}',[
    'uses'=>'DetailsController@getTotalesCentro',
    'as'=>'details.totales'
]);

Route::get('/update', 'UpdateController@index');
Route::get('/bitacora', 'BitacoraController@index');
Route::get('/log_login', 'LogLoginController@index');

Route::get('/update_details', 'UpdateProyectoController@index');
Route::get('/set_rol', 'RolesController@index');
Route::get('/edit_permisos', 'RolesController@index_edit');

//Modulo Seguridad
Route::get('/home_seguridad', 'SeguridadController@index');
Route::post('/home_seguridad', 'SeguridadController@index');
//Route::get('/seguridad', 'SeguridadController@getList');

//Inspecciones
Route::get('/evaluacion_crear', 'SeguridadController@createInspeccion');
Route::post('/guardarevaluacion/{centrocontable}', 'SeguridadController@storeInspeccion');
Route::get('/home_evaluaciones', 'SeguridadController@getHomeInspecciones');
Route::get('/inspecciones', 'SeguridadController@getInspecciones');
Route::get('/inspeccion_cambiar_estado', 'SeguridadController@setEstadoInspeccion');
Route::get('/inspeccion_descargar', 'SeguridadController@downloadInspeccion');


//Hallazgos
Route::get('/home_hallazgos', [
    'uses' => 'SeguridadController@getHomeHallazgos',
    'as'   => 'home_hallazgos'
]);
Route::get('/hallazgos', 'SeguridadController@getHallazgos');
Route::get('/hallazgo_crear', [
    'uses' => 'SeguridadController@createHallazgo',
    'as'   => 'hallazgo_crear'
]);
Route::get('/hallazgo_cambiar_estado', 'SeguridadController@setEstadoHallazgo');
Route::post('/hallazgo_guardar', 'SeguridadController@saveHallazgo');
Route::get('/hallazgo_descargar', 'SeguridadController@downloadHallazgo');

//Route::post('/upload', 'ItemController@uploadDocument'); to charge files to items
Route::post('/upload', [
    'uses'=>'AdendaController@uploadDocument',
    'as'=>'adenda.cargar'
]);
Route::post('/upload_file', 'CargarDatosController@postUploadCsv');
//Route::post('/upload_file', 'CargarDatosController@postCuentas'); use for charge cuentas

//ajax request
Route::get('/centros', 'HomeController@getCentrosContables');
Route::get('/centros_user', 'RolesController@getCentrosUser');
Route::get('/cuentas', 'DetailsController@getCuentas');
Route::get('/cuentasDivisoras', 'DetailsController@getCuentasDivisoras');
Route::get('/cuentasAutocompletar', 'DetailsController@getcuentasAutocompletar');
Route::get('/cuentasu', 'UpdateController@getCuentas');
Route::get('/dataupdate', 'UpdateController@update');

//Adendas
Route::get('/adendas', 'AdendaController@getList');
Route::get('/adenda_crear', 'AdendaController@create');
Route::get('/adenda_detail', [
    'uses' => 'AdendaController@getDetail',
    'as' => 'adenda.detail'
]);
Route::get('/adenda_editar', [
    'uses' => 'AdendaController@edit',
    'as' => 'adenda.edit'
]);
Route::get('/adenda_editar_data', 'AdendaController@update');
Route::get('/adenda_guardar', 'AdendaController@save');

//Ajustes
Route::get('/ajustes', 'AjusteController@getList');
Route::get('/ajuste_crear', [
    'uses'=>'AjusteController@create',
    'as'=>'ajuste.create'
]);
Route::get('/ajuste_detail', [
    'uses'=>'AjusteController@getDetail',
    'as' => 'ajuste.detail'
]);

Route::get('/ajuste_editar', 'AjusteController@edit');
Route::get('/ajuste_editar_data', 'AjusteController@update');
Route::get('/ajuste_guardar', 'AjusteController@save');

//Items
Route::get('/items', 'ItemController@getList');
Route::get('/item_crear', 'ItemController@create');
Route::get('/item_detail', 'ItemController@getDetail');
Route::get('/item_guardar', 'ItemController@save');

Route::get('/update_details_data', 'UpdateProyectoController@update');
Route::post('/update_rol', 'RolesController@update');
Route::get('/permisos', 'RolesController@getPermisos');
Route::post('/permiso_update', 'RolesController@updatePermisos');
Route::post('/centro_user_update', 'RolesController@updateCentroUser');
Route::get('/comprometido',[
    'uses'=>'GastadoVerController@getComprometido',
    'as'=>'comprometido.ver'
]);
Route::post('/comprometido',[
    'uses'=>'GastadoVerController@getComprometido',
    'as'=>'comprometido.filtrar'
]);
Route::get('/gastado', [
    'uses'=>'GastadoVerController@getGastado',
    'as'=>'gastado.ver'
]);
Route::post('/gastado', [
    'uses'=>'GastadoVerController@getGastado',
    'as'=>'gastado.filtrar'
]);

Route::get('/grafico', 'DetailsController@getGraficoCuentas');
Route::get('data_grafico/{idCentro}', 'DetailsController@getDataGrafico');

Route::get('/grafico_test', 'GraficosController@getData');

Route::get('/export_detail/{tipo}/{centro}', [
    'uses' => 'ExcelController@index',
    'as'   => 'export_detail'
]);

Route::get('/export_detail_corto/{centro}', [
    'uses' => 'ExcelController@ExportDetailCorto',
    'as'   => 'export_detail_corto'
]);
Route::get('/export_detail_division', [
    'uses' => 'ExcelController@ExportDetailDivision',
    'as'   => 'export_detail_division'
]);

Route::get('/export_home', [
    'uses' => 'ExcelController@exportHome',
    'as'   => 'export_home'
]);
Route::get('/informes_home', [
    'uses' => 'ExcelController@informesHome',
    'as'   => 'informes_home'
]);
Route::get('/export_home_adendas', [
    'uses' => 'ExcelController@exportHomeAdenda',
    'as'   => 'export_home_adendas'
]);
Route::get('/exportar_adendas/{centro}/{estado}/{fechai}/{fechaf}', [
    'uses' => 'ExcelController@exportar_adenda_filtrada',
    'as'   => 'exportar_adendas'
]);
Route::get('exportar_actual_adendas',[
    'uses' => 'ExcelController@exportar_actual_adendas',
    'as' => 'exportar_actual_adendas'
]);
Route::post('/adendas/list', [
    'uses'=> 'AdendaController@adendaDataTable',
    'as'=>'adendas.list'
]);


Route::get('/export_adenda/{tipo}/{adenda}', [
    'uses' => 'ExcelController@exportAdenda',
    'as'   => 'export_adenda'
]);

Route::get('/export_ajuste/{tipo}/{ajuste}', [
    'uses' => 'ExcelController@exportAjuste',
    'as'   => 'export_ajuste'
]);

Route::get('/export_detalle_comprometido/{centro}/{idCuenta?}/{categoriaFilter?}/{dateFilterDesde?}/{dateFilterHasta?}/{montoFilterDesde?}/{montoFilterHasta?}/{proveedorFilter?}', [
    'uses' => 'ExcelController@exportDetalleComprometido',
    'as'   => 'export_detalle_comprometido'
]);

Route::get('/export_detalle_gastado/{centro}/{idCuenta?}/{categoriaFilter?}/{dateFilterDesde?}/{dateFilterHasta?}/{montoFilterDesde?}/{montoFilterHasta?}/{proveedorFilter?}', [
    'uses' => 'ExcelController@exportDetalleGastado',
    'as'   => 'export_detalle_gastado'
]);

Route::post('/export_view_detail', 'ExcelController@exportViewDetail');

//Documenots
Route::get('/documentos',[
    'uses'=>'DocumentosController@index',
    'as' => 'documentos.index'
]);
Route::post('/documentos',[
    'uses'=>'DocumentosController@index',
    'as' => 'documentos.index'
]);
Route::get('/{documento}/{centrocontable}/cargar', 'DocumentosController@cargar');
Route::post('/{documento}/{centrocontable}/cargar', 'DocumentosController@guardararchivo');
Route::get  ('/{documento}/{centrocontable}/descargar', 'DocumentosController@descargar');
Route::get('/{documento}/{centrocontable}/docestado', 'DocumentosController@mostrarestado');
Route::post('/{documento}/{centrocontable}/docestado', 'DocumentosController@guardarestado');
Route::resource('/catalogos/documentos','DocumentosController');
/*
Route::get('/documentos', 'DocumentosController@index');
Route::get('/creardocumento/', 'DocumentosController@create');
Route::post('/guardardocumento', 'DocumentosController@store');
Route::get('/{documento}/agregartipos', 'DocumentosController@editar');
Route::post('/{documento}/agregartipos', 'DocumentosController@actualizar');*/



//Centros Contables
Route::get('/cambiarestado', 'CentrosContablesController@cambiarEstado');
Route::post('/guardarestado', 'CentrosContablesController@guardarEstado');

//Tipos proyectos
Route::get('/catalogos', 'CatalogosController@index');

Route::resource('/catalogos/proyecto','TipoProyectoController');
//Propuestas
Route::resource('propuestas', 'PropuestaController');
Route::get('versiones/create/{id}', array('as' => 'createversion', 'uses' => 'VersionPropuestaController@create'));

//Versión propuesta
Route::get('versiones/estado/{id}', array('as' => 'estado', 'uses' => 'VersionPropuestaController@estado'));
Route::post('versiones/estado/{id}', array('as' => 'saveestado', 'uses' => 'VersionPropuestaController@saveestado'));
Route::get('versiones/cartapropuesta/{id}', array('as' => 'cartapropuesta', 'uses' => 'VersionPropuestaController@cartapropuesta'));
Route::resource('versiones', 'VersionPropuestaController');

//detalle version
Route::resource('versiones.detalle', 'DetalleVersionController');

//PresupuestoAvance
Route::resource('presupuestoavance','PresupuestoAvanceController');

//Clientes
Route::resource('/catalogos/cliente','ClienteController');

//PresupuestoItems
Route::resource('/presupuestoitems','PresupuestoItemController');

Route::resource('/configuracion/formula','ConfiguracionFormulaController');