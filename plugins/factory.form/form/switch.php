<?php

/* ========================================================================
 * $Id: switch.php 322 2020-03-29 23:29:23Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
if($MODE=='form'){
  $itemBox=$item;
  $itemBox['label']=$arr['label'];
  $itemBox['type']='switch';
  $itemBox['label_width']='width:auto';
  #$items[]=$item;
}elseif($MODE=='post'){
  $onez[$arr['key']]=($_REQUEST[$arr['key']]=='false'||!$_REQUEST[$arr['key']])?0:1;
}