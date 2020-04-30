<?php

/* ========================================================================
 * $Id: textarea.php 333 2020-04-12 06:05:35Z onez $
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
  return '<textarea class="form-control" id="input-'.$key.'" name="'.$key.'" placeholder="'.$item['hint'].'" autocomplete="off" style="min-height:100px">'.$item['value'].'</textarea>';
}elseif($MODE=='show'){
  return $item['value'];
}elseif($MODE=='post'){
  
}