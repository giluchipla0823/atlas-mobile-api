<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HydrateController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\LoadController;
use App\Http\Controllers\DamageController;

//MIDDLEWARE QUE SE DEBE AÑADIR A TODAS LAS RUTAS EXCEPTO LOGIN Y REGISTRO DE DISPOSITIVO (SOLO PERMITE LLAMADAS AUTENTICADAS)
// middleware('auth:sanctum')->

Route::get('/', function() {
   return 'hello api';
});

//AUTH
Route::post('/login',[AuthController::class,'login'])->name('login'); //Realiza el acceso autenticado al sistema

Route::post('/isdeviceregistered',[AuthController::class,'isDeviceRegistered'])->name('isdeviceregistered'); //Consulta si el dispositivo está dado de alta en el sistema
// Route::post('/registerdevice',[AuthController::class,'registerDevice'])->name('registerdevice'); //Da de alta el dispositivo en el sistema
Route::post('/resetpassword',[AuthController::class,'resetPassword'])->name('resetpassword'); //Sustituye la contraseña del usuario dado
Route::post('/readmessage',[AuthController::class,'readMessage'])->name('readmessage'); // POR ACABAR // Marca como leído el mensaje dado // POR ACABAR //

// Devices
Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store'); // Da de alta el dispositivo en el sistema
Route::get('/devices/exists/{uuid}', [DeviceController::class, 'exists'])->name('devices.exists');

//HYDRATE (Rellenado de listas y contenido)
Route::post('/compounds',[HydrateController::class,'listCompounds'])->name('compounds'); //Devuelve las campas disponibles en esta instancia de WPark
Route::post('/pairs',[HydrateController::class,'listPairs'])->name('pairs'); //Devuelve los pares de "tiles" de las páginas menú en esta instancia de WPark

// Route::post('/devicetypes',[HydrateController::class,'deviceTypes'])->name('devicetypes'); //Devuelve los tipos de dispositivo en esta instancia de WPark
Route::get('/device-types', [DeviceTypeController::class, 'index'])->name('device-types.index'); // Devuelve los tipos de dispositivo en esta instancia de WPark

Route::post('/vehicles/datatables', [VehicleController::class, 'datatables'])->name('vehicles.datatables');
Route::get('/vehicles/find-all', [VehicleController::class, 'findAll'])->name('vehicles.find-all');

