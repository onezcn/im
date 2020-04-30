<?php

/* ========================================================================
 * $Id: editor.php 362 2020-04-13 05:11:26Z onez $
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
  $_html=onez('kindeditor')->form_code($item);
  $_html=str_replace('<div class="form-group"><label for="input-'.$key.'" />','<div>',$_html);
  return $_html;
}elseif($MODE=='show'){
  return $item['value'];
}elseif($MODE=='post'){
  onez('kindeditor')->form_save($onez[$arr['key']],$arr);
}