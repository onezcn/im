<?php

/* ========================================================================
 * $Id: image.php 361 2020-04-25 14:50:58Z onez $
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
  $_arr=$arr;
  $_arr['label']=' ';
  $_arr['value']=$item['value'];
  return onez('form.file')->form_code($_arr);
}elseif($MODE=='show'){
  return '<img src="'.$item['value'].'" style="max-height:128px;">';
}elseif($MODE=='post'){
  onez('form.file')->form_save($onez[$arr['key']],$arr);
}