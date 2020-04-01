// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +---------------------------------------------------------------------
layui.define(['layer', 'nprogress'], function(exports){ 
	let layer = layui.layer
  ,$ = layui.$;

	let obj = {
    ajax:function(opt){
      let load;
      let defaultSettings = {
        url: opt.url,
        data: opt.data || {},
        type: opt.type || 'get',
        dataType: 'json',
        async: opt.async?true:false,
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
          load = layer.load(1);
        },
        complete:function(){  
          layer.close(load);
        },
        success: opt.success,
        error: function(res){
          layer.msg('服务器错误，请稍后再试');
        }
      }
      return $.ajax(defaultSettings)
    },
    open:function(opt){
      let theme = layui.data('vaeAdmin_theme').color || ['#009688','#ffffff'];
      let defaultSettings = {
        title: [opt.title || '信息','background:'+theme[0]+';color:'+theme[1]],
        type: 2, 
        content: opt.href,
        shade: 0,
        maxmin: true,
        offset: '10px',
        area: opt.area || ['90%','90%'],
        end: opt.end
      }
      return layer.open(defaultSettings)
    },
    confirm:function(txt,success) {
      let theme = layui.data('vaeAdmin_theme').color || ['#009688','#ffffff'];
      layer.confirm(txt, {icon: 3, title:['警告','background:'+theme[0]+';color:'+theme[1]],shade: 0,offset: ['calc(50% - 200px)', 'calc(50% - 250px)']}, success);
    },
    close:function(time=1500){
      setTimeout(function(){
        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
        parent.layer.close(index); //再执行关闭 
      },time)
    }
	};
  
  exports('vae', obj);
});    