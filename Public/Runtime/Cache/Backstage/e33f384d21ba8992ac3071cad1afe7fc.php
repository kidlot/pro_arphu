<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="UTF-8"/>
        <title>管理员操作日志</title>
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/ace.css"/>
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
<br />
<form class="form-inline" method="GET" id="form" action="<?php echo U('AdminLog/index');?>" >
    <div class="form-group">
        <label >管理员账号</label>
        <input class="input-sm form-control" name="name" id="name" value="<?php echo ($search["name"]); ?>">
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <label >ip</label>
        <input class="input-sm form-control" name="ip" id="ip"  value="<?php echo ($search["ip"]); ?>">
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <button type="submit" id="query" class="btn btn-purple btn-sm">
            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
            搜索
        </button>
    </div>
</form>
<br />
<table class="table table-striped table-bordered table-hover table-condensed">
    <tr>
        <th width="20%">管理员账号</th>
        <th width="20%">操作时间</th>
        <th width="20%">ip</th>
        <th width="35%">记录</th>
    </tr>
    <?php if(empty($data)): ?><tr><td colspan="4" >没有记录</td></tr>
        <?php else: ?>
        <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                <td><?php echo ($v['account']); ?></td>
                <td><?php echo ($v['time']); ?></td>
                <td><?php echo ($v['ip']); ?></td>
                <td><?php echo ($v['log']); ?></td>
            </tr><?php endforeach; endif; endif; ?>
</table>
<?php echo ($page); ?>
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