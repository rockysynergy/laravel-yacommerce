@extends('layouts.admin.layer')

@section('content')
<div class="content__pg--app-new">
   {{-- 会费信息表单 --}}
   <form class="layui-form form__app" action="javascript:;" id="product-form">
        <input type="hidden" value="{{isset($variant) ? $variant->id : ''}}" name="id">
        <input type="hidden" value="{{$productId ?? $variant->getProductId()}}" name="product_id">

        <div class="layui-form-item" id="title">
            <label class="layui-form-label">型号</label>
            <div class="layui-input-block">
                <input type="text" name="model" required  value="{{isset($variant) ? $variant->model : ''}}" lay-verify="required" placeholder="请输入型号" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">图片</label>
                <div class="layui-input-inline">
                    <input type="hidden" name="pictures" id="picturesInputimgurl" placeholder="图片地址" value="{{isset($variant) ? $variant->pictures : ''}}" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <div class="layui-upload-list" style="margin:0">
                        <img src="{{isset($variant) ? $variant->pictures : ''}}" id="picturesSrcimgurl" class="layui-upload-img">
                    </div>
                </div>
                <div class="layui-input-inline layui-btn-container" style="width: auto;">
                    <button class="layui-btn layui-btn-primary" class="pictures" id="pictures">上传图片</button >
                </div>
                <div class="layui-form-mid layui-word-aux">头像的尺寸限定640x320px,大小在400kb以内</div>
        </div>


        <div class="layui-form-item" id="price">
            <label class="layui-form-label">实际价格</label>
            <div class="layui-input-block">
                <input type="text" name="price" required  value="{{isset($variant) ? $variant->price / 100 :  ''}}" lay-verify="required" placeholder="请输入价格" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="show_price">
            <label class="layui-form-label">显示价格</label>
            <div class="layui-input-block">
                <input type="text" name="show_price" required  value="{{isset($variant) ?  $variant->showPrice / 100 : ''}}" lay-verify="required" placeholder="请输入显示价格" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="price">
            <label class="layui-form-label">参数</label>
            <div class="layui-input-block">
                <input type="text" name="parameters"  value="{{isset($variant) ? (count($variant->getParameters()) > 0 ? implode('，', $variant->getParameters()) : '' ) : ''}}" placeholder="请输入参数，以逗号隔开（长:230cm,宽200cm,颜色：红色）" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="inventory">
            <label class="layui-form-label">库存</label>
            <div class="layui-input-block">
                <input type="text" name="inventory" required  value="{{isset($variant) ? $variant->inventory : ''}}" lay-verify="required" placeholder="请输入库存" autocomplete="off" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="上架" {{isset($variant) && $variant->status == '1' ? 'checked' : ''}}>
                <input type="radio" name="status" value="0" title="下架" {{isset($variant) && $variant->status == '0' ? 'checked' : ''}}>
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
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script>
        if (undefined !== window.CKEDITOR) {
            var options = {
                //   filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                filebrowserImageUploadUrl: '/file/upload?_token='+$('meta[name="csrf-token"]').attr('content'),
                //   filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                //   filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
                language: 'zh-cn',
                };

            if ($('#description').length > 0) {
                var editor = CKEDITOR.replace( 'description', options);
            }
        }


        layui.config({
             base: '/static/cropper/' //layui自定义layui组件目录
        }).use(['layer', 'form', 'croppers', 'laydate', 'laydate', 'upload'], function(){
            let layer = layui.layer;
            let form = layui.form;
            let croppers = layui.croppers;
            let laydate = layui.laydate;
            let upload = layui.upload;


            form.on('submit(productForm)', function(data){
                let tForm = document.querySelector('#product-form');
                let formData = new FormData(tForm);
                if ($('#description').length > 0) formData.append('description', editor.document.getBody().getHtml());

                $.ajax({
                    url: '/admin/shop_product_variant/save',
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

            // 图片上传
            var listPic = upload.render({
                elem: '#pictures' //绑定元素
                ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content')
                ,data: {
                    name: 'file'
                }
                ,done: function(res){
                    if (res.uploaded == 0) {
                        alert(res.error.message);
                    } else {
                        let url = res.url;
                        $("#picturesInputimgurl").val(url);
                        $("#picturesSrcimgurl").attr('src',url);
                    }
                }
                ,error: function(){
                    alert('网络错误!');
                }
            });

        });
    </script>
@endpush
