<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\BasefriaController;//datatable serverside
use App\Http\Controllers\MovimientoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');


//Route::get('image-upload-preview', [PagoController::class, 'indexpreview'])->name('image-upload-preview');
//Route::post('upload-image', [PagoController::class, 'storeimage'])->name('upload-image');


Route::resource('clientes', ClienteController::class)->names('clientes');
Route::get('clientestabla', [ClienteController::class, 'indextabla'])->name('clientestabla');

//Route::get('basefria', [ClienteController::class, 'indexbf'])->name('basefria');
//Route::resource('basefriadatatable', BasefriaController::class);
Route::get('basefria', [ClienteController::class, 'indexbf'])->name('basefria');//actualizado para serverside
Route::get('basefriatabla', [BasefriaController::class, 'index'])->name('basefriatabla');//actualizado para serverside

Route::get('clientes.createbf', [ClienteController::class, 'createbf'])->name('clientes.createbf');
Route::post('clientes.storebf', [ClienteController::class, 'storebf'])->name('clientes.storebf');
Route::post('basefriacliente/{cliente}', [ClienteController::class, 'updatebf'])->name('updatebf');

Route::post('basefriaclienteRequest', [ClienteController::class, 'updatebfpost'])->name('basefriaRequest.post');

Route::get('clientes.editbf/{cliente}/edit2', [ClienteController::class, 'editbf'])->name('clientes.editbf');

Route::resource('pedidos', PedidoController::class)->names('pedidos');
Route::post('pedidoss.store', [PedidoController::class, 'pedidosstore'])->name('pedidoss.store');//actualizado para serverside
Route::get('pedidostabla', [PedidoController::class, 'indextabla'])->name('pedidostabla');//actualizado para serverside
Route::get('pedidostablahistorial', [PedidoController::class, 'indextablahistorial'])->name('pedidostablahistorial');//actualizado para serverside
Route::get('deudoresoncreate', [PedidoController::class, 'deudoresoncreate'])->name('deudoresoncreate');//actualizado para serverside
Route::get('mispedidostabla', [PedidoController::class, 'mispedidostabla'])->name('mispedidostabla');//actualizado para serverside
Route::get('ruc', [PedidoController::class, 'ruc'])->name('cargar.ruc');
Route::get('rucnombreempresa', [PedidoController::class, 'rucnombreempresa'])->name('rucnombreempresa');
Route::post('pedidos.infopdf', [PedidoController::class, 'infopdf'])->name('pedidos.infopdf');



Route::get('basefria.cargarid', [BasefriaController::class, 'cargarid'])->name('basefria.cargarid');
//Route::get('pedidos.destroyid', [PedidoController::class, 'destroyid'])->name('pedidos.destroyid');
Route::post('pedidodeleteRequest', [PedidoController::class, 'destroyid'])->name('pedidodeleteRequest.post');
Route::post('pedidorestaurarRequest', [PedidoController::class, 'Restaurarid'])->name('pedidorestaurarRequest.post');
Route::post('pedidos.restaurar/{pedido}', [PedidoController::class, 'Restaurar'])->name('pedidos.restaurar');


Route::get('cliente', [PedidoController::class, 'cliente'])->name('cargar.cliente');
Route::get('clientedeasesor', [PedidoController::class, 'clientedeasesor'])->name('cargar.clientedeasesor');
Route::get('asesortiempo', [PedidoController::class, 'asesortiempo'])->name('asesortiempo');
Route::get('clientedeasesorparapagos', [PedidoController::class, 'clientedeasesorparapagos'])->name('cargar.clientedeasesorparapagos');
Route::get('clientedeasesordeuda', [PedidoController::class, 'clientedeasesordeuda'])->name('cargar.clientedeasesordeuda');
Route::get('clientedeudaparaactivar', [PedidoController::class, 'clientedeudaparaactivar'])->name('cargar.clientedeudaparaactivar');

