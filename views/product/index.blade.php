@extends('layouts.admin.admin')

@section('content')
<div class="content__apps">
    <input type="hidden" value="{{$shopId}}" id="shopId" />
    <input type="hidden" value="{{$shopType}}" id="shopType" />

    <nav class="nav__top">
        <div class="nav-item nav-item__new">
            <button type="button" id="btn-add" class="layui-btn"><i class="layui-icon layui-icon-add-1"></i>添加</button>

            {{-- 搜索表单 --}}
            <div class="layui-inline">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline" style="width: 100px;">
                    <select name="filterStatus" id="filterStatus">
                        <option value=""></option>
                        <option value="0">下架</option>
                        <option value="1">上架</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline" style="width: 100px;">
                    <input class="layui-input filter-form-input" name="filterTitle" id="filterTitle" placeholder="名称" autocomplete="off">
                </div>
            </div>
            <button class="layui-btn" id="btn-search" data-type="reload"><i class="layui-icon layui-icon-search"></i>搜索</button>
            <button class="layui-btn" id="btn-refresh"><i class="layui-icon layui-icon-refresh"></i></button>
        </div>
    </nav>
    <div id="product-list" lay-filter="product-list"></div>
</div>

{{-- 类别列表toolbar 的模板 --}}
<script type="text/html" id="actionTempl">
    <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
    @if ($shopType != 'seckill')
        <br><a class="layui-btn layui-btn-xs" lay-event="add_variant">添加产品型号</a>
        <br><a class="layui-btn layui-btn-xs" lay-event="edit_variant">编辑产品型号</a>
    @endif
</script>
@endsection

@push('body-scripts')
    <script>
        $(document).ready(function () {
            let shopId = $('#shopId').val();
            let shopType = $('#shopType').val();

            layui.use(['table', 'layer'], function () {
                var table = layui.table;
                let bCols = [
                    { field: 'id', title: 'ID', width: 50, sort: true}
                    , { field: 'title', title: '名称', width: 220 }
                    , { field: 'cover_pic', title: '图片', width: 160, templet: function(d) {
                        if (d.cover_pic) return '<img src="'+d.cover_pic+'" />';
                        else return '';
                    }}
                    , { field: 'price', title: '价格', width: 100, templet: function(d) {
                        return shopType != 'bp_shop' ? (d.price / 100).toFixed(2) : d.price;
                    }}
                    , { field: 'inventory', title: '库存', width: 80 }
                    , { field: 'status', title: '状态', width: 80, templet: function(d){
                        if (d.status == 0) return '下架';
                        else return '上架';
                    }}
                    , { field: 'variants', title: '规格', width: 300, templet: function(d) {
                        let o = '';
                        d.variants.forEach(item => {
                            o += '<div>';
                            o += '模型：'+item.model;
                            o += ' 状态：'+item.status;
                            o += '<br> 价格：'+item.price / 100;
                            o += ' 显示价格：'+item.showPrice / 100;
                            o += '</div>';
                        });
                        return o;
                    }}

                    , { field: '', title: '操作', width: 160, toolbar: '#actionTempl' }
                ]

                // 秒杀商品表格
                if (shopType == 'seckill') {
                    bCols.splice(3,1,{
                        field: 'price', title: '价格', width: 220, templet: function(d) {
                            let s = '原价：￥' + (d.price / 100).toFixed(2) + '<br>';
                                s += '秒杀价：￥' + (d.sk_price / 100).toFixed(2);

                            return s;
                        }
                    });
                    bCols.splice(4,1,{
                        field: 'price', title: '库存', width: 150, templet: function(d) {
                            let s = '总库存：' + d.total + '<br>';
                                s += '已秒杀：' + d.sold;

                            return s;
                        }
                    });
                    bCols.splice(5,1,{
                        field: 'status', title: '状态', width: 280, templet: function(d) {
                            let s = '状态：';
                            s += (d.status == 0) ? '下架' : '上架'
                            s +='<br>';
                            s += '开始时间：' + d.start_time + '<br>';
                            s += '结束时间：' + d.end_time + '<br>';
                            return s;
                        }
                    });
                    bCols.splice(6, 1);
                }

                // 显示类别列表
                let productTable = table.render({
                    elem: '#product-list'
                    , url: '/admin/shop_product/index/'+shopId+'?m=fetch' //数据接口
                    , page: true //开启分页
                    , toolbar: true
                    , cols: [bCols]
                });


                // 监听操作事件
                table.on('tool(product-list)', function (obj) {
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                    var tr = obj.tr; //获得当前行 tr 的DOM对象

                    if (layEvent === 'edit') {
                        showProductForm({
                            showUrl: '/admin/shop_product/edit/'+data.id+'?shopType='+shopType,
                        });
                    }
                    if (layEvent === 'add_variant') {
                        showProductVariantForm(data.id, shopType);
                    }
                    if (layEvent === 'edit_variant') {
                        showProductVariantEditForm(data.id, shopType);
                    }
                });

                // 点击添加按钮，添加新产品
                $('#btn-add').on('click', function (e) {
                    showProductForm({
                        showUrl: '/admin/shop_product/new/?shopType='+shopType+'&shopId='+shopId,
                    });
                });

                let showProductForm = function showProductForm(conf) {
                    // let url = '/admin/shop_product/new/';

                    // 显示编辑表单
                    layer.open({
                        type: 2,
                        content: conf.showUrl,
                        area: ['900px', '700px'],
                        title: '产品信息',
                        cancel: function (index, layero) {
                            if (confirm('确定要关闭么')) {
                                layer.close(index)
                            }
                            return false;
                        },
                        end: function () {
                            productTable.reload();
                        }
                    });
                };

                let showProductVariantForm = function showProductVariantForm(productId) {
                    layer.open({
                        type: 2,
                        content: '/admin/shop_product_variant/new/?productId='+productId,
                        title: '新建产品型号',
                        area: ['900px', '700px'],
                        cancel: function (index, layero) {
                            if (confirm('确定要关闭么')) {
                                layer.close(index)
                            }
                            return false;
                        },
                        end: function () {
                            productTable.reload();
                        }
                    })
                };

                let showProductVariantEditForm = function showProductVariantForm(productId) {
                    layer.open({
                        type: 2,
                        content: '/admin/shop_product_variant/edit/?product_id='+productId,
                        title: '编辑产品型号',
                        area: ['900px', '700px'],
                        cancel: function (index, layero) {
                            if (confirm('确定要关闭么')) {
                                layer.close(index)
                            }
                            return false;
                        },
                        end: function () {
                            productTable.reload();
                        }
                    })
                };

                // 刷新
                $('.content__apps').on('click', '#btn-refresh', e => {
                    $('.filter-form-input').val('')
                    productTable.reload({
                        url: '/admin/shop_product/index/'+shopId+'?m=fetch'
                    })
                })

                // 搜索
                $('.content__apps').on('click', '#btn-search', e => {
                    let url = '/admin/shop_product/index/'+shopId+'?m=fetch'
                    let filterTitle = $('#filterTitle').val()
                    url += filterTitle ? '&filterTitle='+filterTitle : ''
                    let filterStatus = $('#filterStatus').val()
                    url += filterStatus ? '&filterStatus='+filterStatus : ''

                    productTable.reload({
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
