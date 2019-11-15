@extends('layouts.admin.layer')

@section('content')
<div class="content__pg--app-new">
   {{-- 会费信息表单 --}}
   <form class="layui-form form__app" action="javascript:;" id="product-form">
       <input type="hidden" value="{{isset($shopType) ? $shopType : ''}}" name="shop_type">
    <input type="hidden" value="{{isset($product) ? $product->id : ''}}" name="id">
    <input type="hidden" value="{{isset($product) ? $product->categoryId : ''}}" name="category_id">

    <div class="layui-form-item" id="title">
        <label class="layui-form-label">名称</label>
        <div class="layui-input-block">
            <input type="text" name="title" required  value="{{isset($product) ? $product->title : ''}}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">列表图片</label>
            <div class="layui-input-inline">
                <input type="hidden" name="cover_pic" id="coverPicInputimgurl" placeholder="图片地址" value="{{isset($product) ? $product->coverPic : ''}}" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <div class="layui-upload-list" style="margin:0">
                    <img src="{{isset($product) ? $product->coverPic : ''}}" id="coverPicSrcimgurl" class="layui-upload-img">
                </div>
            </div>
            <div class="layui-input-inline layui-btn-container" style="width: auto;">
                <button class="layui-btn layui-btn-primary" class=".pictures" id="cover_pic">上传图片</button >
            </div>
            <div class="layui-form-mid layui-word-aux">头像的尺寸限定320x200px,大小在300kb以内</div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">详情图片</label>
            <div class="layui-input-inline">
                <input type="hidden" name="pictures" id="picturesInputimgurl" placeholder="图片地址" value="{{isset($product) ? $product->pictures : ''}}" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <div class="layui-upload-list" style="margin:0">
                    <img src="{{isset($product) ? $product->pictures : ''}}" id="picturesSrcimgurl" class="layui-upload-img">
                </div>
            </div>
            <div class="layui-input-inline layui-btn-container" style="width: auto;">
                <button class="layui-btn layui-btn-primary" class="pictures" id="pictures">上传图片</button >
            </div>
            <div class="layui-form-mid layui-word-aux">头像的尺寸限定640x320px,大小在400kb以内</div>
    </div>


    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">详情</label>
        <div class="layui-input-block">
            <textarea name="description" placeholder="请输入内容" class="layui-textarea" id="description">{!! isset($product) ? $product->description : '' !!}</textarea>
        </div>
    </div>

    <div class="layui-form-item" id="price">
        <label class="layui-form-label">实际价格</label>
        <div class="layui-input-block">
            <input type="text" name="price" required  value="{{isset($product) ? (($shopType != 'bp_shop') ? $product->price / 100 : $product->price) : ''}}" lay-verify="required" placeholder="请输入价格" autocomplete="off" class="layui-input">
        </div>
    </div>

    @if ($shopType != 'seckill')
        <div class="layui-form-item" id="show_price">
            <label class="layui-form-label">显示价格</label>
            <div class="layui-input-block">
                <input type="text" name="show_price" required  value="{{isset($product) ? (($shopType != 'bp_shop') ? $product->showPrice / 100 : $product->showPrice) : ''}}" lay-verify="required" placeholder="请输入显示价格" autocomplete="off" class="layui-input">
            </div>
        </div>
    @endif

    <div class="layui-form-item" id="price">
        <label class="layui-form-label">参数</label>
        <div class="layui-input-block">
            <input type="text" name="parameters"  value="{{isset($product) ? (count($product->getParameters()) > 0 ? implode('，', $product->getParameters()) : '' ) : ''}}" placeholder="请输入参数，以逗号隔开（长:230cm,宽200cm,颜色：红色）" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item" id="inventory">
        <label class="layui-form-label">库存</label>
        <div class="layui-input-block">
            <input type="text" name="inventory" required  value="{{isset($product) ? $product->inventory : ''}}" lay-verify="required" placeholder="请输入库存" autocomplete="off" class="layui-input">
        </div>
    </div>


    <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="1" title="上架" {{isset($product) && $product->status == '1' ? 'checked' : ''}}>
            <input type="radio" name="status" value="0" title="下架" {{isset($product) && $product->status == '0' ? 'checked' : ''}}>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">种类</label>
        {{-- <a href="" id="newTag" class="layui-btn layui-btn-sm">添加标签</a> --}}
        <div class="layui-input-block">
            <select name="category_id">
            @foreach ($categories as $category)
                <option type="checkbox" value="{{$category['id']}}" {{(isset($product) && ($product->categoryId == $category['id'])) ? 'selected' : ''}}>{{$category['title']}}</option>
            @endforeach
            </select>
        </div>
    </div>

    @if ($shopType == 'seckill')
        <div class="layui-form-item" id="sk_price">
            <label class="layui-form-label">秒杀价格</label>
            <div class="layui-input-block">
                <input type="text" name="sk_price" required  value="{{isset($product) ? $product->price / 100 : ''}}" lay-verify="required" placeholder="请输入秒杀价格" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">活动开始时间</label>
            <div class="layui-input-block">
                <input type="text" name="start_time" id="start_time" value="{{isset($product) ? $product->startTime : ''}}" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">活动结束时间</label>
            <div class="layui-input-block">
                <input type="text" name="end_time" id="end_time" value="{{isset($product) ? $product->endTime : ''}}" class="layui-input">
            </div>
        </div>
    @endif
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
                    url: '/admin/shop_product/save',
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

              //执行实例
            var coverPic = upload.render({
                elem: '#cover_pic' //绑定元素
                ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content')
                ,data: {
                    name: 'file'
                }
                ,done: function(res){
                    console.log('>>>', res);
                    if (res.uploaded == 0) {
                        alert(res.error.message);
                    } else {
                        let url = res.url;
                        $("#coverPicInputimgurl").val(url);
                        $("#coverPicSrcimgurl").attr('src',url);
                    }
                }
                ,error: function(){
                    alert('网络错误!');
                }
            });

            var listPic = upload.render({
                elem: '#pictures' //绑定元素
                ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content')
                ,data: {
                    name: 'file'
                }
                ,done: function(res){
                    console.log('>>>', res);
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

                //创建一个图片上传（包括裁切）组件
            // croppers.render({
            //     elem: '#cover_pic'
            //     ,saveW:320     //保存宽度
            //     ,saveH:200
            //     ,mark:2/1    //选取比例
            //     ,area:'700px'  //弹窗宽度
            //     ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content') //图片上传接口返回和（layui 的upload 模块）返回的JOSN一样
            //     ,done: function(url){ //上传完毕回调
            //         $("#coverPicInputimgurl").val(url);
            //         $("#coverPicSrcimgurl").attr('src',url);
            //     }
            // })

            //创建一个图片上传（包括裁切）组件
            // croppers.render({
            //     elem: '#pictures'
            //     ,saveW:640     //保存宽度
            //     ,saveH:320
            //     ,mark:2/1    //选取比例
            //     ,area:'700px'  //弹窗宽度
            //     ,url: "/file/upload?_token="+$('meta[name="csrf-token"]').attr('content') //图片上传接口返回和（layui 的upload 模块）返回的JOSN一样
            //     ,done: function(url){ //上传完毕回调
            //         $("#picturesInputimgurl").val(url);
            //         $("#picturesSrcimgurl").attr('src',url);
            //     }
            // });

            let getTimeVal = function (el) {
                let time = $(el).val();
                if (time.length < 1) time = new Date();
                return time;
            }

            if ($('#start_time').length > 0) {
                laydate.render({
                    elem: '#start_time'
                    ,type: 'datetime'
                    ,value: getTimeVal('#start_time')
                });
            }

            if ($('#end_time').length > 0) {
                laydate.render({
                    elem: '#end_time'
                    ,type: 'datetime'
                    ,value: getTimeVal('#end_time')
                });
            }
        });
    </script>
@endpush
