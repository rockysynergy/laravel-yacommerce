<?php

namespace Orq\Laravel\YaCommerce;

use Illuminate\Support\Facades\Route;

class Yac
{
    /**
     * Generates web routes for this package
     */
    public static function webRoutes(): void
    {
        Route::namespace("\\Orq\\Laravel\\YaCommerce\\Controllers\\Admin")->group(function () {
            Route::get('/shop_category/index/{shop_id}', 'YacCategoryController@index')->name('ShopCategoryIndex');
            Route::get('/shop_category/new', 'YacCategoryController@new')->name('ShopCategoryNew');
            Route::post('/shop_category/save', 'YacCategoryController@save')->name('ShopCategorySave');
            Route::get('/shop_category/edit/{id}', 'YacCategoryController@edit')->name('ShopCategoryEdit');
            Route::post('/shop_category/update', 'YacCategoryController@update')->name('ShopCategoryUpdate');

            Route::get('/shop_product/index/{shop_id}', 'YacProductController@index')->name('ProductIndex');
            Route::get('/shop_product/new', 'YacProductController@new')->name('ShopProductNew');
            Route::post('/shop_product/save', 'YacProductController@save')->name('ShopProductSave');
            Route::get('/shop_product/edit/{id}', 'YacProductController@edit')->name('ShopProductEdit');
            Route::post('/shop_product/update', 'YacProductController@update')->name('ShopProductUpdate');

            Route::get('/shop_product_variant/new', 'YacProductVariantController@new')->name('ShopProductVariantNew');
            Route::post('/shop_product_variant/save', 'YacProductVariantController@save')->name('ShopProductVariantSave');
            Route::get('/shop_product_variant/edit/', 'YacProductVariantController@edit')->name('ShopProductVariantEdit');

            Route::get('/shop_order/index/{shop_id}', 'YacOrderController@index')->name('OrderIndex');
            Route::get('/shop_order/edit/{id}', 'YacOrderController@edit')->name('ShopOrderEdit');
            Route::post('/shop_order/update', 'YacOrderController@update')->name('ShopOrderUpdate');
        });
    }

    /**
     * Generates web routes for this package
     */
    public static function apiRoutes(): void
    {
        Route::namespace("\\Orq\\Laravel\\YaCommerce\\Controllers\\Api")->group(function () {
            Route::get('shop_productlist', 'ShopController@productList');
            Route::get('shop_productinfo', 'ShopController@productInfo');
            Route::post('shop_makeorder', 'ShopController@makeOrder');
            Route::get('shop_getallorder', 'ShopController@getAllOrdersForUser');
            Route::post('shop_addcartitem', 'ShopController@addCartItem');
            Route::post('shop_deletecartitem', 'ShopController@deleteCartItem');

            Route::post('shop_repayorder', 'ShopController@repayOrder');
            Route::get('shop_getcartitems', 'ShopController@getCartItems');
            Route::get('shop_getshipaddresses', 'ShopController@getShipAddressesForUser');
        });
    }
}