// RUTAS PROTEGIDAS POR TOKEN DE ACCESO
Route::group(['middleware' => 'auth:sanctum'], function() {

    // Auth
    Route::post('/logout', [AuthController::class,'logout'])->name('logoutapi'); //Cierra la sesión del usuario dado

    // Vehículos
    Route::post('/vininfo', [HydrateController::class,'vinInfo'])->name('vininfo'); //Devuelve la información completa del vehículo dado
    Route::post('/searchlist', [HydrateController::class,'searchList'])->name('searchlist'); //Devuelve la lista de vehículos y filas de una campa dada
//     Route::post('/rowinfobylp',[HydrateController::class,'rowInfo'])->name('rowinfolp'); //Devuelve la información de una fila a partir de su lp
    Route::post('/categoryrows', [HydrateController::class,'categoryRows'])->name('categoryrows'); //Devuelve las filas de una categoría dada a partir de su lpname

//     Route::post('/brandslist',[HydrateController::class,'brandsList'])->name('brandslist'); //Devuelve las marcas disponibles en esta instancia de WPark
    // Route::post('/modelslist',[HydrateController::class,'modelsList'])->name('modelslist'); //Devuelve los modelos disponibles en esta instancia de WPark
    // Route::post('/colorslist',[HydrateController::class,'colorsList'])->name('colorslist'); //Devuelve los colores disponibles en esta instancia de WPark
//     Route::post('/countrieslist',[HydrateController::class,'countriesList'])->name('countrieslist'); //Devuelve los paises disponibles en esta instancia de WPark
    Route::post('/dcodelist',[HydrateController::class,'dcodeList'])->name('dcodelist'); //Devuelve los codigos de destino disponibles en esta instancia de WPark
    // Route::post('/designsvg',[HydrateController::class,'designSvg'])->name('designsvg'); //Devuelve el svg de un modelo dado a partir de su ID
    Route::post('/getposition',[HydrateController::class,'getPosition'])->name('getposition'); //Devuelve la info de una posición dado a partir de su ID
    Route::post('/getdirectpositions',[HydrateController::class,'getDirectPositions'])->name('getdirectpositions'); //Devuelve las posiciones consideradas "directas" en esta instancia de WPark

    // Vehicles
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/search-by-vin/{vin}', [VehicleController::class, 'vinInfo'])->name('vehicles.search-by-vin');

    // Marcas
    Route::get('/brands',[BrandController::class, 'index'])->name('brands.index'); // Devuelve las marcas disponibles en esta instancia de WPark

    // Models
    Route::get('/models', [ModelController::class, 'index'])->name('models.index'); // Devuelve los modelos disponibles en esta instancia de WPark

    // Colors
    Route::get('/colors', [ColorController::class, 'index'])->name('colors.index'); // Devuelve los colores disponibles en esta instancia de WPark

    // Countries
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index'); // Devuelve los paises disponibles en esta instancia de WPark

    // Positions
    // Route::get('/positions/back-to-plant', [PositionController::class, 'getPositionsForBackToPlant'])->name('positions.back-to-plant'); // Posiciones para Back To Plant.
    Route::get('/positions/{position}', [PositionController::class, 'show'])->name('positions.show'); // Obtener información de posición con el id.
    Route::get('/positions/{compound}/back-to-plant', [PositionController::class, 'getPositionsForBackToPlant'])->name('positions.back-to-plant'); // Obtener posición de tipo planta.

    Route::post('/positions/{position}/row-rellocate', [PositionController::class, 'rowRellocate'])->name('positions.row-rellocate');
    Route::post('/positions/info-by-lp', [PositionController::class, 'rowInfo'])->name('positions.row-info-lp'); // Devuelve la información de una fila a partir de su lp
    Route::post('/positions/{position}/lock-or-unlock', [PositionController::class, 'lockOrUnlock'])->name('positions.lock-or-unlock'); // Devuelve la información de una fila a partir de su lp
    Route::get('/positions/{position}/children', [PositionController::class, 'children'])->name('positions.children'); // Devuelve la información de una fila a partir de su lp

    Route::post('/positions/{position}/toggle-row', [PositionController::class,'toggleRow'])->name('positions.toggle-row'); // Activa/desactiva una fila
    Route::get('/positions/{position}/test-slot-available', [PositionController::class, 'testSlotAvailable'])->name('positions.test-slot-available'); // Pruebas para obtener slot disponible de una fila.
    Route::get('/positions/{position}/last-vehicle-positioned', [PositionController::class, 'getLastVehiclePositioned'])->name('positions.last-vehicle-positioned');

    // Rules
    Route::get('/rules', [RulesController::class, 'index'])->name('rules.index');

    // Movements
    Route::post('/confirm',[MovementController::class,'confirm'])->name('confirm'); //Confirma una recomendación y vertifica su validez
    Route::post('/movements/confirm', [MovementController::class,'confirm'])->name('movements.confirm'); // Confirma una recomendación y vertifica su validez
});

Route::post('/designsvg',[HydrateController::class,'designSvg'])->name('designsvg'); //Devuelve el svg de un modelo dado a partir de su ID
Route::get('/positions/{compound}/compound-plant-type', [PositionController::class, 'getCompoundPlantType'])->name('positions.compound-plant-type'); // Obtener posición de tipo planta.
Route::get('/positions/{compound}/compound-plant-buffer', [PositionController::class, 'getCompoundPlantBuffer'])->name('positions.compound-plant-buffer'); // Obtener posición de tipo buffer.


