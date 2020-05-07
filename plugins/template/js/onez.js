var onez=onez||{};
onez.debug=true;
const _log = console.log;
console.log2 = (...args) => {
  if(onez.debug){
    _log.apply(console, args);
  }
};
onez.readyList=[];
onez.ready=function(func){
  onez.readyList.push(func);
};
onez.start=function(){
  if(onez.readyList.length>0){
    for(var i=0;i<onez.readyList.length;i++){
      try{
        $(onez.readyList[i]);
      }catch(e){
        console.warn(e);
      }
    }
    onez.readyList=[];
  }
};
onez.alert=function(message,callback){
  if(typeof callback=='undefined'){
    layer.alert(message);
  }else{
    layer.alert(message,function(index){
      layer.close(index);
      callback();
    });
  }
};
onez.confirm=function(message,callback){
  if(typeof callback=='undefined'){
    layer.alert(message);
  }else{
    layer.confirm(message,function(index){
      layer.close(index);
      callback();
    });
  }
};
onez.reload=function(){
  window.location.reload();
};
onez.reload2=function(){
  parent.location.reload();
};
onez.closeWin=function(form){
  if(parent==self){
    window.close();
  }else{
    if(typeof parent.closeWin=='function'){
      parent.closeWin();
    }
  }
};
onez.doit=function(data,runFunc){
  if(!data){
    return;
  }
  if(runFunc){
    if(data.exit){
      if(onez.pages.length>0){
        onez.pages[onez.pages.length-1].close();
      }
    }
    if(data.update){
      $(document).trigger('onez-update',data.update);
    }
    if(data.token){
      onez.onEvent(data);
    }else if(data.goto=='reload'){
      if(onez.pages.length>0){
        onez.pages[onez.pages.length-1].update();
      }
    }else if(data.goto=='refresh'){
      onez.reload();
    }else if(data.goto=='reload2'){
      onez.updateAll();
      if(onez.pages.length>0){
        onez.pages[onez.pages.length-1].close();
      }
    }else if(data.goto=='close'){
      if(onez.pages.length>0){
        onez.pages[onez.pages.length-1].close();
      }
    }else{
      onez.loadPage({action:data.goto});
    }
    return;
  }
  if(typeof data.error=='string'){
    onez.alert(data.error);
    return;
  }
  if(typeof data.message=='string'){
    onez.alert(data.message,function(){
      onez.doit(data,true);
    });
  }else{
    onez.doit(data,true);
  }
};
onez.formpost=function(form){
  var postdata=$(form).serializeObject();
  if(!postdata._ajax_action){
    postdata._ajax_action=onez.curPage(form).data._ajax_action;
  }
  $.post(window.location.href,postdata,function(data){
    onez.doit(data);
  },'json');
};
onez.del=function(id,obj){
  onez.confirm('您确定要删除这条记录吗？',function(){
    $.post(window.location.href,{action:'delete',id:id,ajax:1,_ajax_action:onez.curPage(obj).data._ajax_action},function(data){
      onez.doit(data);
    },'json');
  });
};
onez.postList=[];
onez.postIng=false;
onez.post=function(action,postdata,callback){
  if(action){
    onez.postList[action]={postdata:postdata,callback:callback};
  }else{
    onez.postIng=false;
  }
  if(!onez.postIng){
    onez.postIng=true;
    setTimeout(function(){
      var postdatas={};
      var actions=[];
      for(var action in onez.postList){
        actions.push(action);
        postdatas[action]=onez.postList[action].postdata;
      }
      $.post(window.location.href,{_action:'posts',_postdatas:JSON.stringify(postdatas)},function(data){
        data.actions=data.actions||{};
        for(var i=0;i<actions.length;i++){
          var action=actions[i];
          if(data.actions[action]){
            onez.postList[action].callback(data.actions[action]);
          }else{
            onez.postList[action].callback({error:'没有数据'});
          }
          delete onez.postList[action];
        }
        var hasMore=false;
        for(var action in onez.postList){
          hasMore=true;
          break;
        }
        if(hasMore){
          onez.post();
        }else{
          onez.postIng=false;
        }
      },'json');
    },20);
  }
};
onez.setTitle=function(title){
  if(title){
    document.title=title;
  }
};
onez.Res=function(res,callback){
  var that=this;
  var list=[];
  this.load=function(){
    if(list.length<1){
      if(typeof callback=='function'){
        callback();
      }
    }else{
      var res=list.shift();
      if(!res.id || $('#'+res.id).length==0){
        if(res.type=='js'){
          var script = document.createElement("script");
          if (script.readyState) {
            script.onreadystatechange = function () {
              if (script.readyState == "loaded" || script.readyState == "complete") {
                script.onreadystatechange = null;
                that.load();
              }
            };
          } else {
            script.onload = function () {
              that.load();
            };
          }
          script.src = url;
          document.body.appendChild(script);
          return;
        }else if(res.type=='css'){
          $('<link rel="stylesheet" href="'+res.url+'" />').appendTo('body');
        }
      }
      that.load();
    }
  };
  if(res){
    for(var i=0;i<res.length;i++){
      list.push(res[i]);
    }
  }
  this.load();
};