Route::get('tipobanca', [PedidoController::class, 'tipobanca'])->name('cargar.tipobanca');
Route::post('pedidos.agregarruc', [PedidoController::class, 'AgregarRuc'])->name('pedidos.agregarruc');
Route::get('pedidos.mispedidos', [PedidoController::class, 'MisPedidos'])->name('pedidos.mispedidos');
Route::get('pedidos.pagados', [PedidoController::class, 'Pagados'])->name('pedidos.pagados');
Route::get('pedidos.sinpagos', [PedidoController::class, 'SinPagos'])->name('pedidos.sinpagos');
Route::get('operaciones.poratender', [PedidoController::class, 'PorAtender'])->name('operaciones.poratender');
Route::get('operaciones.poratendertabla', [PedidoController::class, 'PorAtendertabla'])->name('operaciones.poratendertabla');
Route::get('operaciones.enatencion', [PedidoController::class, 'EnAtencion'])->name('operaciones.enatencion');
Route::get('operaciones.enatenciontabla', [PedidoController::class, 'EnAtenciontabla'])->name('operaciones.enatenciontabla');
Route::get('operaciones.atendidos', [PedidoController::class, 'Atendidos'])->name('operaciones.atendidos');
Route::get('operaciones.atendidostabla', [PedidoController::class, 'Atendidostabla'])->name('operaciones.atendidostabla');
Route::get('datatable.cargaratendidos', [PedidoController::class, 'cargarAtendidos'])->name('datatable.cargaratendidos');
Route::post('pedidos.atender/{pedido}', [PedidoController::class, 'Atender'])->name('pedidos.atender');
Route::get('operaciones.editatender/{pedido}', [PedidoController::class, 'editAtender'])->name('operaciones.editatender');
Route::post('operaciones.updateatender/{pedido}', [PedidoController::class, 'updateAtender'])->name('operaciones.updateatender');
Route::get('operaciones.showatender/{pedido}', [PedidoController::class, 'showAtender'])->name('operaciones.showatender');
Route::post('pedidos/eliminarAdjunto/{id}', [PedidoController::class, 'eliminarAdjunto'])->name('pedidos.eliminarAdjunto');
Route::post('pedidos.envio/{pedido}', [PedidoController::class, 'Enviar'])->name('pedidos.envio');
Route::post('pedidos.destino/{pedido}', [PedidoController::class, 'Destino'])->name('pedidos.destino');
Route::post('operaciones.sinenvio/{pedido}', [PedidoController::class, 'SinEnviar'])->name('operaciones.sinenvio');

Route::get('pedidos/{adjunto}/descargaradjunto', [PedidoController::class, 'DescargarAdjunto'])->name('pedidos.descargaradjunto');
Route::get('envios.index', [PedidoController::class, 'Envios'])->name('envios.index');
Route::post('envios.recibir/{pedido}', [PedidoController::class, 'Recibir'])->name('envios.recibir');
Route::post('envios.enviar/{pedido}', [PedidoController::class, 'EnviarPedido'])->name('envios.enviar');
Route::post('envios.direccion', [PedidoController::class, 'DireccionEnvio'])->name('envios.direccion');
Route::get('envios.createdireccion/{pedido}', [PedidoController::class, 'createDireccion'])->name('envios.createdireccion');
Route::post('envios.updatedireccion/{direccion}', [PedidoController::class, 'UpdateDireccionEnvio'])->name('envios.updatedireccion');
Route::get('envios.enviados', [PedidoController::class, 'Enviados'])->name('envios.enviados');
Route::post('pedidos/eliminarFoto1/{pedido}', [PedidoController::class, 'eliminarFoto1'])->name('pedidos.eliminarFoto1');
Route::post('pedidos/eliminarFoto2/{pedido}', [PedidoController::class, 'eliminarFoto2'])->name('pedidos.eliminarFoto2');
Route::get('envios/{imagen}/descargarimagen', [PedidoController::class, 'DescargarImagen'])->name('envios.descargarimagen');

