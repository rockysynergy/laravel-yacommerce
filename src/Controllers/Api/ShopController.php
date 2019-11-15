<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Orq\DddBase\DomainException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Service\Api\AuthService;
use Orq\DddBase\IllegalArgumentException;
use Orq\Laravel\YaCommerce\AppService\Api\ShopService;
use Orq\Laravel\YaCommerce\Order\Service\OrderService;
use Orq\Laravel\YaCommerce\Order\Service\CartItemService;
use Orq\Laravel\YaCommerce\Shipment\Service\ShipAddressService;

class ShopController extends Controller
{
    public function productList(Request $request)
    {
        try {
            $shopId = (int) $request->input('shop_id');
            return response()->json(['code' => 0, 'status' => 'success', 'data' => ShopService::getAllProductForShop($shopId)]);
        } catch (DomainException $e) {
            Log::error($e->getMessage());
            return response()->json(['code' => 1, 'status' => 'failed', 'data' => ['msg' => $e->getMessage()]]);
        }
    }

    public function productInfo(Request $request)
    {
        try {
            $productId = (int) $request->input('product_id');
            $shopId = (int) $request->input('shop_id');
            return response()->json(['code' => 0, 'status' => 'success', 'data' => ShopService::getProductInfo($productId, $shopId)]);
        } catch (DomainException $e) {
            Log::error($e->getMessage());
            return response()->json(['code' => 1, 'status' => 'failed', 'data' => ['msg' => $e->getMessage()]]);
        }
    }

    public function makeOrder(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->input('app_user_id'), (int) $request->input('app_id'));
            $payload = ShopService::makeOrder(array_merge($request->all(), ['user_id' => $wqbUser->getId(), 'app_user_id' => $request->header('app-user-id')]));
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => $payload]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function repayOrder(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            $payload = OrderService::repay((int) $request->input('order_id'), $wqbUser);
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => $payload]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function getAllOrdersForUser(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            $d = OrderService::findAllForUser($wqbUser->getId(), $request->input('ptype'), $request->input('pid'));
            $k = $request->input('ptype') . '_' . $request->input('pid') . '_orders';
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => [$k => $d]]);
        } catch (DomainException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function getShipAddressesForUser(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            $d = ShipAddressService::getAllForUser($wqbUser);
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => $d]);
        } catch (DomainException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function addCartItem(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            CartItemService::addItem($wqbUser->getId(), $request->all());
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => []]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function deleteCartItem(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            CartItemService::deleteItem($request->input('item_id'));
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => []]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    public function getCartItems(Request $request)
    {
        try {
            $wqbUser = AuthService::getWqbUser((int) $request->header('app-user-id'), (int) $request->header('app-id'));
            $d = CartItemService::getAllForUser($wqbUser->getId(), $request->input('shop_id'));
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => $d]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }
}
