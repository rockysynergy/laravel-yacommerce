@extends('layouts.admin.admin')

@section('content')
<div class="content__apps">
    <input type="hidden" value="{{$shopId}}" id="shopId" />
    <nav class="nav__top">
        <div class="nav-item nav-item__new">
            <button type="button" class="layui-btn"><i class="layui-icon layui-icon-add-1"></i>添加</button>
            {{-- 搜索表单 --}}
            <div class="layui-inline">
                <input class="layui-input filter-form-input" name="filterName" id="filterName" placeholder="收件人" autocomplete="off">
            </div>
            <div class="layui-inline">
                <input class="layui-input filter-form-input" name="filterMobile" id="filterMobile" placeholder="电话" autocomplete="off">
            </div>
            <div class="layui-inline">
                <input class="layui-input filter-form-input" name="filterOrderNumber" id="filterOrderNumber" placeholder="订单号" autocomplete="off">
            </div>
            <div class="layui-inline">
                <input class="layui-input filter-form-input" name="filterCreatedAt" id="filterCreatedAt" placeholder="下单日期" autocomplete="off">
            </div>
            <button class="layui-btn" id="btn-search" data-type="reload"><i class="layui-icon layui-icon-search"></i>搜索</button>
            <button class="layui-btn" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></button>
        </div>
    </nav>

    <div id="order-list" lay-filter="order-list"></div>
</div>

{{-- 类别列表toolbar 的模板 --}}
<script type="text/html" id="actionTempl">
    <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
</script>
@endsection

@push('body-scripts')
    <script>
        $(document).ready(function () {
            let shopId = $('#shopId').val();

            layui.use(['table', 'layer', 'laydate'], function () {
                var table = layui.table;
                let laydate = layui.laydate

                laydate.render({elem: '#filterCreatedAt'})

                // 显示类别列表
                let orderTable = table.render({
                    elem: '#order-list'
                    , url: '/admin/shop_order/index/'+shopId+'?m=fetch' //数据接口
                    , toolbar: true
                    , page: true //开启分页
                    , cols: [[ //表头
                        { field: 'id', title: 'ID', width: 60, sort: true}
                        , { field: 'order_number', title: '订单号', width: 200 }
                        , { field: 'exorder_number', title: '微信支付订单号', width: 200 }
                        , { field: 'pay_amount', title: '支付金额', width: 120, templet: function(d){
                            return '￥' + (d.pay_amount/100).toFixed(2);
                        }}
                        , { field: 'pay_info', title: '支付', width: 120, templet: function(d) {
                            let s = '';
                            if (d.pay_method==1) s += '微信支付';
                            if (d.pay_method==2) s += '现金';
                            s += '</br>'
                            if (d.pay_status==1) s += '等待支付';
                            if (d.pay_status==2) s += '支付完成';
                            return s;
                        }}
                        , { field: 'shipment', title: '收件信息', width: 240, templet: function(d) {
                            let s = '<ul>';
                            s += '<li>收件人：'+d.name + '</li>';
                            s += '<li>电话：'+d.mobile + '</li>';
                            s += '<li>地址：'+d.address + '</li>';
                            s += '</ul>';

                            return s;
                        }}
                        , { field: 'created_at', title: '下单日期', width: 180 }
                        , { field: '', title: '操作', width: 260, toolbar: '#actionTempl' }
                    ]]
                });


                // 监听操作事件
                table.on('tool(order-list)', function (obj) {
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                    var tr = obj.tr; //获得当前行 tr 的DOM对象

                    if (layEvent === 'edit') {
                        showEditForm(data.id);
                    }
                });


                let showEditForm = function showEditForm(orderId) {
                    let url = '/admin/shop_order/edit/'+orderId;

                    // 显示编辑表单
                    layer.open({
                        type: 2,
                        content: url,
                        area: ['600px', '400px'],
                        title: '修改',
                        cancel: function (index, layero) {
                            if (confirm('确定要关闭么')) {
                                layer.close(index)
                            }
                            return false;
                        },
                        end: function () {
                            orderTable.reload();
                        }
                    });
                }


            // 刷新
            $('.content__apps').on('click', '#btn-refresh', e => {
                $('.filter-form-input').val('')
                orderTable.reload({
                    url: '/admin/shop_order/index/'+shopId+'?m=fetch'
                })
            })

            // 搜索
            $('.content__apps').on('click', '#btn-search', e => {
                let url = '/admin/shop_order/index/'+shopId+'?m=fetch'
                let filterName = $('#filterName').val()
                url += filterName ? '&filterName='+filterName : ''
                let filterMobile = $('#filterMobile').val()
                url += filterMobile ? '&filterMobile='+filterMobile : ''
                let filterOrderNumber = $('#filterOrderNumber').val()
                url += filterOrderNumber ? '&filterOrderNumber='+filterOrderNumber : ''
                let filterCreatedAt = $('#filterCreatedAt').val()
                url += filterCreatedAt ? '&filterCreatedAt='+filterCreatedAt : ''

                orderTable.reload({
                    url: url,
                    page: {
                        curr: 1
                    }
                })
            })

            });
        });
    </script>
@endpush
