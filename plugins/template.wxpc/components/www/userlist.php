<?php

/* ========================================================================
 * $Id: userlist.php 977 2020-04-30 14:37:16Z onez $
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
<div class="widget-userlist" :data-token="data.token">
  <img :src="data.avatar" class="item-avatar" />
  <div class="item-nickname">
    {{data.nickname}}
  </div>
  <div class="item-sign">
    {{data.sign}}
  </div>
</div>
ONEZ;
$json['less']=<<<ONEZ
.comp-userlist{
  display:inline-block;
  width:33.333333%;
}
.widget-userlist{
  position:relative;
  left:0px;
  top:0px;
  padding:5px 5px 5px 50px;
  background:#fff;
  border-right:1px solid #f4f4f4;
  border-bottom:1px solid #f4f4f4;
  cursor:pointer;
  &:hover{
    background: #e2fce2;
  }
  .item-avatar{
    position:absolute;
    left:5px;
    top:4px;
    width:40px;
    height:40px;
    background:#f4f4f4;
    border-radius:3px;
  }
  .item-nickname{
    height:20px;
    line-height:20px;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
    word-break:break-all;
  }
  .item-sign{
    color:#999;
  }
}
ONEZ;
