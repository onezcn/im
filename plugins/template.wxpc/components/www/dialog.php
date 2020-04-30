<?php

/* ========================================================================
 * $Id: dialog.php 8448 2020-04-30 19:28:06Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$json['html']=<<<ONEZ
<div class="onez-dialog">
  <div class="sidebar">
    <onez type="sidebar" :data="data.sidebar"></onez>
  </div>
  <div class="list">
    <div class="header">
      <onez type="search"></onez>
    </div>
    <div class="listBox" v-show="onez.dialogVue().data.status=='dialog'">
      <onez type="list" action="msglist" tpl="none,more" id="msglist"></onez>
    </div>
    <div class="listBox" v-show="onez.dialogVue().data.status=='contact'">
      <onez type="list" action="userlist" tpl="none,none" id="userlist"></onez>
    </div>
  </div>
  <div class="main">
    <div v-show="onez.dialogVue().data.status=='dialog' && onez.dialogVue().data.actionShowBox">
      <div class="header">
        <h2></h2>
        <div class="rtIcon" v-show="onez.dialogVue().data.actionShowBox.indexOf('group.')!=-1">
          <i class="icon layui-icon layui-icon-more" data-token="page" data-target="win" :data-action="'groupInfo?ispage=1&groupId='+onez.dialogVue().data.actionShowBox.split('group.')[1]" data-width="520" data-height="480"></i>
        </div>
      </div>
      <div class="showbox">
        <onez v-if="onez.dialogVue().data.actionShowBox" type="message" :action="onez.dialogVue().data.actionShowBox" tpl="more,none"></onez>
      </div>
      <div class="inputbox">
        <div class="toolbar">
          <template v-for="item in data.toolbar">
            <span class="item-btn" :class="item.classname" :title="item.name" :data-toolbar="item.toolbar"></span>
          </template>
        </div>
        <div class="input-border">
          <textarea class="input"></textarea>
        </div>
        <button type="button" class="sendbtn" data-token="send">发送</button>
      </div>
    </div>
    <div v-show="onez.dialogVue().data.status=='contact'">
      <onez v-if="onez.dialogVue().data.actionUserInfo" type="userinfo" :action="onez.dialogVue().data.actionUserInfo"></onez>
    </div>
  </div>
  <template v-if="onez.dialogVue().data.usercard">
    <onez class="usercard" type="usercard" :data="onez.dialogVue().data.usercard"></onez>
  </template>
</div>
ONEZ;
$json['js']=<<<ONEZ
$(document).off('mouseup').on('mouseup',function(e){
  if($(e.target).closest('.popwin').length<1){
    $('.popwin').hide();
  }
});
$('.onez-dialog').off('focus blur','.input').on('focus','.input',function(e){
  $('.inputbox').addClass('focus');
}).on('blur','.input',function(e){
  $('.inputbox').removeClass('focus');
});
$('.onez-dialog').off('click','.inputbox').on('click','.inputbox',function(e){
  $('.input').focus();
});
$('.onez-dialog').closest('.layui-layer-page').addClass('keep-background');
$('.onez-dialog').off('click','[data-toolbar]').on('click','[data-toolbar]',function(e){
  var toolbar=$(this).attr('data-toolbar');
  console.log(toolbar);
  e.stopPropagation();
});
$('.onez-dialog').off('keydown','.input').on('keydown','.input',function(e){
  if(e.keyCode ==13){
    if($(this).hasClass('enter-ctrl')){
      if(e.ctrlKey){
        e.preventDefault();
        e.returnValue=false;
        onez.onEvent({token:'send'});
      }
    }else{
      if(!e.ctrlKey){
        e.preventDefault();
        e.returnValue=false;
        onez.onEvent({token:'send'});
      }
    }
  }
});
$(document).contextmenu(function(e){
  if($(e.target).get(0).tagName=='TEXTAREA'){
    return true;
  }
  $(".contextmenu").remove();
  var data=$(e.target).data();
  var obj=$(e.target);
  if(!data.menus){
    obj=$(e.target).closest('[data-menus]');
    data=obj.data();
  }
  if(!data){
    return true;
  }
  if(data.menus){
    var menus=[];
    if(data.menus=='msg'){
      menus.push({
        name:'删除聊天',
        token:'delmsg'
      });
    }else if(data.menus=='user'){
      menus.push({
        name:'删除好友',
        token:'delfriend'
      });
    }else if(data.menus=='message'){
      menus.push({
        name:'删除',
        token:'delmessage'
      });
    }
    if(menus.length>0){
      var ul=$('<ul class="contextmenu popwin onez-menu"></ul>');
      for(var i=0;i<menus.length;i++){
        var li=$('<li>'+menus[i].name+'</li>').appendTo(ul);
        if(menus[i].token){
          li.attr('token',menus[i].token);
          li.click(function(){
            $(".contextmenu").remove();
            var token=$(this).attr('token');
            console.log(token,data);
            if(token=='delmsg'){
              data.msggid=obj.attr('data-msggid');
              onez.loadData({
                action:'message?type=delete&msgGId='+data.msggid,
                onLoad:function(){
                  $(document).trigger('onez-update','msglist');
                  Vue.set(onez.dialogVue().data,'actionShowBox','messagelist&userid=0');
                }
              });
            }else if(token=='delfriend'){
              data.userid=obj.attr('data-userid');
              onez.confirm('确定要删除这个好友吗？',function(){
                onez.loadData({
                  action:'userlist?type=delete&userid='+data.userid,
                  onLoad:function(){
                    $(document).trigger('onez-update','userlist|msglist');
                  }
                });
              });
            }else if(token=='delmessage'){
              data.msgid=obj.attr('data-msgid');
              onez.loadData({
                action:'message?type=delete2&msgId='+data.msgid,
                onLoad:function(){
                  $('[data-msgid="'+data.msgid+'"]').closest('.row').remove();
                }
              });
            }
          });
        }
      }
      var evt={token:'show',pos:'mouse'};
      evt.el=ul.appendTo('body');
      onez.onEvent(evt);
    }
  }
  return false;
});
layui.upload.render({
  elem: '[data-toolbar="image"]'
  ,url: onez.info.upload_url
  ,accept: 'images'
  ,done: function(res){
    let msg = {thumb:res.thumb,url:res.url,w:res.width,h:res.height,scale:1}
		let maxW = 175;
		let maxH = 175;
		if(msg.w>maxW||msg.h>maxH){
			msg.scale = msg.w/msg.h;
			msg.w = msg.scale>1?maxW:maxH*msg.scale;
			msg.h = msg.scale>1?maxW/msg.scale:maxH;
      
		}
	  onez.sendMsg(msg,'img');
  }
});
ONEZ;
$json['less']=<<<ONEZ
.onez-dialog{
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  display:flex;
  height:100%;
  .sidebar{
    display:block;
    width:60px;
    background:#272a2d;
    height:100%;
    position: relative;
  }
  .list{
    display:block;
    width:250px;
    background:#e6e5e5;
    height:100%;
    position: relative;
  }
  .main{
    display:block;
    flex:1;
    height:100%;
    background:#f5f5f5;
    position: relative;
    .header{
      h2{
        font-size: 20px;
        font-weight: bold;
        margin: 20px 25px;
      }
      .rtIcon{
        position: absolute;
        right:10px;
        top:20px;
        .icon{
          font-size:32px;
          cursor:pointer;
          color:#bbb;
        }
      }
    }
  }
  .listBox{
    position: absolute;
    top:62px;
    left:0;
    bottom:0px;
    width:100%;
    height:auto;
    overflow:hidden;
    overflow-y:auto;
  }
  .onez-win-title{
    background:#f5f5f5;
    .header{
      border-bottom:1px solid #e7e7e7;
    }
  }
  .showbox{
    position: absolute;
    top:60px;
    bottom:140px;
    left:0;
    width:100%;
    overflow:hidden;
    overflow-y:auto;
    background:#f5f5f5;
    border-top:1px solid #e7e7e7;
  }
  .inputbox{
    border-top:1px solid #ececec;
    position: absolute;
    bottom:0px;
    left:0;
    width:100%;
    height:140px;
    background:#f5f5f5;
    &.focus{
      background:#fff;
    }
    .toolbar{
      padding:10px 0 6px 20px;
      .item-btn{
        cursor:pointer;
        font-size:22px;
        margin-right:15px;
        color:#6d6d6d;
        &:hover{
          color:#1a1a1a;
        }
      }
    }
    .input-border{
      position: absolute;
      bottom:45px;
      left:0;
      right:0;
      height:55px;
      display:flex;
      padding:0 8px;
      .input{
        background:transparent;
        border:0;
        width:100%;
        height:100%;
        flex:1;
        resize: none;
      }
    }
    .sendbtn{
      color:#606060;
      position: absolute;
      right:20px;
      border:1px solid #e5e5e5;
      background:#f5f5f5;
      bottom:10px;
      padding:5px 10px;
      &:hover{
        color:#fff;
        background:#129611;
        border:1px solid #129611;
      }
    }
  }
}
ONEZ;
