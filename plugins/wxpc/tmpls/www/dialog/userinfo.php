<?php

/* ========================================================================
 * $Id: userinfo.php 465 2020-04-30 11:44:23Z onez $
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
$userid=(int)onez()->gp('userid');
$A['title']='用户资料';
$type=onez()->gp('type');
if($type=='detail'){#详情
  $A=$G['this']->userCard($userid,'detail');
  $A['type']='user';
}else{#默认卡片
  $record[]=$G['this']->userCard($userid);
}