<?php

namespace Orq\Laravel\YaCommerce\Controllers\Api;

use Illuminate\Http\Request;
use Orq\DddBase\DomainException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Service\Api\AuthService;
use Orq\DddBase\IllegalArgumentException;
use Orq\Laravel\YaCommerce\AppServices\Api\ShopService;
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
            $data = $request->all();
            if (!isset($data['user_id'])) throw new IllegalArgumentException('请提供 user_id', 1574825377);
            $payload = ShopService::makeOrder(array_merge($request->all(), ['app_user_id' => $request->header('app-user-id')]));
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
            $d = OrderService::findAllForUser((int) $request->input('user_id'), $request->input('ptype'), (int) $request->input('pid'));
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
            $d = ShipAddressService::getAllForUser((int) $request->input('user_id'));
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
            CartItemService::deleteItem((int) $request->input('item_id'));
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
            $d = CartItemService::getAllForUser($wqbUser->getId(), (int) $request->input('shop_id'));
            return response()->json(['code' => 0, 'msg' => 'success', 'data' => $d]);
        } catch (DomainException | IllegalArgumentException $e) {
            Log::error('Code: ' . $e->getCode() . ' Msg: ' . $e->getMessage());
            return response()->json(['code' => 1, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }
}
