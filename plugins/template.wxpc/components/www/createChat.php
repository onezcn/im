<?php

/* ========================================================================
 * $Id: createChat.php 4098 2020-04-30 11:01:40Z onez $
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
<div class="left">
<template v-for="item in data.users">
  <template v-if="item.type=='label'">
    <div class="item-label">
      {{item.subject}}
    </div>
  </template>
  <template v-else-if="item.type=='user'">
    <div class="item-user" :data-userid="item.userid">
      <img :src="item.avatar" class="item-avatar" />
      <div class="item-subject">
        {{item.subject}}
      </div>
      <div class="item-rightinfo">
        <nut-checkbox :label="item.userid" v-model="item.checked"></nut-checkbox>
      </div>
    </div>
  </template>
</template>
</div>
<div class="right">
<p class="caption">请勾选需要添加的联系人</p>
<div class="selBox">
<template v-for="item in data.users">
  <template v-if="item.type=='user' && item.checked">
    <div class="item-user">
      <img :src="item.avatar" class="item-avatar" />
      <div class="item-subject">
        {{item.subject}}
      </div>
      <div class="item-rightinfo">
        <nut-checkbox v-model="item.checked">
          <i class="layui-icon layui-icon-close"></i>
        </nut-checkbox>
      </div>
    </div>
  </template>
</template>
</div>
<div class="btns">
  <button type="button" class="btn btn-submit">确定</button>
  <button type="button" class="btn btn-default btn-close">关闭</button>
</div>
</div>
ONEZ;
$json['js']=<<<ONEZ
$('.comp-createChat .left .item-user').bind('click',function(e){
  if($(e.target).get(0).tagName=='INPUT'){
    return;
  }
  $(this).find('input').trigger('click');
});
$('.comp-createChat .btn-submit').bind('click',function(e){
  var page=$(this).page();
  var users=page.data.record[0].users;
  var uids=[];
  for(var i=0;i<users.length;i++){
    if(users[i].type=='user'){
      if(users[i].checked){
        uids.push(users[i].userid);
      }
    }
  }
  if(uids.length<1){
    onez.alert('请勾选需要添加的联系人');
    return;
  }
  onez.loadData({
    action:page.action,
    postdata:{type:'create',uids:uids.join(',')},
    onLoad:function(data){
      onez.doit(data);
      $(document).trigger('onez-update','msglist|userlist');
    }
  });
});
$('.comp-createChat .btn-close').bind('click',function(e){
  $(this).close();
});
ONEZ;
$json['less']=<<<ONEZ
.comp-createChat{
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  display:flex;
  .item-label{
    height:32px;
    line-height:32px;
    padding:0px 15px;
  }
  .item-user{
    position: relative;
    padding:8px 15px;
    display:flex;
    &:hover{
      background:#d0d0d0;
    }
    &:focus,&.focus{
      background:#c6c6c6;
    }
    .item-avatar{
      display:block;
      width:40px;
      height:40px;
    }
    .item-subject{
      flex:1;
      display:block;
      height:40px;
      line-height:40px;
      white-space:nowrap;
      text-overflow:ellipsis;
      overflow:hidden;
      word-break:break-all;
      padding-left:6px;
    }
    .item-rightinfo{
      width:40px;
      text-align:right;
      padding-top:5px;
      .nut-checkbox.nut-checkbox-size-base input{
        width:32px;
        height:32px;
        border-radius:50%;
      }
    }
  }
  .left{
    flex:6;
    border-right:1px solid #f4f4f4;
    height:100%;
    .nut-checkbox-label{
      display:none;
    }
  }
  .right{
    position: relative;
    flex:5;
    .item-rightinfo{
      input{
        display:none;
      }
    }
    .nut-checkbox{
      cursor:pointer;
      .nut-checkbox-label{
        .layui-icon{
          display: inline-block;
          text-align:center;
          font-size:22px;
          color:#999;
          margin-top:3px;
          cursor:pointer;
        }
      }
    }
    .caption{
      font-size:16px;
      padding:10px;
    }
    .selBox{
      position: absolute;
      left:0;
      top:50px;
      bottom:50px;
      width:100%;
      overflow:hidden;
      overflow-y:auto;
    }
    .btns{
      position: absolute;
      bottom:0;
      right:0;
      text-align:right;
      padding:15px 10px;
      .btn{
        margin-left:10px;
      }
    }
  }
}
ONEZ;
