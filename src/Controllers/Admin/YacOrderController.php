<?php
namespace Orq\Laravel\YaCommerce\Controllers\Admin;

use Orq\DddBase\DomainException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\AppServices\Admin\YacOrderService;

class YacOrderController extends Controller
{
    public function index($shopId, Request $request)
    {
        if ($request->input('m') == 'fetch') {
            $orders = YacOrderService::getOrdersForShop($shopId, $request->all());
            return response()->json(['code'=>'0','msg'=>'success','count'=>count($orders), 'data'=>$orders]);
        }
        return view('YaCommerce::order.index', ['siteName'=>'微圈宝', 'shopId'=>$shopId]);
    }

    //显示修改表单
    public function edit($id)
    {
        $order = YacOrderService::getById($id);
        return view('YaCommerce::order.edit', ['order' => $order]);
    }

    // 添加数据库记录
    public function update(Request $request)
    {
        try {
            YacOrderService::updateItem($request->all());
            return response()->json(['code' => 0, 'msg' => '修改成功', 'data' => $request->all()]);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return response()->json(['code' => 2, 'msg' => '修改失败！']);
        }
    }
}
