<?php

/* ========================================================================
 * $Id: number.php 317 2020-04-12 06:04:17Z onez $
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
  return '<input type="number" class="form-control" id="input-'.$key.'" name="'.$key.'" placeholder="'.$item['hint'].'" autocomplete="off" value="'.$item['value'].'">';
}elseif($MODE=='show'){
  return $item['value'];
}elseif($MODE=='post'){
  
}