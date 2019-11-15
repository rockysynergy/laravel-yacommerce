@extends('layouts.admin.admin')

@section('content')
<div class="content__apps">
    <input type="hidden" value="{{$shopId}}" id="shopId" />
    <input type="hidden" value="{{$shopType}}" id="shopType" />
    <nav class="nav__top">
        <div class="nav-item nav-item__new">
            <button type="button" class="layui-btn"><i class="layui-icon layui-icon-add-1"></i>添加</button>
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
    <script src="{{asset('/js/admin/yac/product.js?v=').time()}}"></script>
@endpush
