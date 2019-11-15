@extends('layouts.admin.layer')

@section('content')
<div class="content__pg--order-edit">
   {{-- 会费信息表单 --}}
   <form class="layui-form form__app" action="javascript:;" lay-filter="orderEditForm" id="order-edit-form">
        <input type="hidden" value="{{$order['id'] ?? ''}}" name="id" />
        <input type="hidden" value="{{$order['shipaddress_id']}}" name="shipaddress_id" />

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">快递公司</label>
            <div class="layui-input-block">
                <input type="text" name="carrier" required  value="{{$order['carrier'] ?? ''}}"  autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">快递单号</label>
            <div class="layui-input-block">
                <input type="text" name="shipnumber" required  value="{{$order['shipnumber'] ?? ''}}"  autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">支付金额</label>
            <div class="layui-input-block">
                <input type="text" name="pay_amount" required  value="{{$order['pay_amount'] / 100}}" lay-verify="required"  autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">支付方式</label>
            <div class="layui-input-block">
                <select name="pay_method">
                    @foreach (config('kvconfig.pay_method') as $k => $item)
                        <option value="{{$k}}" {{$order['pay_method'] == $k ? 'selected' : ''}}>{{$item}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">支付状态</label>
            <div class="layui-input-block">
                <select name="pay_status">
                    @foreach (config('kvconfig.pay_status') as $k => $item)
                        <option value="{{$k}}" {{$order['pay_status'] == $k ? 'selected' : ''}}>{{$item}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="productForm">提交</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('body-scripts')
    <script>
        layui.config({
             base: '/static/cropper/' //layui自定义layui组件目录
        }).use(['layer', 'form', 'croppers', 'laydate'], function(){
            let layer = layui.layer;
            let form = layui.form;


            form.on('submit(orderEditForm)', function(data){
                let tForm = document.querySelector('#order-edit-form');
                let formData = new FormData(tForm);

                $.ajax({
                    url: '/admin/shop_order/update',
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        console.log(res);
                        if (res.code == 0) {
                            let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭
                        }
                        if (res.code == 1) {
                            alsert(res.msg);
                        }
                    },
                });

                return false;
            });
        });
    </script>
@endpush
