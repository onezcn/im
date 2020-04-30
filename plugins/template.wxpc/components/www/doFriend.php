<?php

/* ========================================================================
 * $Id: doFriend.php 726 2020-04-28 09:35:24Z onez $
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
<div class="row">
  <div class="col-xs-4 text-center">
    <img :src="data.avatar" :data-token="data.token" class="item-avatar" />
    <p>{{data.nickname}}</p>
  </div>
  <div class="col-xs-8">
    <onez type="form" :data="data.form"></onez>
  </div>
</div>
ONEZ;
$json['less']=<<<ONEZ
.comp-doFriend{
  padding:20px;
  .item-avatar{
    width: 100px;
    height: 100px;
    margin-bottom: 10px;
    border-radius: 100%;
  }
  form{
    padding:0;
    .col-xs-12{
      &:last-child{
        text-align:right;
      }
    }
    button[type="submit"] {
      display: inline-block;
      margin: 2px 0 2px 10px;
      min-width: auto;
    }
  }
}
ONEZ;
