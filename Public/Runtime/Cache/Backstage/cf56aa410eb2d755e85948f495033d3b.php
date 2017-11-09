<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en" style="overflow-y: auto;">
    <head>
        <meta charset="utf-8" />
        <title><?php echo (C("WEBSITE_TITLE")); ?> - 企业综合管理后台系统</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/bootstrap.css" />
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/font-awesome.css" />

        <!-- text fonts -->
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/ace-fonts.css" />

        <!-- ace styles -->
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
        <link rel="stylesheet" href="/Public/Default/aceadmin/css/ace.min.css" />
        <script src="/Public/Default/aceadmin/js/ace-extra.js"></script>

        <script type="text/javascript">
            window.jQuery || document.write("<script src='/Public/Default/aceadmin/js/jquery.js'>" + "<" + "/script>");
        </script>

        <script type="text/javascript">
            if ('ontouchstart' in document.documentElement)
                document.write("<script src='/Public/Default/aceadmin/js/jquery.mobile.custom.js'>" + "<" + "/script>");
        </script>
        <script src="/Public/Default/aceadmin/js/bootstrap.js"></script>

        <script src="/Public/Default/aceadmin/myjs/js/bootbox.js"></script>

        <!-- ace scripts -->
        <script src="/Public/Default/aceadmin/js/ace/elements.scroller.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.colorpicker.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.fileinput.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.typeahead.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.wysiwyg.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.spinner.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.treeview.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.wizard.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/elements.aside.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.ajax-content.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.touch-drag.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.sidebar.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.sidebar-scroll-1.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.submenu-hover.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.widget-box.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.settings.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.settings-rtl.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.settings-skin.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.widget-on-reload.js"></script>
        <script src="/Public/Default/aceadmin/js/ace/ace.searchbox-autocomplete.js"></script>
		<script src="/Public/Default/aceadmin/js/particles.js"></script>
    </head>

    <body <?php if(empty($user)): ?>class="login-layout"<?php else: ?>class="no-skin"<?php endif; ?> style="overflow-y: hidden;">
	<div id="particles-js" style="position: absolute; width: 100%;height: 100%;"></div>
    <?php if(!empty($user)): ?><div class="navbar navbar-default" id="navbar">
            <script type="text/javascript">
            try {
                ace.settings.check('navbar', 'fixed')
            } catch (e) {
            }
            </script>
            <div class="navbar-container" id="navbar-container">
                <!-- 没做兼容 -->
                <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                    <span class="sr-only">Toggle sidebar</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                </button>
                <div class="navbar-header pull-left">
                    <a href="<?php echo U('Index/index');?>" class="navbar-brand">
                        <small>
                            <i class="fa fa-leaf"></i>
                            <?php echo (C("WEBSITE_TITLE")); ?> - 企业综合管理后台系统
                        </small>
                    </a><!-- /.brand -->
                </div>
                <div class="navbar-header pull-right" role="navigation">
                    <ul class="nav ace-nav">
                        <li class="light-blue">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                <img class="nav-user-photo" src="/Public/Default/aceadmin/avatars/user.jpg" alt="Jason's Photo" />
                                <span class="user-info">
                                    <small>欢迎光临,</small>
                                    <?php echo ($user["username"]); ?>
                                </span>
                                <i class="icon-caret-down"></i>
                            </a>

                            <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:;" navId="" navName=""  onclick="mod_pw()">
                                        <i class="icon-off"></i>
                                        修改密码
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Login/logout');?>">
                                        <i class="icon-off"></i>
                                        退出
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div><?php endif; ?>
    <div class="main-container" id="main-container">
        <?php if(empty($user)): else: ?> 
            <script type="text/javascript">
                try {
                    ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
            </script>

            <!--<div class="main-container-inner">-->
            <div id="sidebar" class="sidebar responsive">
	<script type="text/javascript">
		try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
	</script>

	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<button class="btn btn-success">
				<i class="ace-icon fa fa-signal"></i>
			</button>

			<button class="btn btn-info">
				<i class="ace-icon fa fa-pencil"></i>
			</button>

			<!-- #section:basics/sidebar.layout.shortcuts -->
			<button class="btn btn-warning">
				<i class="ace-icon fa fa-users"></i>
			</button> 

			<button class="btn btn-danger">
				<i class="ace-icon fa fa-cogs"></i>
			</button>

			<!-- /section:basics/sidebar.layout.shortcuts -->
		</div>

		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span>

			<span class="btn btn-info"></span>

			<span class="btn btn-warning"></span>

			<span class="btn btn-danger"></span>
		</div>
	</div><!-- /.sidebar-shortcuts -->

	<ul class="nav nav-list">
		<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(empty($v['_data'])): ?><li class="b-nav-li">
					<a href="<?php echo U($v['mca']);?>" target="right_content">
						<i class="<?php echo ($v['ico']); ?>"></i>
						<span class="menu-text"> <?php echo ($v['name']); ?> </span>
					</a>
				</li>
			<?php else: ?>
				<li class="b-has-child">
					<a href="#" class="dropdown-toggle b-nav-parent" target="right_content">
						<i class="<?php echo ($v['ico']); ?>"></i>
						<span class="menu-text"> <?php echo ($v['name']); ?> </span>

						<b class="arrow icon-angle-down"></b>
					</a>
					<b class="arrow"></b>
					<ul class="submenu">
						<?php if(is_array($v['_data'])): $i = 0; $__LIST__ = $v['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$n): $mod = ($i % 2 );++$i;?><li class="b-nav-li">
								<a href="<?php echo U($n['mca']);?>" target="right_content">
									<?php if(empty($n['ico'])): ?><i class="menu-icon fa fa-caret-right"></i>
									<?php else: ?> 
										<i class="<?php echo ($n['ico']); ?>"></i><?php endif; ?> 
									<?php echo ($n['name']); ?>
								</a>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	<!-- #section:basics/sidebar.layout.minimize -->
	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
	</div>

	<!-- /section:basics/sidebar.layout.minimize -->
	<script type="text/javascript">
		try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
	</script>
</div>

            <div class="main-content">
                <div class="page-content">
                    <iframe id="content-iframe" src="<?php echo U('Index/welcome');?>" frameborder="0" width="100%" height="100%" name="right_content"  scrolling="auto"></iframe>
                </div><!-- /.page-content -->
            </div><!-- /.main-content -->
            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="icon-double-angle-up icon-only bigger-110"></i>
            </a>
            <!--		</div> /.main-container-inner --><?php endif; ?> 
        <!--         <div class="footer">
                <div class="footer-inner">
                    <div class="footer-content">
                        <span class="bigger-120">
                            <span class="blue bolder">HLA</span> &copy; 2016-2018
                        </span>
                    </div>
                </div>
            </div>-->
    </div>
    <!-- 修改密码模态框开始 -->
    <div class="modal fade" id="mod-pw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:400px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        修改密码
                    </h4>
                </div>
                <div class="space-6"></div>
                <div class="modal-body">
                    <form id="pw_form" name="pw_form" action="<?php echo U('Index/modPw');?>" method="post">
                        <fieldset>
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <input type="password" name="old_pw" class="form-control" placeholder="原密码"  title="原密码不能为空"
                                           required/>
                                    <i class="ace-icon fa fa-lock"></i>
                                </span>
                            </label>

                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <input type="password" name="new_pw" class="form-control" placeholder="新密码"  title="新密码不能为空"
                                           required/>
                                    <i class="ace-icon fa fa-lock"></i>
                                </span>
                            </label>

                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <input type="password" name="cfm_pw" class="form-control" placeholder="确认密码"  title="两次密码必须一致"
                                           required oninvalid="this.setCustomValidity('两次密码不一致');" oninput="this.setCustomValidity('');"/>
                                    <i class="ace-icon fa fa-lock"></i>
                                </span>
                            </label>

                            <div class="space"></div>
                            <button type="submit" class="width-35 pull-right btn btn-sm btn-primary" onclick=" return check_pw();">
                                <i class="ace-icon fa fa-key"></i>
                                <span class="bigger-110">提交</span>
                            </button>
                            <div class="space-4"></div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- 修改密码模态框结束 -->
    <script type="text/javascript">

        $(function () {
            // 导航点击事件
            $('.b-nav-li').click(function (event) {
                $('.active').removeClass('active');
                var ulObj = $(this).parents('.b-has-child').eq(0);
                $(this).addClass('active');
                // alert(2);
                if (ulObj.length != 0) {
                    $(this).parents('.b-has-child').eq(0).addClass('active');
                }
            });
            //计算iframe高度
            $("body").height($(window).height());
            $("#content-iframe").height($(window).height() - $("#navbar").height());
			//动态效果的js部分#######################################################
			particlesJS('particles-js', {
			  particles: {
				color: '#0ff',
				shape: 'circle', // "circle", "edge" or "triangle"
				opacity: 1,
				size: 4,
				size_random: true,
				nb: 150,
				line_linked: {
				  enable_auto: true,
				  distance: 100,
				  color: '#ff0',
				  opacity: 0.8,
				  width: 1,
				  condensed_mode: {
					enable: false,
					rotateX: 600,
					rotateY: 600
				  }
				},
				anim: {
				  enable: true,
				  speed: 1
				}
			  },
			  interactivity: {
				enable: true,
				mouse: {
				  distance: 300
				},
				detect_on: 'canvas', // "canvas" or "window"
				mode: 'grab',
				line_linked: {
				  opacity: .5
				},
				events: {
				  onclick: {
					enable: true,
					mode: 'push', // "push" or "remove"
					nb: 4
				  }
				}
			  },
			  /* Retina Display Support */
			  retina_detect: true
			});
		//动态效果的js部分#######################################################
        });
        //当浏览器窗口大小改变时，重新计算iframe的高度  
        window.onresize = function () {
            $("body").height($(window).height());
            $("#content-iframe").height($(window).height() - $("#navbar").height());
        };

        //修改密码
        function mod_pw() {
            $("input[name='old_pw']").val('');
            $("input[name='new_pw']").val('');
            $("input[name='cfm_pw']").val('');
            $('#mod-pw').modal('show');
        }

        function check_pw() {
            if ($("input[name='cfm_pw']").val() !== $("input[name='new_pw']").val()) {
                $("input[name='new_pw']").val('');
                $("input[name='cfm_pw']").val('');
            }
        }
    </script>
</body>
</html>