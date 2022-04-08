<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ProductOrderController;



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

Route::middleware('api')->group(function () {

    Route::post('/loginApi', 'Admin\LoginController@loginApi');
    Route::post('/user/store', 'Admin\UserController@store');

});

Route::middleware('auth:api')->group(function () {
    Route::get('/infos', function() {
        return response()->json(['message' => 'auth']);
    });
    Route::get('/product/ordersApi', 'Admin\ProductOrderController@indexApi');
    Route::get('/user/{id}/editApi', 'Admin\UserController@editApi');
    Route::PUT('/user/{id}/updateApi', 'Admin\UserController@updateApi');
    Route::get('/order/update/{sub_order_id}/{state}', 'Admin\ProductOrderController@updateSubOrderStateApi');
    Route::get('/CalendarApi', 'Admin\CalendarController@indexApi');
    Route::get('/posApi', 'Admin\PosController@indexApi');
    Route::get('/add-to-cart/{id}', 'Admin\PosController@addToCart');
    Route::get('/updateQty/{key}/{qty}', 'Admin\PosController@updateQty');
    Route::get('/cart/item/remove/{id}', 'Admin\PosController@cartitemremove');
    Route::get('/cart/clear', 'Admin\PosController@cartClear');
    Route::get('/pos/payment-methods', 'Admin\PosController@paymentMethods');
    Route::post('/pos/payment-method/store', 'Admin\PosController@paymentMethodStore');
    Route::post('/pos/payment-method/update', 'Admin\PosController@paymentMethodUpdate');
    Route::post('/pos/payment-method/delete', 'Admin\PosController@paymentMethodDelete');
    Route::post('/product/order/delete', 'Admin\ProductOrderController@orderDelete');
    Route::post('/order/change-table', 'Admin\ProductOrderController@updateOrderTable');
    Route::post('/order/transfert-product', 'Admin\ProductOrderController@transferProducts');
    Route::post('/pos/placeorder', 'Admin\PosController@placeOrder');
});
