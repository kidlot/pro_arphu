<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>地区管理</title>
        <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/Public/Default/statics/bootstrap-3.3.5/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/Public/Default/statics/css/base.css" />
    <link rel="stylesheet" href="/Public/Default/statics/bootstrap-3.3.5/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="/Public/Default/statics/font-awesome-4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="/Public/Default/statics/iCheck-1.0.2/skins/all.css">
</head>
<body>
    <!-- #section:basics/面包屑导航 -->

<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb" style="margin-bottom: 2px;font-size: 12px;">
		<li>
			<i class="fa fa-home" style="font-size: 20px;margin-left: 2px;margin-right: 2px;"></i>
			首页
		</li>
		<?php if($breadcrumbs['title3'] != ''): ?><li><?php echo ($breadcrumbs['title3']); ?></li><?php endif; ?>
		<?php if($breadcrumbs['title2'] != ''): ?><li><?php echo ($breadcrumbs['title2']); ?></li><?php endif; ?>
		<?php if($breadcrumbs['title1'] != ''): ?><li><?php echo ($breadcrumbs['title1']); ?></li><?php endif; ?>
	</ul>

</div>
<!-- /section:basics/面包屑导航 -->
<table class="table table-striped table-bordered table-hover table-condensed">
    <tr>
        <th style="width:15%;" >省</th>
        <td  style="width:30%;" >
            <select class="input-sm form-control" name="province" id="province" onchange="change_region(0);">
                <option value="">请选择</option>
                <?php if(!empty($province)): if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            </select>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <th style="width:15%;" >市</th>
        <td>
            <select class="input-sm form-control" name="city" id="city" onchange="change_region(1);">
                <option value="">请选择</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-primary" onclick="add_region(1);">添加市</button>
        </td>
    </tr>
    <tr>
        <th style="width:15%;" >县</th>
        <td>
            <select class="input-sm form-control" name="county" id="country">
                <option value="">请选择</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-primary" onclick="add_region(2);">添加县</button>
        </td>
    </tr>
</table>

<!-- 添加菜单模态框开始 -->
<div class="modal fade" id="region-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加地区
                </h4>
            </div>
            <div class="modal-body">
                <form id="bjy-form" class="form-inline" action="<?php echo U('Region/add');?>" method="post">
                    <input class="form-control" type="hidden" name="pid" id="pid" value="">
                    <table class="table table-striped table-bordered table-hover table-condensed">
                        <tr>
                            <th width="15%">地区名：</th>
                            <td>
                                <input class="form-control" type="text" name="name">
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input class="btn btn-success" type="submit" value="添加">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 添加菜单模态框结束 -->
<script>
// 添加地区
    function add_region(type) {
        $("input[name='name']").val('');
        if (1 === type) {
            if( "" === $('#province').val()){
                alert("请选择上级省");
                return;
            }
            $('#myModalLabel').text("添加市");
            $('#pid').val($('#province').val());
        } else if (2 === type) {
            if( "" === $('#city').val()){
                alert("请选择上级市");
                return;
            }
            $('#myModalLabel').text("添加县");
            $('#pid').val($('#city').val());
        }
        $('#region-add').modal('show');
    }
</script>
<!-- 引入bootstrjs部分开始 -->
<script src="/Public/Default/statics/js/jquery-1.10.2.min.js"></script>
<script src="/Public/Default/statics/bootstrap-3.3.5/js/bootstrap.min.js"></script>
<script>
    var xbIsLogin=1,
        xbCheckLoginUrl='<?php echo U('Home/Public/ajax_check_login');?>',
        fwbCheckLoginUrl ="<?php echo U('User/Login/check_login');?>",
        fwbLoginOutUrl="<?php echo U('User/Login/ajax_logout');?>",
        rongUserInfoUrl="<?php echo U('Api/Rong/get_user_info');?>",
        rongKey="",
        rongToken="",
        xbUserInfo = {
            id: "88",
            name: "admin",
            avatar: ""
        };
</script>
<script src="/Public/Default/statics/emoji/js/config.js"></script>
<script src="/Public/Default/statics/emoji/js/emoji-picker.js"></script>
<script src="/Public/Default/statics/emoji/js/jquery.emojiarea.js"></script>
<script src="http://cdn.ronghub.com/RongIMLib-2.0.6.beta.min.js"></script>
<script src="http://cdn.ronghub.com/RongEmoji-2.0.2.beta.min.js"></script>
<script src="/Public/Default/statics/js/hla.common.js"></script>
<script src="/Public/Default/statics/iCheck-1.0.2/icheck.min.js"></script>
<script>
$(document).ready(function(){
    $('.xb-icheck').iCheck({
        checkboxClass: "icheckbox_minimal-blue",
        radioClass: "iradio_minimal-blue",
        increaseArea: "20%"
    });
});
</script>
</body>
</html>