Route::resource('pagos', PagoController::class)->names('pagos');
Route::get('pagostabla', [PagoController::class, 'indextabla'])->name('pagostabla');//actualizado para serverside
Route::get('pagostablahistorial', [PagoController::class, 'indextablahistorial'])->name('pagostablahistorial');//actualizado para serverside
Route::get('MisPagosTabla', [PagoController::class, 'MisPagosTabla'])->name('MisPagosTabla');//actualizado para serverside
Route::get('pedidoscliente', [PagoController::class, 'pedidoscliente'])->name('cargar.pedidoscliente');
Route::get('pedidosclientetabla', [PagoController::class, 'pedidosclientetabla'])->name('cargar.pedidosclientetabla');
Route::post('pagodeleteRequest', [PagoController::class, 'destroyid'])->name('pagodeleteRequest.post');
Route::post('validarrelacionruc', [PedidoController::class, 'validarrelacionruc'])->name('validarrelacionruc');
Route::post('pedidostiempo', [ClienteController::class, 'pedidostiempo'])->name('pedidostiempo');

Route::post('pagos.addImgTemp', [PagoController::class, 'addImgTemp'])->name('pagos.addImgTemp');//actualizado para serverside
Route::post('pagos.changeImg', [PagoController::class, 'changeImg'])->name('pagos.changeImg');

Route::post('pago/eliminarPedido/{id}/{pago}', [PagoController::class, 'eliminarPedido'])->name('pago.eliminarPedido');
Route::post('pago/eliminarPago/{id}/{pago}', [PagoController::class, 'eliminarPago'])->name('pago.eliminarPago');
Route::get('pagos.mispagos', [PagoController::class, 'MisPagos'])->name('pagos.mispagos');
Route::get('pagos.pagosincompletos', [PagoController::class, 'PagosIncompletos'])->name('pagos.pagosincompletos');
Route::get('pagos.pagosobservados', [PagoController::class, 'PagosObservados'])->name('pagos.pagosobservados');
Route::get('administracion.porrevisar', [PagoController::class, 'PorRevisar'])->name('administracion.porrevisar');
Route::get('administracion.porrevisartabla', [PagoController::class, 'PorRevisartabla'])->name('administracion.porrevisartabla');//agregado para serverside
//
Route::get('administracion.observados', [PagoController::class, 'Observados'])->name('administracion.observados');//de pagos por revisar index
Route::get('administracion.observadostabla', [PagoController::class, 'Observadostabla'])->name('administracion.observadostabla');//agregado para serverside
Route::get('administracion.abonados', [PagoController::class, 'Abonados'])->name('administracion.abonados');//de pagos por revisar index
Route::get('administracion.abonadostabla', [PagoController::class, 'Abonadostabla'])->name('administracion.abonadostabla');//agregado para serverside
//
Route::get('administracion.aprobados', [PagoController::class, 'Aprobados'])->name('administracion.aprobados');
Route::get('administracion.aprobadostabla', [PagoController::class, 'Aprobadostabla'])->name('administracion.aprobadostabla');//agregado por zubieta
Route::get('administracion.revisar/{pago}', [PagoController::class, 'Revisar'])->name('administracion.revisar');
Route::get('administracion.revisarpago', [PagoController::class, 'Revisarpago'])->name('administracion.revisarpago');//agregado para detalle de revisar

Route::resource('movimientos', MovimientoController::class)->names('movimientos');
Route::get('movimientostabla', [MovimientoController::class, 'indextabla'])->name('movimientostabla');//actualizado para serverside
Route::get('movimientostablaconciliar', [MovimientoController::class, 'indextablaconciliar'])->name('movimientostablaconciliar');//actualizado para serverside
Route::get('tipomovimiento', [MovimientoController::class, 'tipomovimiento'])->name('cargar.tipomovimiento');
Route::get('validar_repetido', [MovimientoController::class, 'repeat'])->name('validar_repetido');
Route::post('movimientodeleteRequest', [MovimientoController::class, 'destroyid'])->name('movimientodeleteRequest.post');

