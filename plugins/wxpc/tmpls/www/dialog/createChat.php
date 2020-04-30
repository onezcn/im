<?php

/* ========================================================================
 * $Id: createChat.php 1689 2020-04-30 19:51:55Z onez $
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
$oldUids=array();
$groupId=(int)onez()->gp('groupId');
if($groupId){
  $users=onez('factory.im')->groupUsers($groupId);
  foreach($users as $rs){
    $oldUids[]=$rs['userid'];
  }
}


$userid=(int)onez()->gp('userid');
$A['title']='创建群聊';
$type=onez()->gp('type');
if($type=='create'){
  $uids=onez()->gp('uids');
  ob_clean();
  if($groupId){#追加
    $A['message']='添加成功';
    onez('factory.im')->addGroupUsers($groupId,$uids);
    $A['goto']='reload2';
  }else{
    $A['message']='创建成功';
    $uname='群聊'.date('YmdHis');
    $gid=onez('factory.im')->addGroup($uname,$uids);
    $userid='group.'.$gid;
    $A['token']=$G['this']->getEvent(array(
      'token'=>'sendTo',
      'userid'=>$userid,
      'uname'=>$uname,
    ));
  }
  return;
}

$T=onez('factory.im')->friendList();
$words=array();
foreach($T as $rs){
  if(in_array($rs['friendId'],$oldUids)){
    continue;
  }
  $nickname=$G['this']->user($rs['friendId'],'nickname');
  $py=onez('pinyin')->py($nickname);
  $py=$py?ucfirst($py):'#';
  $words[$py][]=array(
    'type'=>'user',
    'avatar'=>$G['this']->avatar($rs['friendId']),
    'subject'=>$nickname,
    'userid'=>$rs['friendId'],
    'checked'=>false,
  );
}
ksort($words);
$users=array();
foreach($words as $py=>$list){
  $users[]=array(
    'type'=>'label',
    'subject'=>$py,
  );
  foreach($list as $rs){
    $users[]=$rs;
  }
}
$record[]=array(
  'type'=>'createChat',
  'groupId'=>$groupId,
  'users'=>$users,
);