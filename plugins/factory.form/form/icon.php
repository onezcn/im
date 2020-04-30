<?php

/* ========================================================================
 * $Id: icon.php 333 2020-03-28 10:48:42Z onez $
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
  $item['isIcon']=true;
  $item['byRes']=true;
  $item['name']=$item['key'];
  $item['limit']=1;
  $item['serverUrl']=$this->url.'/php/fileupload.php?sitetoken='.$G['this']->token;
  $items[]=$item;
}elseif($MODE=='post'){
  
}