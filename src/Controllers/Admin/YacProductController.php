<?php
namespace Orq\Laravel\YaCommerce\Controllers;

use Orq\DddBase\DomainException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\AppService\Admin\YacCategoryService;
use Orq\Laravel\YaCommerce\AppServices\Admin\YacProductService;
use Orq\Laravel\YaCommerce\Shop\Repository\ShopRepository;

class YacProductController extends Controller
{
    public function index($shopId, Request $request)
    {
        if ($request->input('m') == 'fetch') {
            $products = YacProductService::getProductsForShop($shopId, true);
            return response()->json(['code'=>'0','msg'=>'success','count'=>count($products), 'data'=>$products]);
        }
        return view('YaCommerce::product.index', ['siteName'=>'微圈宝', 'shopId'=>$shopId, 'shopType'=>ShopRepository::getType($shopId)->type]);
    }

    public function new(Request $request)
    {
        $categories = YacCategoryService::getCategoriesForShop((int)$request->input('shopId'));
        return view('YaCommerce::product.new', ['siteName'=>'微圈宝', 'shopType'=>$request->input('shopType'), 'categories'=>$categories]);
    }

    public function save(Request $request)
    {
        try {
            if ($request->input('id') > 0) {
                YacProductService::updateItem($request->all());
            } else {
                YacProductService::saveNew($request->all());
            }
            return response()->json(['code'=>0, 'msg'=>'操作成功']);
        } catch (DomainException $e) {
            return response()->json(['code'=>1, 'msg'=>$e->getMessage()]);
        }
    }

    //显示修改表单
    public function edit($id, Request $request)
    {
        $shopType = $request->input('shopType');
        $product = YacProductService::getById($id, $shopType);
        $categories = YacCategoryService::getAllSiblingCategories($product->getCategoryId());
        return view('YaCommerce::product.new', ['product' => $product, 'shopType'=>$shopType, 'categories'=>$categories]);
    }

    // 添加数据库记录
    public function update(Request $request)
    {
        try {
            YacProductService::updateItem($request->all());
            return response()->json(['code' => 0, 'msg' => '修改成功', 'data' => $request->all()]);
        } catch (DomainException $e) {
            Log::error($e->getMessage());
            return response()->json(['code' => 2, 'msg' => '修改失败！']);
        }
    }
}
