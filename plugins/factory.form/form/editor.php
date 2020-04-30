<?php

/* ========================================================================
 * $Id: editor.php 285 2020-03-28 00:06:56Z onez $
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
  $item['type']='editor';
  $item['showImgToolbar']='1';
  $item['showImgResize']='1';
  $itemBox['itemstyle']='padding:5px';
  $items[]=$item;
}elseif($MODE=='post'){
  $onez[$arr['key']]=$_REQUEST[$arr['key']];
}