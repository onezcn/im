<?php

/* ========================================================================
 * $Id: fonticon.php 871 2020-04-12 06:02:51Z onez $
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
  $item['type']='fonticon';
  $item['key'].='_preview';
  $values[$arr['key'].'_preview'] && $item['value']=$values[$arr['key'].'_preview'];
  $items[]=$item;
  $items[]=$this->hidden($arr['key'],$item['value']);
}elseif($MODE=='show'){
  return $item['value'];
}elseif($MODE=='post'){
  $onez[$arr['key'].'_color']=onez()->gp($arr['key'].'_color');
  $onez[$arr['key'].'_preview']=onez()->gp($arr['key'].'_preview');
  list($type,$name)=explode('|',$onez[$arr['key'].'_preview']);
  if($type&&$name){
    $onez[$arr['key']]="$type|$name|auto|".($onez[$arr['key'].'_color']?$onez[$arr['key'].'_color']:'');
    $onez[$arr['key'].'_preview']="$type|$name|64|".($onez[$arr['key'].'_color']?$onez[$arr['key'].'_color']:'');
  }else{
    $onez[$arr['key']]='';
    $onez[$arr['key'].'_preview']='';
  }
}