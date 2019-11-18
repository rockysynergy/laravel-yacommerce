<?php
namespace Orq\Laravel\YaCommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use Orq\DddBase\DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\AppServices\Admin\YacProductVariantService;
use Orq\Laravel\YaCommerce\Product\Repository\ProductVariantRepository;

class YacProductVariantController extends Controller
{
    public function new(Request $request)
    {
        $productId = $request->input('productId');
        return view('YaCommerce::product_variant.form', ['siteName'=>'微圈宝', 'productId'=>$productId]);
    }

    public function save(Request $request)
    {
        try {
            if ($request->input('id') > 0) {
                YacProductVariantService::updateItem($request->all());
            } else {
                YacProductVariantService::saveNew($request->all());
            }
            return response()->json(['code'=>0, 'msg'=>'操作成功']);
        } catch (DomainException $e) {
            return response()->json(['code'=>1, 'msg'=>$e->getMessage()]);
        }
    }

    //显示修改表单
    public function edit(Request $request)
    {
        if ($request->input('variant_id')) {
            $variant = ProductVariantRepository::findById((int) $request->input('variant_id'), true);
            return view('YaCommerce::product_variant.form', ['variant' => $variant]);
        }

        $variants = ProductVariantRepository::find([['product_id', '=', $request->input('product_id')]], true);
        return view('YaCommerce::product_variant.list', ['variants' => $variants]);
    }
}
