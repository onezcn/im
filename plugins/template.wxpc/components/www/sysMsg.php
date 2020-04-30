<?php

/* ========================================================================
 * $Id: sysMsg.php 1931 2020-04-30 18:24:28Z onez $
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
<!--好友申请-->
<template v-if="data.msgtype=='request'" >
<div class="item-request">
  <img :src="data.avatar" class="item-avatar">
  <p class="item-user">
    <span class="item-name">{{data.name}}</span>
    <span class="item-time">{{onez.time_str(data.time)}}</span>
  </p>
  <p class="item-content">
    申请添加你为好友
    <span>附言: {{data.requestCo}}</span>
  </p>
  <p class="item-btns" v-if="!data.doTime">
    <button class="layui-btn layui-btn-small" data-token="ajax" data-type="agree" :data-msgid="data.msgid">同意</button>
    <button class="layui-btn layui-btn-small layui-btn-primary" data-token="ajax" data-type="refuse" :data-msgid="data.msgid">拒绝</button>
  </p>
  <p class="item-btns" v-else>
    <span>{{data.statusName}}</span>
  </p>
</div>
</template>
<template v-else>
<div class="item-system">
 <p>
   <em>系统：</em>{{data.summary}}<span>{{onez.time_str(data.time)}}</span>
 </p>
</div>
</template>
ONEZ;
$json['less']=<<<ONEZ
.comp-sysMsg{
  position: relative;
  margin: 10px 15px 0 15px;
  line-height: 22px;
  border-bottom: 1px dotted #e2e2e2;
}
.item-system{
  margin: 10px 10px 10px 10px;
  em{
    font-style: normal;
    color: #FF5722;
  }
  span {
    padding-left: 5px;
    color: #999;
  }
}
.item-request{
  padding: 0 130px 10px 60px;
  position: relative;
  .item-avatar{
    position: absolute;
    left: 0;
    top: 0;
    width: 50px;
    height: 50px;
  }
  .item-user{
    padding-top: 5px;
    .item-name{
      color: #333;
    }
    .item-time{
      padding-left: 5px;
      color: #999;
    }
  }
  .item-content{
    margin-top: 3px;
    span{
      padding-left: 5px;
      color: #999;
    }
  }
  .item-btns{
    position: absolute;
    right: 0;
    top: 12px;
    color: #999;
    span{
      color:#999;
      margin-top:6px;
      display:inline-block;
    }
  }
}
ONEZ;
