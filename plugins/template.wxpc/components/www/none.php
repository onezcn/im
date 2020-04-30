<?php

/* ========================================================================
 * $Id: none.php 328 2020-04-28 12:52:54Z onez $
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
<div class="widget-none">
  <div class="text" v-html="data.text"></div>
</div>
ONEZ;
$json['less']=<<<ONEZ
.widget-none{
  text-align:center;
  .text{
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    margin-top: -10px;
  }
}
ONEZ;
