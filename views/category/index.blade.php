@extends('layouts.admin.admin')

@section('content')
<div class="content__apps">
    <input type="hidden" value="{{$shopId}}" id="shopId" />
    <nav class="nav__top">
        <div class="nav-item nav-item__new">
            <button type="button" class="layui-btn"><i class="layui-icon layui-icon-add-1"></i>添加</button>
        </div>
    </nav>
    <div id="category-list" lay-filter="category-list"></div>
</div>

{{-- 类别列表toolbar 的模板 --}}
<script type="text/html" id="actionTempl">
    <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
</script>
@endsection

@push('body-scripts')
    <script src="{{asset('/js/admin/yac/category.js?v=').time()}}"></script>
@endpush
