onez.im.$on('message',function(res){
  if(res.msgId){
    if(res.action){
      delete res.action;
    }
    var toUserId=onez.toUserId;
    if(onez.dialogVue().data.status!='dialog'){
      toUserId=''; 
    }
    onez.loadData({
      action:'message?type=read&toUserId='+toUserId,
      postdata:res,
      onLoad:function(data){
        if(data.update){
          $(document).trigger('onez-update',data.update);
        }
        if(data.screenMsg){
          onez.screenMsg(data.screenMsg);
        }
        if(data.sound){
          onez.im.playSound(data.sound);
        }
      }
    });
  }
  console.log('message',res);
});
//连接状态
onez.im.$on('status',function(res){
  console.log('status',res);
});
//检查群组变化
var myGroupIds={};
onez.appEvent=function(event){
  if(event=='userListUpdate'){
    setTimeout(function(){
      for(var gid in myGroupIds){
        myGroupIds[gid]=0;
      }
      $('.list-type-user[data-userid^="group."]').each(function(){
        var gid=$(this).attr('data-userid').substr(6);
        if(myGroupIds[gid]){
        }else{
          //新进入房间
          onez.im.$call('joinScene',{
            sceneId:gid
          });
        }
        myGroupIds[gid]=1;
      });
      //已退出的房间
      for(var gid in myGroupIds){
        if(myGroupIds[gid]==0){
          onez.im.$call('leaveScene',{
            sceneId:gid
          });
        }
      }
    },100);
  }
  return '<!--appEvent-->';
};