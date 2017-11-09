<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>用户组添加用户 - bjyadmin</title>
        <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/Public/Default/statics/bootstrap-3.3.5/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/Public/Default/statics/css/base.css" />
    <link rel="stylesheet" href="/Public/Default/statics/bootstrap-3.3.5/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="/Public/Default/statics/font-awesome-4.4.0/css/font-awesome.min.css" />
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
<ul id="myTab" class="nav nav-tabs">
   <li class="active">
         <a href="<?php echo U('AdminUser/index');?>">管理员列表</a>
   </li>
   <li>
        <a href="<?php echo U('AdminUser/add');?>">添加管理员</a>
    </li>
</ul>
<table class="table table-striped table-bordered table-hover table-condensed">
    <tr>
        <th width="10%">用户名</th>
        <th>用户组</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
            <td><?php echo ($v['username']); ?></td>
            <td><?php echo ($v['title']); ?></td>
            <td>
                <a href="<?php echo U('AdminUser/edit',array('id'=>$v['id']));?>">修改权限或密码</a> |
                <?php switch($v["status"]): case "1": ?><a href="javascript:if(confirm('确定禁止该用户登陆？'))location='<?php echo U('AdminUser/forbid',array('id'=>$v['id'],'status'=>0));?>'">禁止登陆</a><?php break;?>
                    <?php case "0": ?><a href="javascript:if(confirm('确定允许该用户登陆？'))location='<?php echo U('AdminUser/forbid',array('id'=>$v['id'],'status'=>1));?>'">允许登陆</a><?php break;?>
                    <?php default: ?>123<?php endswitch;?>
            </td>
        </tr><?php endforeach; endif; ?>
</table>
 
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
</body>
</html>