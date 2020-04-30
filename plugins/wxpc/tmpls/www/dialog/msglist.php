<?php

/* ========================================================================
 * $Id: msglist.php 726 2020-04-30 17:14:12Z onez $
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
#左侧消息列表
$A['title']='消息列表';
$T=onez('factory.im')->readMessageGroupList();
$A['hasMore']=count($T)>=$G['pagesize'];
foreach($T as $rs){
  $rs['userid']=$rs['gToken'];
  $event=array(
    'token'=>'sendTo',
    'userid'=>$rs['gToken'],
    'uname'=>$rs['subject'],
  );
  if($rs['gToken']=='system'){
    $event=array(
      'token'=>'page',
      'target'=>'win',
      'action'=>'sysmsg',
    );
  }
  $event && $rs['token']=$rs['token']=$G['this']->getEvent($event);
  $record[]=$rs;
}
