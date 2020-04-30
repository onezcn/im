<?php

/* ========================================================================
 * $Id: button.php 928 2016-11-14 01:28:36Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_button extends onezphp_admin_widgets{
  function __construct(){
    
  }
  function code(){
    $type=$this->get('type','button');
    $class=$this->get('class');
    $style=$this->get('style');
    !$style && $style='btn-success';
    $name=$this->get('name','确定');
    
    $color=$this->get('color');
    if($color=='blue'){
      $style='btn-info';
    }elseif($color=='red'){
      $style='btn-danger';
    }elseif($color=='green'){
      $style='btn-success';
    }
    
    $event=$this->get('event');
    if($event){
      $attrs.=' onclick="'.$event.'"';
    }
    $myattrs=$this->get('attrs');
    if($myattrs){
      foreach($myattrs as $k=>$v){
        $attrs.=' '.$k.'="'.$v.'"';
      }
    }
    $this->html.='<button type="'.$type.'" class="btn '.$style.' '.$class.'"'.$attrs.'>'.$name.'</button>';
    return $this->html;
  }
}