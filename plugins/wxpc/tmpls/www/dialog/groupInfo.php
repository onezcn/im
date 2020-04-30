<?php

/* ========================================================================
 * $Id: groupInfo.php 1307 2020-04-30 20:11:02Z onez $
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
$groupId=(int)onez()->gp('groupId');
$type=onez()->gp('type');
if($type=='quit'){
  $A['update']='msglist|userlist';
  $A['token']='toHome';
  onez('factory.im')->delGroupUsers($groupId,$G['userid']);
  return;
}
$A['title']='群资料';
$type=onez()->gp('type');
$group=onez('factory.im')->group($groupId);
$A['type']='group';
$users=onez('factory.im')->groupUsers($groupId);
$A['users']=array();
$A['sendToken']=$G['this']->getEvent(array(
  'token'=>'sendTo',
  'userid'=>'group.'.$groupId,
  'uname'=>$group['name'],
));
foreach($users as $rs){
  $rs['token']=$G['this']->getEvent(array(
    'token'=>'userCard',
    'userid'=>$rs['userid'],
  ));
  $A['users'][]=$rs;
}
$ispage=onez()->gp('ispage');
if($ispage){
  $record[]=array(
    'type'=>'groupInfo',
    'groupid'=>$groupId,
    'users'=>$A['users'],
    'addToken'=>$G['this']->getEvent(array(
      'token'=>'page',
      'target'=>'win',
      'width'=>'760',
      'height'=>'500',
      'action'=>'createChat&groupId='.$groupId,
      'title'=>'邀请好友',
    )),
  );
  unset($A['sendToken']);
  unset($A['users']);
}