<?php

/* ========================================================================
 * $Id: select.php 455 2020-04-12 06:04:50Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$options=$arr['options'];
if($MODE=='form'){
  $_code='';
  $_code.='<select class="form-control" id="input-'.$key.'" name="'.$key.'">';
  foreach($options as $k=>$v){
    $s=($k==$item['value']?' selected':'');
    $_code.='<option value="'.$k.'"'.$s.'>'.$v.'</option>';
  }
  $_code.='</select>';
  return $_code;
}elseif($MODE=='show'){
  return $options[$item['value']];
}elseif($MODE=='post'){
  
}