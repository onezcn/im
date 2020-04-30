<?php

/* ========================================================================
 * $Id: index.php 348 2020-04-30 16:39:45Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$A['title']='即时通讯';

#必须登录
if(!$G['userid']){
  $record[]=array(
    'type'=>'login',
  );
  return;
}

$A['token']=$G['this']->getEvent(array(
  'token'=>'page',
  'target'=>'win',
  'width'=>'850',
  'height'=>'590',
  'noTitle'=>'1',
  'noClose'=>'1',
  'action'=>'dialog',
));