<?php

Route::get('shop_productlist', 'ShopController@productList');
Route::get('shop_productinfo', 'ShopController@productInfo');
Route::post('shop_makeorder', 'ShopController@makeOrder');
Route::get('shop_getallorder', 'ShopController@getAllOrdersForUser');
Route::post('shop_addcartitem', 'ShopController@addCartItem');
Route::post('shop_deletecartitem', 'ShopController@deleteCartItem');

Route::post('shop_repayorder', 'ShopController@repayOrder');
Route::get('shop_getcartitems', 'ShopController@getCartItems');
Route::get('shop_getshipaddresses', 'ShopController@getShipAddressesForUser');
