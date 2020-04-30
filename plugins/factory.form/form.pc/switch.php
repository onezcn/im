<?php

/* ========================================================================
 * $Id: switch.php 551 2020-04-12 15:24:11Z onez $
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
  $label=$arr['label'];
  unset($arr['label']);
  return '<label style="font-weight:normal"><input style="position: relative;top: 2px;" type="checkbox"'.($item['value']?' checked':'').' id="input-'.$key.'" name="'.$key.'" > '.$label.'</label>';
}elseif($MODE=='show'){
  return $item['value']?'<span style="color:green">是</span>':'<span style="color:red">否</span>';
}elseif($MODE=='post'){
  $onez[$arr['key']]=($_REQUEST[$arr['key']]=='false'||!$_REQUEST[$arr['key']])?0:1;
}