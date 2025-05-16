<?php

use Illuminate\Support\Facades\Route;
use Wontonee\Shiprocket\Http\Controllers\Admin\ShiprocketController;
use Wontonee\Shiprocket\Http\Controllers\Admin\ShipmentController;
use Wontonee\Shiprocket\Http\Controllers\Admin\SettingsController;
use Wontonee\Shiprocket\Http\Controllers\Admin\OrderController;
use Wontonee\Shiprocket\Http\Controllers\Admin\PickupController;
use Wontonee\Shiprocket\Http\Controllers\Admin\ChannelController;

/**
 * Admin Routes
 */
Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/shiprocket'], function () {


  // Shipment
  Route::get('shipment', [ShipmentController::class, 'shipment'])->name('admin.shiprocket.shipment');
  Route::get('shipment/view/{id}', [OrderController::class, 'view'])->name('admin.shiprocket.shipment.view');


  // Seetings
  Route::get('settings', [SettingsController::class, 'settings'])->name('admin.shiprocket.settings');
  Route::post('settings', [SettingsController::class, 'saveSettings'])->name('admin.shiprocket.settings.save');

  // Pickup
  Route::get('pickup', [PickupController::class, 'index'])->name('admin.shiprocket.pickup');
  Route::post('pickup/save', [PickupController::class, 'savePickupLocation'])->name('admin.shiprocket.pickup.save');

  // Channel
  Route::get('channel', [ChannelController::class, 'index'])->name('admin.shiprocket.channel');
  Route::post('channel/save', [ChannelController::class, 'saveChannel'])->name('admin.shiprocket.channel.save');


  // Order routes
  Route::get('orders/create/{id}', [OrderController::class, 'create'])->name('admin.shiprocket.orders.create');
  Route::get('orders/view/{id}', [OrderController::class, 'view'])->name('admin.shiprocket.orders.view');
  Route::get('orders/cancel/{id}', [OrderController::class, 'cancelOrder'])->name('admin.shiprocket.orders.cancel');
  Route::get('orders/create-awb/{id}', [OrderController::class, 'createAWB'])->name('admin.shiprocket.orders.create.awb');
  Route::get('orders/shipment-cancel/{id}', [OrderController::class, 'shipmentCancel'])->name('admin.shiprocket.orders.shipment-cancel');


  //Route::get('shipment/data', [ShiprocketController::class, 'getShipmentData'])->name('admin.shiprocket.shipment.data');
  //   Route::get('shipment/view/{id}', [ShiprocketController::class, 'viewShipment'])->name('admin.shiprocket.shipment.view');
  //  Route::get('shipment/cancel/{id}', [ShiprocketController::class, 'cancelShipment'])->name('admin.shiprocket.shipment.cancel');


});
