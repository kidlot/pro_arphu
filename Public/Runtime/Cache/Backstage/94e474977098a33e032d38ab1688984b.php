<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<title>商品管理 - bjyadmin</title>
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
<link rel="stylesheet" href="/Public/Default/aceadmin/kindeditor/plugins/code/prettify.css" />
<script charset="utf-8" src="/Public/Default/aceadmin/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="/Public/Default/aceadmin/kindeditor/lang/zh_CN.js"></script>
<script charset="utf-8" src="/Public/Default/aceadmin/kindeditor/plugins/code/prettify.js"></script>
<script>
    var editor1=null;
    var editor2=null;
    var editor3=null;
    KindEditor.ready(function(K) {
         editor1 = K.create('textarea[name="content1"]', {
            cssPath : '/Public/Default/aceadmin/kindeditor/plugins/code/prettify.css',
            uploadJson : '/Public/Default/aceadmin/kindeditor/php/upload_json.php',
            fileManagerJson : '/Public/Default/aceadmin/kindeditor/php/file_manager_json.php',
            allowFileManager : true,
            filterMode : false,
            afterCreate : function() {
                var self = this;
                K.ctrl(document, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
                K.ctrl(self.edit.doc, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
            }
        });
         editor2 = K.create('textarea[name="content2"]', {
             cssPath : '/Public/Default/aceadmin/kindeditor/plugins/code/prettify.css',
             uploadJson : '/Public/Default/aceadmin/kindeditor/php/upload_json.php',
             fileManagerJson : '/Public/Default/aceadmin/kindeditor/php/file_manager_json.php',
            allowFileManager : true,
            filterMode : false,
            afterCreate : function() {
                var self = this;
                K.ctrl(document, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
                K.ctrl(self.edit.doc, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
            }
        });
         editor3 = K.create('textarea[name="content3"]', {
             cssPath : '/Public/Default/aceadmin/kindeditor/plugins/code/prettify.css',
             uploadJson : '/Public/Default/aceadmin/kindeditor/php/upload_json.php',
             fileManagerJson : '/Public/Default/aceadmin/kindeditor/php/file_manager_json.php',
            allowFileManager : true,
            filterMode : false,
            afterCreate : function() {
                var self = this;
                K.ctrl(document, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
                K.ctrl(self.edit.doc, 13, function() {
                    self.sync();
                    K('form[name=example]')[0].submit();
                });
            }
        });
        prettyPrint();
    });
</script>
<ul id="myTab" class="nav nav-tabs">
   <li class="active">
		 <a href="#home" data-toggle="tab">商品列表</a>
   </li>
   <li>
		<a href="javascript:;" onclick="add()">添加商品</a>
	</li>
</ul>
<div id="myTabContent" class="tab-content">
   <div class="tab-pane fade in active" id="product">
		<table class="table table-striped table-bordered table-hover table-condensed">
			<tr>
				<th>商品名</th>
				<th>商品链接</th>
				<th>操作</th>
			</tr>
			<?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
					<td><?php echo ($v['product_name']); ?></td>
					<td><?php echo ($v['url']); ?></td>
					<td>
						<textarea style="display: none" name="general_details"><?php echo ($v['general_details']); ?></textarea>
						<textarea style="display: none" name="technical_info"><?php echo ($v['technical_info']); ?></textarea>
						<textarea style="display: none" name="product_range"><?php echo ($v['product_range']); ?></textarea>
						<a href="javascript:;"  onclick="edit(this)">修改</a> |
						<a href="javascript:if(confirm('确定删除？'))location='<?php echo U('AuthRule/delete',array('id'=>$v['id']));?>'">删除</a>
					</td>
				</tr><?php endforeach; endif; ?>
		</table>
   </div>
</div>
<!-- 添加商品模态框开始 -->
<div class="modal fade" id="bjy-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		 <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">
					添加商品
				</h4>
			</div>
			<div class="modal-body">
				<form id="bjy-form" class="form-inline" action="<?php echo U('AuthRule/add');?>" method="post">
					<input type="hidden" name="pid" value="0">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<tr>
							<th width="12%">商品名：</th>
							<td>
								<input class="form-control" type="text" name="title">
							</td>
						</tr>
						<tr>
							<th>商品：</th>
							<td>
								<input class="form-control" type="text" name="name"> 输入模块/控制器/方法即可 例如 Rule/index
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
	<div class="modal-dialog" style="width:900px">
		 <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">
					修改商品
				</h4>
			</div>
			<div class="modal-body" >
				<form id="bjy-form" class="form-inline" action="<?php echo U('Product/edit');?>" method="post">
					<input type="hidden" name="id">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<tr>
							<th width="12%">商品名：</th>
							<td>
								<input class="form-control" type="text" name="title">
							</td>
						</tr>
						<tr>
							<th>商品：</th>
							<td>
								<input class="form-control" type="text" name="name"> 输入模块/控制器/方法即可 例如 Rule/index
							</td>
						</tr>
						<tr>

							<th>general_details:</th>
							<td>
								<textarea name="content1" style="width:700px;height:200px;visibility:hidden;"></textarea>
							</td>

						</tr>
						<tr>

							<th>technical_info:</th>
							<td>
								<textarea name="content2" style="width:700px;height:200px;visibility:hidden;"></textarea>
							</td>

						</tr>
						<tr>

							<th>product_range:</th>
							<td>
								<textarea name="content3" style="width:700px;height:200px;visibility:hidden;"></textarea>
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
	$("input[name='title'],input[name='name']").val('');
	$("input[name='pid']").val(0);
	$('#bjy-add').modal('show');
}

// 添加子菜单
function add_child(obj){
	var ruleId=$(obj).attr('ruleId');
	$("input[name='pid']").val(ruleId);
	$("input[name='title']").val('');
	$("input[name='name']").val('');
	$('#bjy-add').modal('show');
}

// 修改菜单
function edit(obj){
	var general_details=$(obj).siblings("textarea[name='general_details']").val();
    var technical_info=$(obj).siblings("textarea[name='technical_info']").val();
    var product_range=$(obj).siblings("textarea[name='product_range']").val();
    editor1.html(general_details);
    editor2.html(technical_info);
    editor3.html(product_range);
	$('#bjy-edit').modal('show');
}
</script>
</body>
</html>