<?php

/* ========================================================================
 * $Id: sidebar.php 2502 2020-04-30 15:50:41Z onez $
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
<div class="widget-5eca4b">  <!-- 用户开始 -->
  <div class="item-user">
    <!-- 头像开始 -->
    <img :src="data.user.avatar" :data-token="data.user.token" class="item-avatar" />
    <!-- 头像结束 -->
  </div>
  <!-- 用户结束 -->
  <!-- 主菜单开始 -->
  <div class="item-menus">
    <template v-for="item in data.menus">
      <!-- 图标开始 -->
      <div class="item-icon selected" v-if="onez.dialogVue().data.status==item.status" v-html="item.icon" :data-token="item.token"></div>
      <div class="item-icon" v-else v-html="item.icon2" :data-token="item.token"></div>
      <!-- 图标结束 -->
    </template>
  </div>
  <!-- 主菜单结束 -->
  <!-- 底部菜单开始 -->
  <div class="bottom item-setting">
    <template v-for="item in data.setting">
      <!-- 图标开始 -->
      <div class="item-icon" v-html="item.icon" :data-token="item.token"></div>
      <!-- 图标结束 -->
    </template>
  </div>
  <!-- 底部菜单结束 -->
  <ul class="popwin onez-menu menu-setting">
    <li data-token="page" data-action="setting" data-target="win" data-title="设置" data-width="547" data-height="474">设置</li>
    <li data-token="logout">退出登录</li>
  </ul>
</div>
ONEZ;
$json['js']=<<<ONEZ
$('.item-avatar').bind('click',function(){
  $('.usercard').show();
});
ONEZ;
$json['less']=<<<ONEZ
.widget-5eca4b{
  width:100%;
  text-align:center;
  padding-top:15px;
  .item-user{
    margin:5px auto;
    .item-avatar{
      width:32px;
      height:32px;
      background:#999;
      cursor:pointer;
    }
  }
  .item-icon{
    padding: 10px 0;
    display:block;
    overflow:hidden;
    cursor:pointer;
    svg{
      width:22px!important;
      height:22px!important;
      transform: translateX(-80px);
      filter: drop-shadow(#8c8c8c 80px 0);
    }
    &.selected{
      svg{
        filter: drop-shadow(#07c160 80px 0)!important;
      }
    }
    &:hover{
      svg{
        filter: drop-shadow(#e3e3e3 80px 0);
      }
    }
  }
  .item-menus{
    margin-top:30px;
  }
  .bottom{
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
  }
  .item-setting{
    
  }
  .onez-menu{
    display:none;
    position: absolute;
    bottom: 10px;
    left: 100%;
    width:100px;
    background:#27292c;
    border:0;
    li{
      padding:10px 5px;
      cursor:pointer;
      color:#8c8c8c;
      &:hover{
        background:#303134;
      }
    }
  }
}
ONEZ;
