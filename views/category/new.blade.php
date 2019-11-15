@extends('layouts.admin.layer')

@section('content')
<div class="content__pg--app-new">
   {{-- 会费信息表单 --}}
   <form class="layui-form form__app" action="" id="category-form">
    <input type="hidden" value="{{$category->id ?? ''}}" name="id">

    <div class="layui-form-item" id="title">
        <label class="layui-form-label">名称</label>
        <div class="layui-input-block">
            <input type="text" name="title" required  value="{{$category->title ?? ''}}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图片</label>
            <div class="layui-input-inline">
                <input type="hidden" name="pic" id="inputimgurl" placeholder="图片地址" value="{{isset($category) ? $category->pic : ''}}" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <div class="layui-upload-list" style="margin:0">
                    <img src="{{isset($category) ? $category->pic : ''}}" id="srcimgurl" class="layui-upload-img">
                </div>
            </div>
            <div class="layui-input-inline layui-btn-container" style="width: auto;">
                <button class="layui-btn layui-btn-primary" id="pic">上传图片</button >
            </div>
            <div class="layui-form-mid layui-word-aux">头像的尺寸限定640x320px,大小在400kb以内</div>
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
            let croppers = layui.croppers;

            form.on('submit(formDemo)', function(data){
                return false;
            });
                //创建一个头像上传（包括裁切）组件
            croppers.render({
                elem: '#pic'
                ,saveW:640     //保存宽度
                ,saveH:320
                ,mark:2/1    //选取比例
                ,area:'700px'  //弹窗宽度
                ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content') //图片上传接口返回和（layui 的upload 模块）返回的JOSN一样
                ,done: function(url){ //上传完毕回调
                    $("#inputimgurl").val(url);
                    $("#srcimgurl").attr('src',url);
                }
            });
        });
    </script>
@endpush
