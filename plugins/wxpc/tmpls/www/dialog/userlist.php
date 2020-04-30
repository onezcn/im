<?php

/* ========================================================================
 * $Id: userlist.php 1611 2020-04-30 18:27:09Z onez $
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
$type=onez()->gp('type');
if($type=='delete'){
  $userid=onez()->gp('userid');
  onez('factory.im')->delFriend($userid);
  return;
}
$A['title']='好友列表';
$friendList=onez('factory.im')->friendList();

$record[]=array(
  'type'=>'event',
  'event'=>'userListUpdate',
);
#群聊
$groupList=onez('factory.im')->groupList();
if($groupList){
  $record[]=array(
    'type'=>'label',
    'subject'=>'群聊',
  );
  foreach($groupList as $rs){
    #所有群聊
    $A['groupIds'][]=$rs['groupId'];
    $record[]=array(
      'type'=>'user',
      'icon'=>$rs['icon'],
      'subject'=>$rs['name'],
      'userid'=>'group.'.$rs['groupId'],
      'token'=>$G['this']->getEvent(array(
        'token'=>'groupInfo',
        'gid'=>$rs['groupId'],
      )),
    );
  }
}


$words=array();
foreach($friendList as $rs){
  $nickname=$G['this']->user($rs['friendId'],'nickname');
  $py=onez('pinyin')->py($nickname);
  $py=$py?ucfirst($py):'#';
  $words[$py][]=array(
    'type'=>'user',
    'icon'=>$G['this']->avatar($rs['friendId']),
    'subject'=>$nickname,
    'userid'=>'dialog.'.$rs['friendId'],
    'token'=>$G['this']->getEvent(array(
      'token'=>'userInfo',
      'userid'=>$rs['friendId'],
    )),
  );
}
ksort($words);
foreach($words as $py=>$list){
  $record[]=array(
    'type'=>'label',
    'subject'=>$py,
  );
  foreach($list as $rs){
    $record[]=$rs;
  }
}
