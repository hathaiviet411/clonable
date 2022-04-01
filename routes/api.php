<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('maintenance-cost/export/{date}', 'MaintenanceCostController@export')->middleware(null);

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
    });
    Route::group(['middleware' => 'api'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('refresh', 'AuthController@refresh');
        });
        Route::get('profile', 'AuthController@getProfile');
        Route::apiResource('user', 'UserController');
    });

    Route::post('receive-vehicles-data', "CloudController@store");
    Route::get('vehicle', 'MaintenanceScheduleController@vehicle');
    Route::get('vehicle/find/{plate}', 'MaintenanceCostController@findVehicleInformation');
    Route::get('maintenance-vehicles-data', "CloudController@index");

    Route::apiResource('user', "UserController");
    Route::apiResource('role', 'RoleController');
    Route::apiResource('department', 'DepartmentController');
    Route::apiResource('accessories', 'AccessoriesController');

    Route::apiResource('accessory-schedule', 'AccessoryScheduleController');
    Route::apiResource('maintenance-cost', 'MaintenanceCostController')
    ->middleware(['role_or_permission:' . ROLE_HEADQUARTER . '|' . ROLE_OPERATOR . '|' . ROLE_TEAM]);
    Route::get('maintenance-cost/accessories/list', 'MaintenanceCostController@accessoryPullDown');
    Route::apiResource('maintenance-schedule', 'MaintenanceScheduleController');

    Route::get('system-config/list-status-and-type', "SystemConfigController@listStatusAndType");
    Route::get('system-config/list-garage', "SystemConfigController@listGarage");
    Route::get('system-config/vehicle-plates', 'SystemConfigController@vehicle');
    Route::get('system-config/year-conf', 'SystemConfigController@yearConf');
    Route::get('schedule/vehicle/{id}', "ScheduleController@maintenanceCostVehicle");
    Route::put('schedule/vehicle/edit/{id}', "ScheduleController@scheduleAccessoryEdit");
    Route::get('schedule/accessories/{id}/{year}', "ScheduleController@scheduleAccessoriesVehicle");
    Route::get('schedule/accessory', "ScheduleController@scheduleAccessory");
    // Route::post('maintenance-cost/accessories', 'MaintenanceCostController@accessories');
});

