<?php

namespace Orq\Laravel\YaCommerce\Controllers\Admin;

use Orq\DddBase\DomainException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Orq\Laravel\YaCommerce\AppServices\Admin\YacCategoryService;
use Orq\Laravel\YaCommerce\Shop\Repository\ShopRepository;

class YacCategoryController extends Controller
{
    public function index($shopId, Request $request)
    {
        $shop = ShopRepository::findById($shopId, true);
        if ($request->input('m') == 'fetch') {
            $categories = YacCategoryService::getCategoriesForShop($shopId);
            return response()->json(['code' => '0', 'msg' => 'success', 'count' => count($categories), 'data' => $categories]);
        }
        return view('YaCommerce::category.index', ['siteName' => '微圈宝', 'shopId' => $shopId]);
    }

    public function new()
    {
        return view('YaCommerce::category.new', ['siteName' => '微圈宝']);
    }

    public function save(Request $request)
    {
        if (Gate::allows('manage-shop', $request->input('shop_id'))) {
            try {
                YacCategoryService::saveNew($request->all());
                return response()->json(['code' => 0, 'msg' => '创建成功']);
            } catch (DomainException $e) {
                return response()->json(['code' => 1, 'msg' => $e->getMessage()]);
            }
        }
    }

    //显示修改表单
    public function edit($id)
    {
        $category = YacCategoryService::getById($id);
        return view('YaCommerce::category.new', ['category' => $category]);
    }

    // 添加数据库记录
    public function update(Request $request)
    {
        if (Gate::allows('manage-shop', $request->input('shop_id'))) {
            try {
                YacCategoryService::updateItem($request->all());
                Log::debug('>>The category to update:'.json_encode($request->all()));
                return response()->json(['code' => 0, 'msg' => '修改成功', 'data' => $request->all()]);
            } catch (\Exception $e) {
                Log::debug($e->getMessage());
                return response()->json(['code' => 2, 'msg' => '修改失败！']);
            }
        }
    }
}
