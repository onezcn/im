<?php

/* ========================================================================
 * $Id: image.php 301 2020-03-28 10:46:54Z onez $
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
  $item['type']='onez-tui-upload';
  $item['name']=$item['key'];
  $item['limit']=(int)$arr['num'];
  $item['serverUrl']=$this->url.'/php/fileupload.php?sitetoken='.$G['this']->token;
  $items[]=$item;
}elseif($MODE=='post'){
  
}