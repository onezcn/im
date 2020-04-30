<?php

/* ========================================================================
 * $Id: list.php 3122 2020-04-30 15:51:38Z onez $
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
<template v-for="item in data.record">
  <!--消息列表-->
  <template v-if="item.type=='msg'">
    <div class="item-box list-type-msg" :class="{focus:onez.toUserId==item.userid}" data-menus="msg" :data-msgGId="item.msgGId" :data-token="item.token" :data-userid="item.userid">
      <img :src="item.icon" class="item-icon" />
      <div class="item-subject">
        {{item.subject}}
      </div>
      <div class="item-summary">
        {{item.summary}}
      </div>
      <div class="item-time">
        {{onez.time_str(item.time)}}
      </div>
      <template v-if="item.num && item.num!='0'">
        <div v-if="item.num=='dot'" class="item-num item-num-dot"></div>
        <div v-if="item.num" class="item-num">{{item.num}}</div>
      </template>
    </div>
  </template>
  <!--用户列表-->
  <template v-else-if="item.type=='user'">
    <div class="item-box list-type-user" data-menus="user" :data-userid="item.userid" :data-token="item.token">
      <img :src="item.icon" class="item-icon" />
      <div class="item-subject">
        {{item.subject}}
      </div>
      <template v-if="item.num && item.num!='0'">
        <div v-if="item.num=='dot'" class="item-num item-num-dot"></div>
        <div v-if="item.num" class="item-num">{{item.num}}</div>
      </template>
    </div>
  </template>
  <!--标签-->
  <template v-else-if="item.type=='label'">
    <div class="item-label">
      {{item.subject}}
    </div>
  </template>
  <!--事件-->
  <template v-else-if="item.type=='event'">
    <span v-html="onez.appEvent(item.event)"></span>
  </template>
</template>
ONEZ;

$json['less']=<<<ONEZ
.comp-list{
  .item-box{
    position:relative;
    left:0px;
    top:0px;
    padding:6px 6px 6px 52px;
    cursor:pointer;
    &:hover{
      background:#d0d0d0;
    }
    &:focus,&.focus{
      background:#c6c6c6;
    }
  }
  .item-num{
    position: absolute;
    display: inline-block;
    padding: 0 3px;
    font-size: 12px;
    text-align: center;
    background-color: #FF5722;
    color: #fff;
    border-radius: 8px;
    margin-left:-3px;
  }
  .item-num-dot{
    width: 8px;
    height: 8px;
    padding: 0;
    border-radius: 50%;
    text-indent: -999em;
  }
  .item-icon{
    position:absolute;
    left:6px;
    top:6px;
    width:40px;
    height:40px;
  }
  .item-subject{
    height:20px;
    line-height:20px;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
    word-break:break-all;
  }
  .item-summary{
    font-size:12px;
    color:#999;
    height:20px;
    line-height:20px;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
    word-break:break-all;
  }
  .item-time{
    position:absolute;
    right:5px;
    top:6px;
    font-size:12px;
    color:#999;
  }
  .item-label{
    margin: 15px 6px 5px;
  }
  .list-type-msg{
    .item-num{
      left:38px;
      top:0px;
    }
  }
  .list-type-user{
    padding-left:45px;
    .item-icon{
      width:32px;
      height:32px;
    }
    .item-subject{
      height:32px;
      line-height:32px;
    }
  }
}
ONEZ;
