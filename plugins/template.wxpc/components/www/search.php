<?php

/* ========================================================================
 * $Id: search.php 2333 2020-04-29 23:25:45Z onez $
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
<div class="search">
  <div class="search-border">
    <div class="search-box">
      <i class="ico layui-icon layui-icon-search"></i>
      <input class="kw" type="text" name="title" placeholder="搜索" autocomplete="off">
      <i class="cancel layui-icon layui-icon-close-fill"></i>
    </div>
    <div class="add" data-token="show" data-el=".menu-add" data-box=".onez-dialog">
      <i class="layui-icon layui-icon-add-1"></i>
    </div>
  </div>
  <ul class="popwin onez-menu menu-add">
    <li data-token="page" data-action="addFriend" data-target="win" data-width="300" data-height="200">添加好友</li>
    <li data-token="page" data-action="createChat" data-target="win" data-width="760" data-height="500">创建群聊</li>
  </ul>
</div>
ONEZ;
$json['js']=<<<ONEZ
$('.kw').bind('focus',function(){
  $('.search').addClass('focus');
}).bind('blur',function(){
  $('.search').removeClass('focus');
});
$('.cancel').bind('click',function(){
  $('.kw').val('').blur();
});
ONEZ;
$json['less']=<<<ONEZ
.comp-search{
  border-bottom:1px solid #ddd;
  padding-bottom:4px;
}
.search{
  margin:20px 10px 5px 10px;
  text-align:center;
  .search-border{
    display:flex;
    .search-box{
      flex:1;
      display:flex;
      border-radius:6px;
      background:#dad8d8;
      border:1px solid #dad8d8;
      padding:0 5px;
      .ico{
        display:block;
        margin:7px 2px 0 5px;
      }
      .ico{
        color:#656565;
      }
      input{
        border:0;
        width:100px;
        background:transparent;
        display:block;
        flex:1;
        height:28px;
        line-height:28px;
        text-align:left;
        position: relative;
        top: 1px;
      }
      .cancel{
        border-radius:50%;
        color:#cfcfcf;
        font-size:20px;
        display:block;
        margin:5px 2px 0 5px;
        cursor:pointer;
      }
    }
    .add{
      background:#dcd9d8;
      border-radius:6px;
      color:#585858;
      width:32px;
      height:32px;
      display:block;
      margin:0 3px;
      cursor:pointer;
      i{
        font-size:20px;
        display:block;
        margin-top:3px;
      }
    }
  }
  &.focus{
    .search-box{
      background:#f6f6f5;
      border:1px solid #dad8d7;
    }
  }
}
ONEZ;
