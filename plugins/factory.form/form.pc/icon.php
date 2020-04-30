<?php

/* ========================================================================
 * $Id: icon.php 445 2020-04-12 06:03:44Z onez $
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
  $arr['value']=$item['value'];
  $arr['cloudname']='云图标';
  $arr['cloud']='//www.onez.cn/?_p=icon&key='.$arr['key'].'&size=128&ext=svg&kw=';
  return onez('form.file')->form_code($arr);
}elseif($MODE=='show'){
  return '<img src="'.$item['value'].'" style="width:64px;height:64px;">';
}elseif($MODE=='post'){
  onez('form.file')->form_save($onez[$arr['key']],$arr);
}