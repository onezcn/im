<?php

/* ========================================================================
 * $Id: login.php 1772 2020-04-30 00:14:05Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$type=onez()->gp('type');
if($type=='logout'){
  onez('cache')->cookie($G['this']->cname,'del');
  return;
}
$action=onez()->gp('action');
if($action=='login'){
  $username=onez()->gp('username');
  $password=onez()->gp('password');
  $or=array();
  $or[]="username='$username'";
  if(strpos($username,'@')!==false){
    $or[]="email='$username'";
  }elseif(is_numeric($username) && strlen($username)==11 && $username[0]=='1'){
    $or[]="mobile='$username'";
  }
  $T=onez('db')->open('member')->one(implode(' or ',$or));
  !$T && onez()->error('账号不存在');
  #$password_md5=hash_hmac('md5',$password,$T['rndkey']);
  $password_md5=md5($password);#旧密码格式
  $T['password']!=$password_md5 && onez()->error('密码不正确');
  onez('cache')->cookie($G['this']->cname,"$T[userid]\t$T[username]\t".time(),0);
  onez()->ok('登录成功','refresh');
}elseif($action=='reg'){
  $username=onez()->gp('username');
  $password=onez()->gp('password');
  strlen($username)<6 && onez()->error('名称长度不能小于6个字符');
  strlen($password)<6 && onez()->error('密码长度不能小于6个字符');
  if(onez('db')->open('member')->rows("username='$username'")>0){
    onez()->error('此账号已被其他用户使用，请更换');
  }
  $item=array();
  $item['username']=$username;
  $item['nickname']=$username;
  $item['rndkey']=uniqid();
  #$item['password']=hash_hmac('md5',$password,$item['rndkey']);
  $item['password']=md5($password);#旧密码格式
  $item['infotime']=time();
  $item['infoip']=onez()->ip();
  $userid=onez('db')->open('member')->insert($item);
  onez('cache')->cookie($G['this']->cname,"$userid\t$username\t".time(),0);
  onez()->ok('恭喜，注册成功！','refresh');
}