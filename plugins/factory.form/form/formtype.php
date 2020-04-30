<?php

/* ========================================================================
 * $Id: formtype.php 201 2020-04-06 19:38:45Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$options=$this->formtypes();
if($MODE=='form'){
  $item['type']='onez-picker';
}elseif($MODE=='post'){
  
}
include(dirname(__FILE__).'/select.php');