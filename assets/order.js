$(document).ready(function () {
    let shopId = $('#shopId').val();

    layui.use(['table', 'layer'], function () {
        var table = layui.table;

        // 显示类别列表
        let orderTable = table.render({
            elem: '#order-list'
            , url: '/admin/shop_order/index/'+shopId+'?m=fetch' //数据接口
            , page: true //开启分页
            , limit: 15
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

    });

});