onez.Vue=function(el,data,pageId,opt){
  var opt=opt||{};
  var vue=new Vue({
    el: el,
    data:function(){
      return {
        data:data,
        action:''
      };
    },
    created:function(){
    },
    methods:{
      refresh:function(){
        if(opt.refresh){
          opt.refresh(this);
        }
      },
      pushRecord:function(data){
        if(opt.pushRecord){
          opt.pushRecord(this,data);
        }
      }
    },
    mounted:function(){
      if(pageId){
        $(el).attr('data-page-id',pageId);
      }
      $(el).find('a[href]').each(function(){
        var href=$(this).attr('href');
        var target=$(this).attr('target')||'_self';
        if(href=='#'||href.indexOf('javascript:')!=-1||target=='_blank'){
          
        }else{
          $(this).attr('href','javascript:;');
          $(this).attr('data-token','page');
          $(this).attr('data-action',href);
          $(this).attr('data-target',$(this).attr('target')||'_self');
        }
      });
      
      if(data.component){
        if(data.component.js){
          $('<script type="text/javascript">'+data.component.js+'</script>').appendTo($(el));
        }
      }
      if(opt.mounted){
        opt.mounted(this);
      }
    },
    beforeDestroy:function(){
      $(el).off('click','[data-token]');
      $(el).off('click','[data-role]');
    }
  });
  return vue;
};
onez.events={};
onez.onEvent=function(data){
  if(typeof data=='string' && data.substr(0,1)=='{'){
    data=JSON.parse(data);
  }
  if(!data||!data.token){
    return;
  }
  if(typeof data.token=='string' && data.token.substr(0,1)=='{'){
    data=JSON.parse(data.token);
  }
  if(data.token.token){
    data=Object.assign(data,data.token);
  }
  if(data.exit){
    page=onez.findPageByEl(window.event?window.event.target:data.el);
    if(page){
      page.close();
    }
  }
  var token=data.token;
  if(token=='page'){
    if(data.target=='url'){
      var w=data.width||800;
      var h=data.height||600;
      layer.open({
        type: 2
        ,title: data.title
        ,area: [w+'px', h+'px']
        ,shade: 0
        ,maxmin: true
        ,moveOut: true
        ,offset: 'auto' 
        ,content: data.action
        ,zIndex: layer.zIndex
        ,success: function(layero){
          layer.setTop(layero);
        }
      });
    }else{
      onez.loadPage(data);
    }
  }else if(token=='set'){
    var $vue=onez.curPage(data.$el);
    Vue.set($vue.data,data.key,data.value);
  }else if(token=='setAction'){
    if(onez.pages.length>0){
      onez.pages[onez.pages.length-1].opt.action=data.action;
      onez.pages[onez.pages.length-1].update();
    }
  }else if(token=='close'){
    page=onez.findPageByEl(window.event?window.event.target:data.el);
    if(page){
      page.close();
    }  
  }else if(token=='logout'){
    onez.confirm('确定要退出吗？',function(){
      onez.loadData({
        action:'login',
        postdata:{type:'logout'},
        onLoad:function(){
          location.reload();
        }
      });
    });
  }else if(token=='ajax'){
    var page=$(window.event?window.event.target:data.$el).page();
    if(page){
      if(data.confirm){
        onez.confirm(data.confirm,function(){
          delete data.confirm;
          onez.loadData({
            action:page.action+(page.action.indexOf('?')==-1?'?':'&')+'t='+Math.random(),
            postdata:data,
            onLoad:function(o){
              onez.doit(o);
            }
          });
        });
      }else{
        onez.loadData({
          action:page.action+(page.action.indexOf('?')==-1?'?':'&')+'t='+Math.random(),
          postdata:data,
          onLoad:function(o){
            onez.doit(o);
          }
        });
      }
    }
  }else if(token=='show'){
    if(data.pos){
      var left=window.event.pageX;
      var top=window.event.pageY;
      if(data.pos=='mouse'){
      }else{
        left=$(data.pos).offset().left+$(data.pos).width();
        top=$(data.pos).offset().top;
      }
      if(data.box){
        left-=$(data.box).offset().left;
        top-=$(data.box).offset().top;
      }
      $(data.el).css({
        left:left+'px',
        top:top+'px'
      });
    }
    $(data.el).show();
  }else{
    if(typeof onez.events[token]!='undefined'){
      onez.events[token](data);
    }
  }
  console.log(data);
};
onez.curPage=function(obj){
  var pageId=$(obj).closest('[data-page-id]').attr('data-page-id');
  if(pageId){
    var page=onez.findPageById(pageId);
    if(page){
      return page.vue;
    }
  }
  return onez.pages[onez.pages.length-1].vue;
};
onez.pages=[];
onez.loadPage=function(data){
  var page=false;
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].action==data.action){
      if(i==onez.pages.length-1){//当前
        onez.pages[i].show();
      }
      page=onez.pages.splice(i,1)[0];
      break;
    }
  }
  
  if(page){
    if(onez.pages.length>0){
      onez.pages[onez.pages.length-1].hide();
    }
    onez.pages.push(page);
    if(onez.pages.length>20){
      onez.pages.shift();
    }
    page.show(data);
  }else{
    onez.pages.push(new onez.Page(data));
  }
};
onez.updateAll=function(){
  for(var i=0;i<onez.pages.length;i++){
    onez.pages[i].waitUpdate=true;
  }
  $('[data-page-id]').each(function(){
    var page=onez.findPageById($(this).attr('data-page-id'));
    if(page){
      page.update();
    }
  });
};
onez.findPageById=function(pageId){
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].id==pageId){
      return onez.pages[i];
    }
  }
  return false;
};
onez.findPageByEl=function(el){
  var pageEl=$(el).closest('[data-page-id]');
  if(pageEl.length>0){
    return onez.findPageById(pageEl.attr('data-page-id'));
  }
  return false;
};
onez.hashObj={};
onez.hash=function(key,value){
  if(typeof value=='undefined'){
    var hash=location.hash;
    if(hash.length>1 && hash.substr(0,1)=='#'){
      var o=hash.substr(1).split('|');
      onez.hashObj.action=o[0];
      for(var i=1;i<o.length-1;i+=2){
        onez.hashObj[o[i]]=o[i+1];
      }
    }
    onez.hashObj.action=onez.hashObj.action||'index';
    return onez.hashObj[key]||'';
  }else{
    onez.hashObj[key]=value;
    var hash=onez.hashObj.action||'index';
    if(key!='action'){
      for(var k in onez.hashObj){
        if(k!='action' && onez.hashObj[k]){
          hash+='|'+k+'|'+onez.hashObj[k];
        }
      }
    }
    location.hash=hash;
  }
};
onez.addLoading=function(el){
  var box=$(el).closest('.layui-layer-content');
  var i=$('<i class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop" style="font-size:36px;position: absolute;"></i>').appendTo($(el));
  i.css({
    left:((box.width()-i.width())/2)+'px',
    top:((box.height()-i.height())/2)+'px'
  });
};
onez.Page=function(opt){
  var that=this;
  that.id=(''+Math.random()).split('.')[1];
  that.data=false;
  that.status='new';
  that.action=opt.action;
  that.opt=opt;
  that.page=1;
  //显示
  that.show=function(_opt){
    if(_opt){
      opt=_opt;
      that.opt=opt;
    }
    var target=opt.target||'_self';
    if(target=='_self'){
      onez.hash('action',opt.action);
      opt.el='#content';
      $('.page-current').removeClass('page-current active');
      $('[data-action="'+opt.action+'"]').each(function(){
        $(this).addClass('page-current active');
        $(this).parents('li').addClass('page-current active');
      });
    }else if(target=='win'){
      if(that.layerIndex){
        layer.title(that.data.title, that.layerIndex);
        if(opt.el){
          $(opt.el).closest('.layui-layer').find('.layui-layer-title').trigger('mousedown').trigger('mouseup');
        }
      }else{
        opt.el='#page-'+that.id;
        var w=opt.width||800;
        var h=opt.height||600;
        var dlg={
          type: 1
          ,title: that.data.title?that.data.title:opt.title
          ,area: [w+'px', h+'px']
          ,shade: 0
          ,maxmin: false
          ,moveOut: true
          ,offset: 'auto' 
          ,content: '<div class="onez-win-title"></div><div class="onez-win-body" id="'+opt.el.substr(1)+'"></div>'
          ,zIndex: layer.zIndex
          ,success: function(layero){
            that.layero=layero;
            layer.setTop(layero);
            onez.addLoading(opt.el);
          }
          ,end:function(){
            for(var i=0;i<onez.pages.length;i++){
              if(onez.pages[i].action==that.action){
                onez.pages.splice(i,1);
                break;
              }
            }
          }
        };
        if(opt.noTitle){
          dlg.title=false;
          dlg.closeBtn=0;
          dlg.move='.onez-win-title';
        }
        if(opt.maxmin){
          dlg.maxmin=true;
        }
        if(opt.noScroll){
          dlg.scrollbar=false;
        }
        if(opt.shade){
          dlg.shade=[0.01,'#000000'];
          dlg.shadeClose=true;
          dlg.isOutAnim=false;
        }
        if(opt.dlgId){
          dlg.id=opt.dlgId;
        }
        if(opt.auto){
          if(opt.width){
            dlg.area=opt.width+'px';
          }else{
            dlg.area='auto';
          }
        }
        if(opt.pos){
          if(opt.pos=='mouse'){
            if(window.event){
              dlg.offset=[window.event.pageY+'px',window.event.pageX+'px'];
            }
          }
        }
        that.layerIndex=layer.open(dlg);
      }
    }
    if(that.data){
      if(target=='_self'){
        if(that.data.title){
          onez.app.data.pageTitle=that.data.title;
          onez.setTitle(that.data.title);
        }
      }
      if(that.layerIndex){
        if(that.data.style){
          layer.style(that.layerIndex,that.data.style); 
        }
      }
      that.status='show';
      var html=onez.pageTpl();
      if(that.data.hasMore){
        html+='<a href="javascript:;" data-role="more" data-page-id="'+that.id+'" class="more">加载更多</a>';
      }
      $(opt.el).html(html);
      that.vue=onez.Vue(opt.el,that.data,that.id,{
        mounted:function(){
          if(opt.pos){
            if(opt.pos=='mouse'){
              setTimeout(function(){
                var layBox=$(opt.el).closest('.layui-layer-page');
                var left=parseInt(layBox.css('left'));
                var top=parseInt(layBox.css('top'));
                var width=layBox.width();
                var height=layBox.height();
                if(left>$(window).width()-width){
                  left-=width;
                  layBox.css({left:left+'px'});
                }
                if(top>$(window).height()-height){
                  top-=height;
                  layBox.css({top:top+'px'});
                }
              });
            }
          }
        }
      });
      
      if(that.waitUpdate){
        that.update();
        if(!that.data.autoUpdate){
          that.waitUpdate=false;
        }
      }
      if(that.data.token){
        onez.onEvent(that.data.token);
      }
    }else{
      if(target=='_self'){
        that.loadIndex=layer.load();
      }
      if(opt.pageData){
        if(target=='_self'){
          layer.close(that.loadIndex); 
        }
        that.data=opt.pageData;
        that.show();
        that.page=1;
      }else{
        that.status='loading';
        onez.loadData({
          action:opt.action,
          onLoad:function(data){
            if(target=='_self'){
              layer.close(that.loadIndex); 
            }
            that.data=data;
            that.show();
            that.page=1;
          }
        });
      }
    }
  };
  //隐藏
  that.hide=function(){
    that.status='hide';
    if(that.vue){
    
    }
  };
  //更新
  that.update=function(){
    onez.loadData({
      action:that.opt.action,
      onLoad:function(data){
        that.data=data;
        that.show();
        that.page=1;
      }
    });
  };
  //加载更多
  that.loadMore=function(){
    that.page++;
    onez.loadData({
      action:that.opt.action,
      postdata:{page:that.page},
      onLoad:function(data){
        if(!data.hasMore){
          $(opt.el).find('.more').hide();
        }
        var record=that.vue.data.record||[];
        if(data.record){
          for(var i=0;i<data.record.length;i++){
            record.push(data.record[i]);
          }
          Vue.set(that.vue.data,'record',record);
        }
      }
    });
  };
  //关闭
  that.close=function(){
    if(opt.noClose){
      return;
    }
    var target=opt.target||'_self';
    if(target=='_self'){
      onez.pages.pop();
      if(onez.pages.length>0){
        onez.pages[onez.pages.length-1].show();
      }
    }else if(target=='win'){
      if(that.layerIndex){
        layer.close(that.layerIndex); 
      }
    }
  };
  that.show();
};
onez.pageTpl=function(){
  var html='';
  html+='<template v-if="data.record" v-for="item in data.record">';
  html+='<onez :type="item.type" :data="item"></onez>';
  html+='</template>';
  return html;
};
onez.time = function() {
  return Math.floor((new Date()).getTime() / 1000);
};
onez.time_str=function(time){
  if(typeof time=='string'){
    time=parseInt(time);
  }
  if(isNaN(time)){
    return time;
  }
  time=parseInt(time+'');
  var dur = onez.time()-time;
  if(dur < 0){
    dur=0;
  }
  if(dur < 60){
    return '刚刚';
  }else if(dur < 3600){
    return Math.ceil(dur/60)+'分钟前';
  }else if(dur < 86400){
    return Math.ceil(dur/3600)+'小时前';
  }else if(dur < 259200){//3天内
    return Math.ceil(dur/86400)+'天前';
  }else{
    var date_ = new Date();
    var Y_=date_.getFullYear();
    var date = new Date(time*1000);
    var Y=date.getFullYear();
    var M=date.getMonth()+1;
    var D=date.getDate();
    if(M<10){
      M='0'+M;
    }
    if(D<10){
      D='0'+D;
    }
    if(Y_==Y){
      return M+'-'+D;
    }else{
      return Y+'-'+M+'-'+D;
    }
  }
};
onez.loadData=function(opt){
  var postdata=opt.postdata||{};
  postdata._action='getData';
  postdata.action=opt.action;
  onez.post(opt.action,postdata,function(res){
    var data=res.data||res;
    if(data.bodyClass){
      $('body').addClass((typeof data.bodyClass=='object')?data.bodyClass.join(' '):data.bodyClass);
    }
    if(data.res){
      new onez.Res(data.res);
    }
    if(data.onez){
      for(var k in data.onez){
        onez[k]=data.onez[k];
      }
    }
    if(opt.onLoad){
      opt.onLoad(data);
    }
  });
};
onez.components={};
onez.loadComponent=function(type,callback){
  if(onez.components[type]){
    if(onez.components[type].status=='ok'){
      callback(onez.components[type].data);
    }else{
      onez.components[type].callbacks.push(callback);
    }
  }else{
    onez.components[type]={
      status:'loading',
      callbacks:[callback]
    };
    onez.post('_component?type='+type,{},function(data){
      if(data.type==type){
        onez.components[type].status='ok';
        onez.components[type].data=data;
        if(data.css){
          $('<style></style>').text(data.css).appendTo('head');
        }
        for(var i=0;i<onez.components[type].callbacks.length;i++){
          onez.components[type].callbacks[i](onez.components[type].data);
        }
        onez.components[type].callbacks=[];
      }
    },'json');
  }
};
onez.vues={};
onez.ready(function(){
  $.fn.serializeObject = function(){  
     var o = {};  
     var a = this.serializeArray();  
     $.each(a, function() {  
         if (o[this.name]) {  
             if (!o[this.name].push) {  
                 o[this.name] = [o[this.name]];  
             }  
             o[this.name].push(this.value || '');  
         } else {  
             o[this.name] = this.value || '';  
         }  
     });  
     return o;  
  };
  $.fn.close = function(){  
    var page=onez.findPageByEl(this);
    if(page){
      page.close();
    }
  };
  $.fn.page = function(){  
    return onez.findPageByEl(this);
  };
  $.fn.vue = function(){  
    var el=$(this);
    var role=el.attr('data-role');
    if(!role){
      el=el.closest('[data-role]');
    }
    if(el.length>0){
      var id=el.attr('id');
      if(onez.vues[id]){
        return onez.vues[id];
      }
    }
    return false;
  };
  $('body').on('click','[data-token]',function(){
    var data=$(this).data();
    data.token=$(this).attr('data-token')||data.token;
    data.$el=$(this);
    onez.onEvent(data);
  });
  $('body').on('click','[data-role]',function(e){
    var data=$(this).data();
    if(data.role=='more'){
      $(e.target).trigger('onez-role-more');
      if(data.pageId){
        var page=onez.findPageById(data.pageId);
        if(page){
          page.loadMore();
        }
      }
    }
  });
  $(document).bind('onez-update',function(e,data){
    var data=data||{};
    var actions=(data.action||'all').split('|');
    for(var k in onez.vues){
      if(onez.vues[k].action){
        for(var i=0;i<actions.length;i++){
          if(actions[i]=='all'||onez.vues[k].action.indexOf(actions[i])!=-1){
            onez.vues[k].refresh();
          }
        }
      }
    }
  });
  $(document).bind('onez-push',function(e,data){
    var data=data||{};
    var actions=(data.action||'all').split('|');
    for(var k in onez.vues){
      if(onez.vues[k].action){
        for(var i=0;i<actions.length;i++){
          if(actions[i]=='all'||onez.vues[k].action.indexOf(actions[i])!=-1){
            onez.vues[k].pushRecord(data);
          }
        }
      }
    }
  });
  $(window).bind('resize',function(){
    $('#app.wrapper').css({'min-height':$(window).height()});
  }).trigger('resize');
  window.addEventListener('message', function(e){
    var data=e.data;
    $(document).trigger('on-message',data);
    for(var k in data){
      if($('[name="'+k+'"]').length>0){
        $('[name="'+k+'"]').val(data[k]);
        var tmp=$('[name="'+k+'"]').parent().find('[data-tmpkey]');
        if(tmp.length>0){
          if(typeof OnezUploadResult=='function'){
          	OnezUploadResult(tmp.attr('data-tmpkey'),'ok',data[k]);
          }
        }
      }
    }
    window.closeWin();
  }, false);
  window.openWin=function(href,title){
    onez.onEvent({token:'page',action:href,target:'url',title:title});
  };
  onez.canClose=true;
  window.closeWin=function(){
    if(!onez.canClose){
      return;
    }
    onez.canClose=false;
    setTimeout(function(){
      onez.canClose=true;
    },100);
    var $el=$('.layui-layer-iframe').last();
    var win=window;
    if($el.length>0){
      try{
        win=window[$el.find('iframe')[0]['name']];
        var index = layer.getFrameIndex(win.name);
        layer.close(index);
      }catch(e){
        $el.remove();
      }
    }
  };
  /*
  Vue.config.errorHandler = function(err, vm, info) {
    console.log(err,vm,info);
  }
  */
  Vue.prototype.onez = onez;
  //注册组件
  Vue.component('onez',{
    data:function(){
      return {
        elId:'',
        content:'',
        vue:null
      };
    },
    props:['type','data','tpl','action'],
    watch: {
      action:function(val){
        this.vue.action=val;
        this.refresh();
      }
    },
    methods:{
      init:function(vue){
        this.vue=vue;
        if(this.action){
          this.vue.action=this.action;
          this.refresh();
        }
      },
      pushRecord:function(vue,data){
        console.log('pushRecord',data);
        var that=this;
        var record=that.vue.data.record||[];
        if(data.pos=='before'){
          record.unshift(data.item);
        }else{
          record.push(data.item);
        }
        Vue.set(that.vue.data,'record',record);
      },
      refresh:function(){
        var that=this;
        this.page=1;
        this.loadData(this.page,function(data){
          Vue.set(that.vue,'data',data);
          if(data.hasMore){
            that.hasMore=true;
            if(typeof that.tpl!='object'){
              that.tpl=(that.tpl||'none,none').split(',');
              if(that.tpl.length!=2){
                that.tpl=['none','none'];
              }
            }
            if(that.tpl[0]!='none'||that.tpl[1]!='none'){
              if(that.tpl[0]=='more'){
                that.addMore('before');
              }else{
                that.addMore('after');
              }
            }
          }
          
        });
      },
      addMore:function(pos,text,classname){
        var that=this;
        setTimeout(function(){
          $('#'+that.elId+' [data-role="more"]').remove();
          var more=$('<a href="javascript:;" data-role="more" class="more">'+(text||'加载更多')+'</a>');
          if(classname){
            more.addClass(classname);
          }
          if(pos=='before'){
            more.prependTo($('#'+that.elId));
          }else{
            more.appendTo($('#'+that.elId));
          }
          $('#'+that.elId).unbind('onez-role-more');
          if(that.hasMore){
            $('#'+that.elId).bind('onez-role-more',function(){
              $('#'+that.elId+' [data-role="more"]').addClass('loading').text('加载中...');
              that.loadMore(pos);
            });
          }
        },100);
      },
      loadMore:function(pos){
        var that=this;
        if(!that.hasMore){
          return;
        }
        this.page++;
        this.loadData(this.page,function(data){
          that.hasMore=data.hasMore;
          var record=that.vue.data.record||[];
          if(data.record){
            for(var i=0;i<data.record.length;i++){
              if(pos=='before'){
                record.unshift(data.record[i]);
              }else{
                record.push(data.record[i]);
              }
            }
            Vue.set(that.vue.data,'record',record);
            if(data.hasMore){
              that.addMore(pos);
            }
          }else{
            that.hasMore=false;
          }
          if(!that.hasMore){
            that.addMore(pos,'没有更多了','nomore');
          }
        });
      },
      loadData:function(page,callback){
        onez.loadData({
          action:this.action,
          postdata:{page:page},
          onLoad:callback
        });
      }
    },
    mounted:function(){
      var type=this.type||'default';
      var data=this.data||{};
      var that=this;
      var id='comp-'+((''+Math.random()).split('.')[1]);
      that.elId=id;
      that.$el.setAttribute('id',id);
      that.$el.setAttribute('data-role','vue');
      that.$el.setAttribute('class','comp-'+type);
      //绑定远程数据
      if(type=='form'){
        that.content=data.html;
        setTimeout(function(){
          $('<script type="text/javascript" id="'+data.formId+'-js">'+data.js+'</script>').appendTo($('#'+id));
        },100);
      }else if($('#temp-'+type).length>0){
        var res={html:$('#temp-'+type).text()};
        if($('#js-'+type).length>0){
          res.js=$('#js-'+type).text();
        }
        that.content=res.html;
        setTimeout(function(){
          data.component=res;
          that.vue=onez.Vue('#'+id,data,false,{mounted:that.init,refresh:that.refresh,pushRecord:that.pushRecord});
          onez.vues[id]=that.vue;
        });
      }else{
        onez.loadComponent(type,function(res){
          that.content=res.html;
          setTimeout(function(){
            data.component=res;
            that.vue=onez.Vue('#'+id,data,false,{mounted:that.init,refresh:that.refresh,pushRecord:that.pushRecord});
            onez.vues[id]=that.vue;
          });
        });
      }
    },
    destroyed:function(){
      console.log('--------------');
      if(onez.vues[this.elId]){
        delete onez.vues[this.elId];
      }
    },
    template:'<div v-html="content"></div>'
  });
  //首页
  onez.loadData({
    action:'welcome',
    onLoad:function(data){
      onez.setTitle(data.title);
      data.pageTitle='';
      onez.app=onez.Vue('#app',data);
      var home=onez.hash('action');
      if(home==''){
        if(data.home){
          home=data.home;
        }else{
          home='index';
        }
      }
      onez.loadPage({action:home});
    }
  });
  var innerDocClick;
  $('body').on('mouseleave',function(){
    innerDocClick=false;
  });
  $('body').on('mouseover',function(){
    innerDocClick=true;
  });
  window.onhashchange=function(){
    if(!innerDocClick){
      onez.hash('action');
      onez.loadPage({action:onez.hash('action')});
    }
  }
});