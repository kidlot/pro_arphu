<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<title>菜单管理 - bjyadmin</title>
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
		 <a href="#home" data-toggle="tab">菜单列表</a>
   </li>
   <li>
		<a href="javascript:;" onclick="add()">添加菜单</a>
	</li>
</ul>
<form action="<?php echo U('Nav/order');?>" method="post">
	<div id="myTabContent" class="tab-content">
	   <div class="tab-pane fade in active" id="home">
			<table class="table table-striped table-bordered table-hover table-condensed">
				<tr>
					<th width="5%">排序</th>
					<th>菜单名</th>
					<th>连接</th>
					<th>操作</th>
				</tr>
				<?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
						<td>
							<input class="form-control" style="width:40px;height:25px;" type="text" name="<?php echo ($v['id']); ?>" value="<?php echo ($v['order_number']); ?>">
						</td>
						<td><?php echo ($v['_name']); ?></td>
						<td><?php echo ($v['mca']); ?></td>
						<td>
							<a href="javascript:;" navId="<?php echo ($v['id']); ?>" navName="<?php echo ($v['name']); ?>"  onclick="add_child(this)">添加子菜单</a> |
							<a href="javascript:;" navId="<?php echo ($v['id']); ?>" navName="<?php echo ($v['name']); ?>" navMca="<?php echo ($v['mca']); ?>" navIco="<?php echo ($v['ico']); ?>" onclick="edit(this)">修改</a> |
							<a href="javascript:if(confirm('确定删除？'))location='<?php echo U('Nav/delete',array('id'=>$v['id']));?>'">删除</a>
						</td>
					</tr><?php endforeach; endif; ?>
			</table>
            <input class="btn btn-success" type="submit" value="排序">
	   </div>
	</div>
</form>
<!-- 添加菜单模态框开始 -->
<div class="modal fade" id="bjy-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		 <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">
					添加菜单
				</h4>
			</div>
			<div class="modal-body">
				<form id="bjy-form" class="form-inline" action="<?php echo U('Nav/add');?>" method="post">
					<input type="hidden" name="pid" value="0">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<tr>
							<th width="12%">菜单名：</th>
							<td>
								<input class="form-control" type="text" name="name">
							</td>
						</tr>
						<tr>
							<th>连接：</th>
							<td>
								<input class="form-control" type="text" name="mca"> 输入模块/控制器/方法即可 例如 Nav/index
							</td>
						</tr>
						<tr>
							<th>图标：</th>
							<td>
								<input class="form-control" type="text" name="ico">
								font-awesome图标 输入fa fa- 后边的即可
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

<!-- 修改菜单模态框开始 -->
<div class="modal fade" id="bjy-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		 <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">
					修改菜单
				</h4>
			</div>
			<div class="modal-body">
				<form id="bjy-form" class="form-inline" action="<?php echo U('Nav/edit');?>" method="post">
					<input type="hidden" name="id">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<tr>
							<th width="12%">菜单名：</th>
							<td>
								<input class="form-control" type="text" name="name">
							</td>
						</tr>
						<tr>
							<th>连接：</th>
							<td>
								<input class="form-control" type="text" name="mca"> 输入模块/控制器/方法即可 例如 Nav/index
							</td>
						</tr>
						<tr>
							<th>图标：</th>
							<td>
								<input class="form-control" type="text" name="ico">
								font-awesome图标 输入fa fa- 后边的即可
							</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<input class="btn btn-success" type="submit" value="修改">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- 修改菜单模态框结束 -->
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
<script>
// 添加菜单
function add(){
	$("input[name='name'],input[name='mca']").val('');
	$("input[name='pid']").val(0);
	$('#bjy-add').modal('show');
}

// 添加子菜单
function add_child(obj){
	var navId=$(obj).attr('navId');
	$("input[name='pid']").val(navId);
	$("input[name='name']").val('');
	$("input[name='mca']").val('');
	$('#bjy-add').modal('show');
}

// 修改菜单
function edit(obj){
	var navId=$(obj).attr('navId');
	var navName=$(obj).attr('navName');
	var navMca=$(obj).attr('navMca');
	var navIco=$(obj).attr('navIco');
	$("input[name='id']").val(navId);
	$("input[name='name']").val(navName);
	$("input[name='mca']").val(navMca);
	$("input[name='ico']").val(navIco);
	$('#bjy-edit').modal('show');
}

</script>
</body>
</html>