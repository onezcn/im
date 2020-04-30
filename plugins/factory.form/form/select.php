<?php

/* ========================================================================
 * $Id: select.php 442 2020-04-06 19:41:26Z onez $
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
  $item['type']='onez-picker';
  #$item['classname']='itemline-none';
  #$item['itemstyle']='padding:0;';
  $item['mode']='selector';
  $item['options']=array_values($options);
  $item['value']=(int)array_search($item['value'],array_keys($options));
  $items[]=$item;
}elseif($MODE=='post'){
  $keys=array_keys($options);
  $onez[$arr['key']]=$keys[$onez[$arr['key']]];
}