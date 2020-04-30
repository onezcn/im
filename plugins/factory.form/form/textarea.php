<?php

/* ========================================================================
 * $Id: textarea.php 180 2020-03-28 09:30:35Z onez $
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
  $item['type']='onez-textarea';
  $item['name']=$item['key'];
  $items[]=$item;
}elseif($MODE=='post'){
  
}