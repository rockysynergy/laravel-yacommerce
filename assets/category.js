$(document).ready(function () {
    let shopId = $('#shopId').val();

    layui.use(['table', 'layer'], function () {
        var table = layui.table;

        // 显示类别列表
        let categoryTable = table.render({
            elem: '#category-list'
            , url: '/admin/shop_category/index/'+shopId+'?m=fetch' //数据接口
            , page: true //开启分页
            , limit: 15
            , cols: [[ //表头
                { field: 'id', title: 'ID', width: 50, sort: true}
                , { field: 'title', title: '名称', width: 220 }
                , { field: 'pic', title: '图片', width: 160, templet: function(d) {
                    if (d.pic) return '<img src="'+d.pic+'" />';
                    else return '';
                }}
                /*
                , {
                    field: 'parent', title: '父类名称', width: 90, templet: function (d) {
                        if (d.parent_id == 0) return '无';
                        else return d.parent_title;
                    }
                }
                */
                , { field: '', title: '操作', width: 260, toolbar: '#actionTempl' }
            ]]
        });


        // 监听操作事件
        table.on('tool(category-list)', function (obj) {
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象

            if (layEvent === 'edit') {
                showCategoryForm({
                    showUrl: '/admin/shop_category/edit/'+data.id,
                    saveUrl: '/admin/shop_category/update'
                });
            }
        });

        // 点击添加按钮，添加新应用
        $('.nav-item__new').on('click', function (e) {
            showCategoryForm({
                showUrl: '/admin/shop_category/new/',
                saveUrl: '/admin/shop_category/save'
            });
        });

        let showCategoryForm = function showCategoryForm(conf) {
            // let url = '/admin/shop_category/new/';

            // 显示编辑表单
            layer.open({
                type: 2,
                content: conf.showUrl,
                area: ['900px', '700px'],
                title: '添加',
                btn: '添加',
                cancel: function (index, layero) {
                    if (confirm('确定要关闭么')) {
                        layer.close(index)
                    }
                    return false;
                },
                yes: function (index, layero) {
                    let ifname = "layui-layer-iframe" + index;//获得layer层的名字
                    let Ifame = window.frames[ifname]//得到框架
                    let tForm = eval(Ifame.document.getElementById("category-form"))
                    let formData = new FormData(tForm);
                    formData.append('shop_id', shopId);

                    $.ajax({
                        url: conf.saveUrl,
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
                                layer.close(index); //如果设定了yes回调，需进行手工关闭
                                //同步更新缓存对应的值
                                categoryTable.reload();
                            } else if (res.status == 1) {
                                showError(tForm, res.msg);
                            } else if (res.status == 2) {
                                alert(res.msg);
                            }
                        },
                    })
                },
                success: function () {
                }
            });
        }

    });

});
