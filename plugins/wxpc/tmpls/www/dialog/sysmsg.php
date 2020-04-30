<?php

/* ========================================================================
 * $Id: sysmsg.php 1680 2020-04-30 20:20:16Z onez $
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
$type=onez()->gp('type');
if($type=='agree'){
  $msgid=(int)onez()->gp('msgid');
  $r=onez('factory.im')->readMessage($msgid);
  if($r){
    $friendId=$r['$extra']['fromUserId'];
    onez('factory.im')->addMessageSystem($friendId,$G['this']->user($G['userid'],'nickname').' 已经同意你的好友申请');
    
    onez('factory.im')->setMessageExtra($r,array(
      'doTime'=>time(),
      'status'=>'agree',
      'statusName'=>'已同意',
    ));
    $extra=array(
      'add-from'=>$friendId,
      'add-type'=>'request',
    );
    onez('factory.im')->addFriend($G['userid'],$friendId,$extra);
    onez('factory.im')->addFriend($friendId,$G['userid'],$extra);
    $A['goto']='reload';
    return;
  }
  $A['error']='未知错误';
  return;
}elseif($type=='refuse'){
  $msgid=(int)onez()->gp('msgid');
  $r=onez('factory.im')->readMessage($msgid);
  if($r){
    $friendId=$r['$extra']['fromUserId'];
    onez('factory.im')->addMessageSystem($friendId,$G['this']->user($G['userid'],'nickname').' 拒绝了你的好友申请');
    
    onez('factory.im')->setMessageExtra($r,array(
      'doTime'=>time(),
      'status'=>'refuse',
      'statusName'=>'已拒绝',
    ));
    $A['goto']='reload';
    return;
  }
  $A['error']='未知错误';
  return;
}
$A['title']='系统消息';
$T=onez('factory.im')->readMessageList('system');
$A['hasMore']=$G['pagesize']>0 && count($T)>=$G['pagesize'];
$T=array_reverse($T);
foreach($T as $rs){
  $record[]=$rs;
}