//OPERATIONS
// Route::post('/togglerow',[OperationsController::class,'toggleRow'])->name('togglerow'); //Activa/desactiva una fila
Route::post('/clearrowrellocation',[OperationsController::class,'clearRowRellocation'])->name('clearrowrellocation'); //Limpia una fila antes de reubicar vehículos en ella
Route::post('/vehiclerowrellocation',[OperationsController::class,'vehicleRowRellocation'])->name('vehiclerowrellocation'); //Reubica vehículo en fila (fila completa)
Route::post('/vehiclesinglerellocation',[OperationsController::class,'vehicleSingleRellocation'])->name('vehiclesinglerellocation'); //Reubica vehículo en posición (slot)

//MOVE
Route::post('/recommend',[RulesController::class,'recommend'])->name('recommend'); //Recomendación de posición, núcleo del sistema
Route::post('/reload',[RulesController::class,'reload'])->name('reload'); //Recarga una recomendación (excluye filas ya recomendadas)

// Route::post('/confirm',[MovementController::class,'confirm'])->name('confirm'); //Confirma una recomendación y vertifica su validez
Route::post('/manual',[MovementController::class,'manual'])->name('manual'); //Ubica un vehículo de forma manual (reserva y ubicación simultáneas)
Route::post('/cancel',[MovementController::class,'cancel'])->name('cancel'); //Cancela una recomendación (libera la posición recomendada)
// Route::post('/children',[MovementController::class,'children'])->name('children'); //Devuelve las posiciones "hijos" de una posición dada
// Route::post('/children/{position}', [MovementController::class, 'children'])->name('children'); //Devuelve las posiciones "hijos" de una posición dada
Route::post('/nextslot',[MovementController::class,'nextSlot'])->name('nextslot'); //Devuelve el siguiente slot de un slot dado
Route::post('/correctfirstlane',[MovementController::class,'correctFirstLane'])->name('correctfirstlane'); //Corrige una ubicación en caso de que se haya equivocado al aparcar el primer vehículo en una fila
Route::post('/fishbonelayout',[MovementController::class,'fishboneLayout'])->name('fishbonelayout'); //Devuelve las posiciones en una espiga alrededor de una posición recomendada
Route::post('/rowinfobyid',[MovementController::class,'rowInfo'])->name('rowinfoid'); //Devuelve la información de una fila a partir de su ID

//HANDOVER
Route::post('/receive',[HandoverController::class,'receive'])->name('receive'); //Recibe un vehículo (lo ubica en la posición inicial de la campa dada y le asigna el estado inicial ON_TERMINAL)

// LOADS
Route::post('/createmanualload',[LoadController::class,'createManualLoad'])->name('createmanualload'); //Crea una carga a partir del listado de vehículos dado
Route::post('/createloadonasset',[LoadController::class,'createLoadOnAsset'])->name('createloadonasset'); // POR ACABAR // Crea una carga a partir del listado de vehículos dado y lo vincula a un activo de transporte (tre, barco, camión...) // POR ACABAR//


//DAÑOS
Route::post('/getdmgportion',[DamageController::class,'getDmgPortion'])->name('getdmgportion'); //Devuelve las porciones de daño según AIAG
Route::post('/getdmgseverity',[DamageController::class,'getDmgSeverity'])->name('getdmgseverity'); //Devuelve las severidades de daños
Route::post('/getdmgtype',[DamageController::class,'getDmgType'])->name('getdmgtype'); //Devuelve los tipos de daños
Route::post('/getdmgcomponents',[DamageController::class,'getDmgComponents'])->name('getdmgcomponents'); //Devuelve los componentes de daños

Route::post('/savedamage',[DamageController::class,'saveDamage'])->name('savedamage'); //Guarda un reporte de daños en sistema

//Faltan
/*

Consultar si un vehiculo tiene daños

*/

Route::get('/environment',[AuthController::class,'test'])->name('test');
//Route::get('/environment', function () {
//    return response()->json([
//        'env' => env('APP_ENV'),
//        'url' => env('APP_URL'),
//        'db_host' => env('DB_HOST'),
//    ]);
//});
