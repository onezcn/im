<?php

/* ========================================================================
 * $Id: welcome.php 608 2020-04-26 19:22:32Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#----------模板加载模式开始--------------
if($MODE=='TEMPLATE'){
  $TPL['records']=false;
  return $MODE;
}
#----------模板加载模式结束--------------
#----------模板加载模式开始--------------
if($G['userid']){
  $color1='#12b7f5';
  $color2='#d0d0d0';
  $navs=array();
  $A['navs']=$navs;
  $A['refreshIcon']=$G['this']->url.'/images/loading.gif';
  $A['refreshIconNormal']=$G['this']->url.'/images/loading.png';
  $A['refreshBoxBackground']='#fafafa';
}else{
  $A['navs']=array();
  include_once(dirname(__FILE__).'/login.php');
}