Route::post('administracion.updaterevisar/{pago}', [PagoController::class, 'updateRevisar'])->name('administracion.updaterevisar');
Route::post('administracion.updaterevisar.post', [PagoController::class, 'updateRevisarpost'])->name('administracion.updaterevisar.post');



Route::get('pagos/{imagen}/descargarimagen', [PagoController::class, 'DescargarImagen'])->name('pagos.descargarimagen');

Route::get('asesorespago', [PagoController::class, 'asesorespago'])->name('asesorespago');
Route::get('clientescreatepago', [PagoController::class, 'clientescreatepago'])->name('clientescreatepago');

Route::resource('users', UserController::class)->names('users');
Route::post('reset/{user}', [UserController::class, 'reset'])->name('user.reset');
Route::resource('roles', RoleController::class)->names('roles');
Route::get('users.asesores', [UserController::class, 'Asesores'])->name('users.asesores');
Route::get('users.asesorestabla', [UserController::class, 'Asesorestabla'])->name('users.asesorestabla');////asesores

Route::get('users.llamadas', [UserController::class, 'Llamadas'])->name('users.llamadas');////llamadas
Route::get('users.llamadastabla', [UserController::class, 'Llamadastabla'])->name('users.llamadastabla');////llamadas

Route::post('users.asignarsupervisor/{user}', [UserController::class, 'AsignarSupervisor'])->name('users.asignarsupervisor');
Route::post('users.asignarencargadopost', [UserController::class, 'AsignarEncargadopost'])->name('users.asignarencargadopost');////
Route::post('users.asignarjefellamadaspost', [UserController::class, 'AsignarJefellamadaspost'])->name('users.asignarjefellamadaspost');////
Route::post('users.asignarllamadaspost', [UserController::class, 'AsignarLlamadaspost'])->name('users.asignarllamadaspost');


Route::post('users.asignaroperario/{user}', [UserController::class, 'AsignarOperario'])->name('users.asignaroperario');
Route::post('users.asignaroperariopost', [UserController::class, 'AsignarOperariopost'])->name('users.asignaroperariopost');

Route::post('users.asignarsupervisorpost', [UserController::class, 'AsignarSupervisorpost'])->name('users.asignarsupervisorpost');
Route::post('users.asignarasesorpost', [UserController::class, 'AsignarAsesorpost'])->name('users.asignarasesorpost');////

Route::post('users.asignarjefe/{user}', [UserController::class, 'AsignarJefe'])->name('users.asignarjefe');
Route::get('users.misasesores', [UserController::class, 'MisAsesores'])->name('users.misasesores');
Route::post('users.asignarmetaasesor/{user}', [UserController::class, 'AsignarMetaAsesor'])->name('users.asignarmetaasesor');
Route::get('users.encargados', [UserController::class, 'Encargados'])->name('users.encargados');
Route::post('users.asignarmetaencargado/{user}', [UserController::class, 'AsignarMetaEncargado'])->name('users.asignarmetaencargado');
Route::get('users.operarios', [UserController::class, 'Operarios'])->name('users.operarios');
Route::get('users.misoperarios', [UserController::class, 'MisOperarios'])->name('users.misoperarios');
Route::get('users.jefes', [UserController::class, 'Jefes'])->name('users.jefes');

Route::post('VentaPorFechas', [PdfController::class, 'VentaPorFechas'])->name('VentaPorFechas');
Route::post('IngresoPorFechas', [PdfController::class, 'IngresoPorFechas'])->name('IngresoPorFechas');
Route::get('reportes.index', [PdfController::class, 'index'])->name('reportes.index');
Route::get('reportes.misasesores', [PdfController::class, 'MisAsesores'])->name('reportes.misasesores');
Route::get('reportes.operaciones', [PdfController::class, 'Operaciones'])->name('reportes.operaciones');

