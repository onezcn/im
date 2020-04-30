<?php

/* ========================================================================
 * $Id: new.php 578 2016-11-14 01:28:36Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_new extends onezphp_admin_widgets{
  var $attrs=array();
  function __construct(){
    
  }
  function attr($key,$value){
    $this->attrs[$key]=$value;
    return $this;
  }
  function code(){
    $name=$this->get('tag');
    if(!$name){
      $name='div';
    }
    $A='<'.$name;
    foreach($this->attrs as $k=>$v){
      $A.=' '.$k.'="'.$v.'"';
    }
    $A.='>';
    $B='</'.$name.'>';
    if($this->code){
      return $A.$this->code.$B;
    }else{
      return $A.$this->html.$B;
    }
  }
}