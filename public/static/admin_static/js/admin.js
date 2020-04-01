/^http(s*):\/\//.test(location.href) || alert('请先部署到 localhost 下再访问');

var objvTab = "";
layui.config({
    base: '/static/admin_static/module/'
}).define(["element", "layer",  "vTab", "vContextMenu", 'colorpicker', 'nprogress', 'vae'], function () {
	var $ = layui.jquery;
	var layer = layui.layer,colorpicker = layui.colorpicker,vae = layui.vae;

	// 加载主题
	var theme = layui.data('vaeAdmin_theme').color || ['#009688','#ffffff'];
	//左侧菜单选中时的主题颜色
	document.styleSheets[1].cssRules[0].style.background=theme[0];
	document.styleSheets[1].cssRules[0].style.color=theme[1];
	// tab主题颜色
	document.styleSheets[1].cssRules[1].style.color=theme[0];
	

	var vTab = layui.vTab({
		// 菜单请求路径
		url: "/index.php/admin/index/getAdminMenuList",
		// 允许同时选项卡的个数
		openTabNum: 30,
		// 如果返回的结果和navs.json中的数据结构一致可省略这个方法
		parseData: function (data) {
			return data.data;
		}
	});
	objvTab = vTab;
	/**
	 * 左侧导航渲染完成之后的操作
	 */
	vTab.render(function () {
		/**tab栏的鼠标右键事件**/
		$("body .v-tab").vContextMenu({
			width: 'auto',
			itemHeight: 30,
			menu: [
				{
					text: "定位所在页",
					icon: "v-icon v-icon-location",
					callback: function () {
						vTab.positionTab();
					}
				},
				{
					text: "关闭当前页",
					icon: "v-icon v-icon-roundclose",
					callback: function () {
						vTab.tabClose(1);
					}
				},
				{
					text: "关闭其他页",
					icon: "v-icon v-icon-roundclose",
					callback: function () {
						vTab.tabClose(2);
					}
				},
				{
					text: "关闭所有页",
					icon: "v-icon v-icon-roundclose",
					callback: function () {

						vTab.tabClose(3);
					}
				}
			]
		});
	});

	//关掉除当前外的TAB
	vTab.tabClose(2);

	//判断是否锁定了界面
	if(layui.data('vaeAdmin_lock').lock){
		layer.prompt({
			btn: ['立即解锁'],
			title: ['屏幕已锁定,请输入解锁密码','background:'+theme[0]+';color:'+theme[1]],
			closeBtn: 0,
			formType: 1
		}, function (value, index, elem) {
			if (value.length < 1) {
				layer.msg('请输入解锁密码');
				return false;
			} else {
				if(value == layui.data('vaeAdmin_lock').lock){
					layer.close(index);
					$(".yy").hide();
					//清除密码
					layui.data('vaeAdmin_lock', {
					  key: 'lock'
					  ,remove: true
					});
					layer.msg('解锁成功,欢迎回来!');
				}else{
					layer.msg('密码错误', {anim: 6, time: 1000});
					return false;
				}
			}
		});
	}
	

	/**
	 * 添加新窗口
	 */
	$("body").on("click", "#navBar .layui-nav-item a, #userInfo a", function () {
		NProgress.start();
		// 如果不存在子级
		if ($(this).siblings().length == 0) {
			vTab.tabAdd($(this));
		}
		// 关闭其他展开的二级标签
		$(this).parent("li").siblings().removeClass("layui-nav-itemed");
		if (!$(this).attr("lay-id")) {
			var topLevelEle = $(this).parents("li.layui-nav-item");
			var childs = $("#navBar > li > dl.layui-nav-child").not(topLevelEle.children("dl.layui-nav-child"));
			childs.removeAttr("style");
		}
		NProgress.done();
	});

	/**
	 * 左侧菜单展开动画
	 */
	$("#navBar").on("click", ".layui-nav-item a", function () {
		if (!$(this).attr("lay-id")) {
			var superEle = $(this).parent();
			var ele = $(this).next('.layui-nav-child');
			var height = ele.height();
			ele.css({"display": "block"});
			// 是否是展开状态
			if (superEle.is(".layui-nav-itemed")) {
				ele.height(0);
				ele.animate({height: height + "px"}, function () {
					ele.css({height: "auto"});
				});
			} else {
				ele.animate({height: 0}, function () {
					ele.removeAttr("style");
				});
			}
		}
	});

	/**
	 * 左边菜单显隐功能
	 */
	$(".v-menu").click(function () {
		$(".layui-layout-admin").toggleClass("v-left-hide");
		$(this).find("i").toggleClass("v-menu-hide");
		localStorage.setItem("isResize", false);
		setTimeout(function () {
			localStorage.setItem("isResize", true);
		}, 1200);
	});

	/**
	 * 移动端的处理事件
	 */
	$("body").on("click", ".layui-layout-admin .v-left a[data-url], .v-make", function () {
		if ($(".layui-layout-admin").hasClass("v-left-hide")) {
			$(".layui-layout-admin").removeClass("v-left-hide");
			$(".v-menu").find('i').removeClass("v-menu-hide");
		}
	});

	/**
	 * tab左右移动
	 */
	$("body").on("click", ".vNavMove", function () {
		var moveId = $(this).attr("data-id");
		var that = this;
		vTab.navMove(moveId, that);
	});

	/**
	 * 刷新当前tab页
	 */
	$("body").on("click", ".v-refresh", function () {
		NProgress.start();
		vTab.refresh(this, function (vTab) {
			//刷新之后所处理的事件
			NProgress.done();
		});
	});

	/**
	 * 关闭tab页
	 */
	$("body").on("click", "#tabAction a", function () {
		var num = $(this).attr("data-num");
		vTab.tabClose(num);
	});

	/**
	 * 键盘的事件监听
	 */
	$("body").on("keydown", function (event) {
		event = event || window.event || arguments.callee.caller.arguments[0];

		// 按 Esc
		if (event && event.keyCode === 27) {
			console.log("Esc");
			$("#fullScreen").children("i").eq(0).removeClass("layui-icon-screen-restore");
		}
		// 按 F11
		if (event && event.keyCode == 122) {
			console.log("F11");
			$("#fullScreen").children("i").eq(0).addClass("layui-icon-screen-restore");
		}
	});

	/**
	 * 全屏/退出全屏
	 */
	$("body").on("click", "#fullScreen", function () {
		if ($(this).children("i").hasClass("layui-icon-screen-restore")) {
			screenFun(2).then(function () {
				$("#fullScreen").children("i").eq(0).removeClass("layui-icon-screen-restore");
			});
		} else {
			screenFun(1).then(function () {
				$("#fullScreen").children("i").eq(0).addClass("layui-icon-screen-restore");
			});
		}
	});

	/**
	 * 全屏和退出全屏的方法
	 * @param num 1代表全屏 2代表退出全屏
	 * @returns {Promise}
	 */
	function screenFun(num) {
		num = num || 1;
		num = num * 1;
		var docElm = document.documentElement;

		switch (num) {
			case 1:
				if (docElm.requestFullscreen) {
					docElm.requestFullscreen();
				} else if (docElm.mozRequestFullScreen) {
					docElm.mozRequestFullScreen();
				} else if (docElm.webkitRequestFullScreen) {
					docElm.webkitRequestFullScreen();
				} else if (docElm.msRequestFullscreen) {
					docElm.msRequestFullscreen();
				}
				break;
			case 2:
				if (document.exitFullscreen) {
					document.exitFullscreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.webkitCancelFullScreen) {
					document.webkitCancelFullScreen();
				} else if (document.msExitFullscreen) {
					document.msExitFullscreen();
				}
				break;
		}

		return new Promise(function (res, rej) {
			res("返回值");
		});
	}

	/**
	 * 退出操作
	 */
	$("#logout").click(function () {
		layer.confirm("确定要退出吗？", {icon: 3, title:['警告','background:'+theme[0]+';color:'+theme[1]]}, function (index) {
			$.ajax({
				url:'/index.php/admin/index/adminLogout',
				type:'post',
				success:function(e){
					if(e.code == 200) {
						layer.msg(e.msg);
						vTab.removeTabStorage(function (res) {
							vTab.removeTabStorage();
							setTimeout(function(){
								window.location = e.data;
							},1500);
						});
					}
				}
			})
			
		});
	});

	/**
	 * 锁定账户
	 */
	$("#lock").click(function () {
		$(".yy").show();
		layer.prompt({
			btn: ['立即锁屏'],
			title: ['设定密码可锁定屏幕','background:'+theme[0]+';color:'+theme[1]],
			formType: 1
		}, function (value, index, elem) {
			if (value.length < 1) {
				layer.msg('请先输入解锁密码');
				return false;
			} else {
				layui.data('vaeAdmin_lock', {
				  key: 'lock'
				  ,value: value
				});
				layer.close(index);
				layer.prompt({
					btn: ['解锁'],
					title: ['屏幕已锁定,请输入解锁密码','background:'+theme[0]+';color:'+theme[1]],
					closeBtn: 0,
					formType: 1
				}, function (value, index, elem) {
					if (value.length < 1) {
						layer.msg('请输入解锁密码');
						return false;
					} else {
						if(value == layui.data('vaeAdmin_lock').lock){
							layer.close(index);
							$(".yy").hide();
							//清除密码
							layui.data('vaeAdmin_lock', {
							  key: 'lock'
							  ,remove: true
							});
							layer.msg('解锁成功,欢迎回来!');
						}else{
							layer.msg('密码错误', {anim: 6, time: 1000});
							return false;
						}
					}
				});
			}
		});
	});

	/**
	 * 点击头像修改资料
	 * @Author   听雨
	 * @DateTime 2020-03-12
	 * @param    {[type]}   ){		layer.open({			type: 2,			content:"/index.php/admin/index/editAdminInfo",			title:["修改个人信息",'background:'+theme[0]+';color:'+theme[1]],			area: ['50%', '50%']		})	} [description]
	 * @return   {[type]}                              [description]
	 */
	$('#thumb').click(function(){
		layer.open({
			type: 2,
			content:"/index.php/admin/index/editAdminInfo",
			title:["修改个人信息",'background:'+theme[0]+';color:'+theme[1]],
			area: ['50%', '50%']
		})
	})

	/**
	 * 便签
	 * @Author   听雨
	 * @DateTime 2020-03-11
	 * @param    {[type]}   )        {		layer.confirm("确定要锁定账户吗？", function (index) {			layer.close(index);			$(".yy").show();			layer.prompt({				btn: ['确定'],				title: '输入密码解锁(123456)',				closeBtn: 0,				formType: 1			} [description]
	 * @param    {[type]}   function (value,                        index,   elem)   {				if                                                              (value            [description]
	 * @return   {[type]}            [description]
	 */
	$("#note").click(function () {
		let note = layui.data('vaeAdmin_note').note || '这是一个本地的便签';
		layer.prompt({
		  formType: 2,
		  value: note,
		  title: ['便签','background:'+theme[0]+';color:'+theme[1]],
		  area: ['280px', '140px'], //自定义文本域宽高
		  offset: ['50px', 'calc(100% - 400px)'],
		  btn: false,
		  shade: 0,
		  id: "vae_admin_index_note"
		});
		$('#vae_admin_index_note').bind('input propertychange','textarea',function(){
		    var text = $('#vae_admin_index_note textarea').val();
		    layui.data('vaeAdmin_note', {
			  key: 'note'
			  ,value: text
			});
		});
	});

	/**
	 * 清除系统缓存
	 * @Author   听雨
	 * @DateTime 2020-03-09
	 * @param    {[type]}   e){		var that          [description]
	 * @param    {[type]}   1000)		                 		}          else {		  			layer.tips(res.msg,that);		  			$("[vaeyo-loading]").hide();		  		}		  	}		})	} [description]
	 * @return   {[type]}             [description]
	 */
	$("#vae-del-cache").on('click', function(e){
		var that = $(this);
		layer.confirm('确定要清空系统缓存吗?', {icon: 3, title:['警告','background:'+theme[0]+';color:'+theme[1]]}, function(index){
		    //do something
			if(that.attr('class') === 'clearThis'){
				layer.tips('正在努力清理中...',that);
				return false;
			}
			layer.tips('正在清理系统缓存...',that);
			that.attr('class','clearThis');
			$.ajax({
			  	url:"/index.php/admin/index/cacheClear",
			  	success:function(res){
			  		that.attr('class','');
			  		if(res.code == 200){
			  			setTimeout(function(){
			  				layer.tips(res.msg,that);
			  			},1000)
			  		} else {
			  			layer.tips(res.msg,that);
			  		}
			  	}
			})
		    layer.close(index);
		});
	})

	/**
	 * 主题
	 * @Author   听雨
	 * @DateTime 2020-03-09
	 * @param    {[type]}   color){	                            layer.msg("换个颜色换种心情");	                 } [description]
	 * @param    {[type]}   change:   function(color){	    	var RgbValue                [description]
	 * @return   {Function}           [description]
	 */
	colorpicker.render({
	    elem: '#vae-color'
	    ,color: theme ? theme[0] : '#009688'
	    ,format: 'rgb'
	    ,predefine: true
	    // ,alpha: true
	    ,size: "xs"
	    ,done: function(color){
	        var RgbValue = color.replace("rgba(", "").replace(")", "");
	    	var RgbValueArry = RgbValue.split(",");
	    	var $grayLevel = RgbValueArry[0] * 0.299 + RgbValueArry[1] * 0.587 + RgbValueArry[2] * 0.114;
	    	var thatColor;
	    	if ($grayLevel >= 192) { 
	    		thatColor = "#000000";
			} else {
			　　thatColor = "#ffffff";
			}
	        layui.data('vaeAdmin_theme', {
			  key: 'color'
			  ,value: [color,thatColor]
			});
			layer.msg("主题设置成功,双击主题按钮可恢复默认");
	    }
	    ,change: function(color){
	    	var RgbValue = color.replace("rgba(", "").replace(")", "");
	    	var RgbValueArry = RgbValue.split(",");
	    	var $grayLevel = RgbValueArry[0] * 0.299 + RgbValueArry[1] * 0.587 + RgbValueArry[2] * 0.114;
	    	var thatColor;
	    	if ($grayLevel >= 192) { 
	    		thatColor = "#000";
			} else {
			　　thatColor = "#fff";
			}

			//左侧菜单选中时的主题颜色
			document.styleSheets[1].cssRules[0].style.background=color;
			document.styleSheets[1].cssRules[0].style.color=thatColor;
			// tab主题颜色
	   		document.styleSheets[1].cssRules[1].style.color=color;
			theme = [color,thatColor];
			layer.msg("您正在预览主题，点击确定完成主题设置");
	    }
	});

	$('#vae-color').on('dblclick', function(e){
		layui.data('vaeAdmin_theme', {
		  key: 'color'
		  ,remove: true
		});
		//左侧菜单选中时的主题颜色
		document.styleSheets[1].cssRules[0].style.background='#009688';
		document.styleSheets[1].cssRules[0].style.color="#ffffff";
		// tab主题颜色
   		document.styleSheets[1].cssRules[1].style.color='#009688';

   		theme = ['#009688','#ffffff'];
		layer.msg("主题已成功恢复默认");
	})
});
