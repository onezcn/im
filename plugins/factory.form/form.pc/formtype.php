<?php

/* ========================================================================
 * $Id: formtype.php 283 2020-04-12 06:03:02Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$arr['options']=$this->formtypes();
if($MODE=='form'){
  $item['type']='onez-picker';
  return include(dirname(__FILE__).'/select.php');
}elseif($MODE=='show'){
  return $arr['options'][$item['value']];
}elseif($MODE=='post'){
  
}