Route::resource('notifications', NotificationsController::class)->names('notifications');
Route::get('notifications.get', [NotificationsController::class, 'getNotificationsData'])->name('notifications.get');
Route::get('markAsRead', function(){
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('markAsRead');
Route::post('/mark-as-read', [NotificationsController::class, 'markNotification'])->name('markNotification');

//PDF
    //MODULO PEDIDOS
    Route::get('PDF/{pedido}/pedido', [PdfController::class, 'pedidosPDF'])->name('pedidosPDF');
    //MODULO REPORTES    
    Route::post('reporte/pedidosporfechas', [PdfController::class, 'PedidosPorFechas'])->name('pedidosporfechas');
    Route::post('reporte/pedidosporasesor', [PdfController::class, 'PedidosPorAsesor'])->name('pedidosporasesor');
    Route::post('reporte/pedidosporasesores', [PdfController::class, 'PedidosPorAsesores'])->name('pedidosporasesores');
    Route::post('reporte/pagosporfechas', [PdfController::class, 'PagosPorFechas'])->name('pagosporfechas');    
    Route::post('reporte/pagosporasesor', [PdfController::class, 'PagosPorAsesor'])->name('pagosporasesor');
    Route::post('reporte/pagosporasesores', [PdfController::class, 'PagosPorAsesores'])->name('pagosporasesores');
    Route::get('imprimir/venta/{venta}', [PdfController::class, 'ticketVentaPDF'])->name('ticketVentaPDF');


//EXCEL EXPORTABLES
    //MODULO PERSONAS
    // Route::get('clientesExcel', [ExcelController::class, 'clientesExcel'])->name('clientesExcel');
    Route::post('usuariosExcel', [ExcelController::class, 'usuariosExcel'])->name('usuariosExcel');
    Route::post('clientesExcel', [ExcelController::class, 'clientesExcel'])->name('clientesExcel');
    Route::post('clientespedidosExcel', [ExcelController::class, 'clientespedidosExcel'])->name('clientespedidosExcel');
    // Route::get('basefriaExcel', [ExcelController::class, 'basefriaExcel'])->name('basefriaExcel');
    Route::post('basefriaExcel', [ExcelController::class, 'basefriaExcel'])->name('basefriaExcel');
    Route::post('basefriaporasesorExcel', [ExcelController::class, 'basefriaporasesorExcel'])->name('basefriaporasesorExcel');
    //MODULO ADMINISTRACION
    // Route::get('pagosaprobadosExcel', [ExcelController::class, 'pagosaprobadosExcel'])->name('pagosaprobadosExcel');
    Route::post('pagosaprobadosExcel', [ExcelController::class, 'pagosaprobadosExcel'])->name('pagosaprobadosExcel');
    //MODULO PAGOS
    // Route::get('pagosExcel', [ExcelController::class, 'pagosExcel'])->name('pagosExcel');
    // Route::get('mispagosExcel', [ExcelController::class, 'mispagosExcel'])->name('mispagosExcel');
    // Route::get('pagosincompletosExcel', [ExcelController::class, 'pagosincompletosExcel'])->name('pagosincompletosExcel');
    // Route::get('pagosobservadosExcel', [ExcelController::class, 'pagosobservadosExcel'])->name('pagosobservadosExcel');
    Route::post('pagosExcel', [ExcelController::class, 'pagosExcel'])->name('pagosExcel');
    Route::post('mispagosExcel', [ExcelController::class, 'mispagosExcel'])->name('mispagosExcel');
    Route::post('pagosincompletosExcel', [ExcelController::class, 'pagosincompletosExcel'])->name('pagosincompletosExcel');
    Route::post('pagosobservadosExcel', [ExcelController::class, 'pagosobservadosExcel'])->name('pagosobservadosExcel');

    Route::post('porrevisarExcel', [ExcelController::class, 'porrevisarExcel'])->name('porrevisarExcel');
    //MODULO OPERACION
    // Route::get('pedidosporatenderExcel', [ExcelController::class, 'pedidosporatenderExcel'])->name('pedidosporatenderExcel');
    // Route::get('pedidosenatencionExcel', [ExcelController::class, 'pedidosenatencionExcel'])->name('pedidosenatencionExcel');
    // Route::get('pedidosatendidosExcel', [ExcelController::class, 'pedidosatendidosExcel'])->name('pedidosatendidosExcel');
    Route::post('pedidosporatenderExcel', [ExcelController::class, 'pedidosporatenderExcel'])->name('pedidosporatenderExcel');
    Route::post('pedidosenatencionExcel', [ExcelController::class, 'pedidosenatencionExcel'])->name('pedidosenatencionExcel');
    Route::post('pedidosatendidosExcel', [ExcelController::class, 'pedidosatendidosExcel'])->name('pedidosatendidosExcel');
    //MODULO ENVIOS

    // Route::get('pedidosporenviarExcel', [ExcelController::class, 'pedidosporenviarExcel'])->name('pedidosporenviarExcel');
    Route::post('pedidosporenviarExcel', [ExcelController::class, 'pedidosporenviarExcel'])->name('pedidosporenviarExcel');

    //MODULO PEDIDOS
    // Route::get('pedidosExcel', [ExcelController::class, 'pedidosExcel'])->name('pedidosExcel');
    // Route::get('mispedidosExcel', [ExcelController::class, 'mispedidosExcel'])->name('mispedidosExcel');
    // Route::get('pedidospagadosExcel', [ExcelController::class, 'pedidospagadosExcel'])->name('pedidospagadosExcel');
    // Route::get('pedidossinpagosExcel', [ExcelController::class, 'pedidossinpagosExcel'])->name('pedidossinpagosExcel');
    Route::post('pedidosExcel', [ExcelController::class, 'pedidosExcel'])->name('pedidosExcel');
    Route::post('mispedidosExcel', [ExcelController::class, 'mispedidosExcel'])->name('mispedidosExcel');
    Route::post('pedidospagadosExcel', [ExcelController::class, 'pedidospagadosExcel'])->name('pedidospagadosExcel');
    Route::post('pedidossinpagosExcel', [ExcelController::class, 'pedidossinpagosExcel'])->name('pedidossinpagosExcel');
    //REPORTES
    Route::post('reporte/pedidosgeneralexcel', [ExcelController::class, 'pedidosgeneralexcel'])->name('pedidosgeneralexcel');
    Route::post('reporte/pedidosporfechasexcel', [ExcelController::class, 'pedidosporfechasExcel'])->name('pedidosporfechasexcel');
    Route::post('reporte/pedidosporasesorexcel', [ExcelController::class, 'pedidosporasesorExcel'])->name('pedidosporasesorexcel');
    Route::post('reporte/pedidosoperacionesexcel', [ExcelController::class, 'pedidosoperacionesexcel'])->name('pedidosoperacionesexcel');
    Route::post('reporte/pagosporasesorexcel', [ExcelController::class, 'pagosporasesorExcel'])->name('pagosporasesorexcel');
    Route::post('reporte/pagosporasesoresexcel', [ExcelController::class, 'pagosporasesoresExcel'])->name('pagosporasesoresexcel');
    
    Route::post('reporte/entregadosporfechasexcel', [ExcelController::class, 'entregadosporfechasexcel'])->name('entregadosporfechasexcel');
    
/* Route::group(['middleware' => ['permission:pedidos.index']], function () {
    Route::get('pedidos.index', [PedidoController::class, 'index']);
}); */

});
