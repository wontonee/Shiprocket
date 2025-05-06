<?php

use Illuminate\Support\Facades\Route;
use Wontonee\ShipRocket\Http\Controllers\Admin\ShipRocketController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/shiprocket'], function () {
    Route::controller(ShipRocketController::class)->group(function () {
        Route::get('', 'index')->name('admin.shiprocket.index');
        Route::get('settings', 'settings')->name('admin.shiprocket.settings');
        Route::post('settings', 'saveSettings')->name('admin.shiprocket.settings.save');
        Route::get('shipment', 'shipment')->name('admin.shiprocket.shipment');
    });
});