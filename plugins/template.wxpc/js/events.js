onez._dialog_vue=null;
onez._dialog=null;
onez.dialog=function(){
  if(onez._dialog==null){
    for(var i=0;i<onez.pages.length;i++){
      if(onez.pages[i].action=='dialog'){
        onez._dialog=onez.pages[i];
        $(document).on('click',function(e){
          if($(e.target).closest('.comp-usercard').length<1){
            if($('.comp-usercard').hasClass('canClose')){
              Vue.set(onez.dialogVue().data,'usercard',null);
            }
          }
        });
        break;
      }
    }
  }
  return onez._dialog;
};
onez.dialogVue=function(){
  if(onez._dialog_vue==null){
    onez._dialog_vue=onez.dialog().vue;
  }
  return onez._dialog_vue;
};
onez.events.sendTo=function(data){
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].action!='dialog'){
      if(onez.pages[i].layerIndex){
        onez.pages[i].close();
      }
    }
  }
  onez.toUserId=data.userid;
  onez.loadData({
    action:'message',
    postdata:{type:'active',userid:data.userid},
    onLoad:function(){
      $(document).trigger('onez-update','msglist');
    }
  });
  Vue.set(onez.dialogVue().data,'status','dialog');
  Vue.set(onez.dialogVue().data,'actionShowBox','messagelist&userid='+data.userid);
  setTimeout(function(){
    $('.main .header h2').html(data.uname);
    $('.input').focus();
  },200);
};
onez.events.userInfo=function(data){
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].action!='dialog'){
      if(onez.pages[i].layerIndex){
        onez.pages[i].close();
      }
    }
  }
  Vue.set(onez.dialogVue().data,'status','contact');
  Vue.set(onez.dialogVue().data,'actionUserInfo','userinfo&type=detail&userid='+data.userid);
};
onez.events.groupInfo=function(data){
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].action!='dialog'){
      if(onez.pages[i].layerIndex){
        onez.pages[i].close();
      }
    }
  }
  Vue.set(onez.dialogVue().data,'status','contact');
  Vue.set(onez.dialogVue().data,'actionUserInfo','groupInfo&groupId='+data.gid);
};
onez.events.toHome=function(){
  for(var i=0;i<onez.pages.length;i++){
    if(onez.pages[i].action!='dialog'){
      if(onez.pages[i].layerIndex){
        onez.pages[i].close();
      }
    }
  }
  onez.toUserId='';
  Vue.set(onez.dialogVue().data,'status','dialog');
  Vue.set(onez.dialogVue().data,'actionShowBox','');
};
onez.screenMsg=function(msg){
  console.log('---screenMsg',msg);
  $(document).trigger('onez-push',{item:msg,action:'messagelist'});
  onez.toBottom();
};
onez.toBottom=function(){
  $('.showbox').scrollTop($('.showbox').get(0).scrollHeight+50);
  setTimeout(function(){
    $('.showbox').scrollTop($('.showbox').get(0).scrollHeight+50);
  },100);
};
onez.msgGoto=function(type,record){
  if(record[0]['type']=='goto'){
    if(type=='bottom'){
      onez.toBottom();
    }
  }
};
onez.sendMsg=function(content,msgtype){
  onez.screenMsg({msg:{
    type:msgtype,
    userinfo:onez.me,
    content:content
  },from:'my',type:'user'});
  onez.loadData({
    action:'message?type=send',
    postdata:{toUserId:onez.toUserId,content:content,msgtype:msgtype},
    onLoad:function(){
      
    }
  });
};
onez.events.send=function(data){
  if(!onez.toUserId){
    onez.alert('请选择聊天对象');
    return;
  }
  var content=$('.input').val().replace(/^[\s\t\r\n]+/g,'').replace(/[\s\t\r\n]+$/g,'');
  $('.input').val('').focus();
  if(content.length>0){
    let msg = {text:content}
		onez.sendMsg(msg,'text');
  }
  
};