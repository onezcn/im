<?php

/* ========================================================================
 * $Id: login.php 2425 2020-04-26 19:22:32Z onez $
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
!$A['title'] && $A['title']='登录';
if(defined('IS_POST')){
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
  if(!$T){
    $A['tip']='账号不存在';
    return $A;
  }
  $password_md5=md5($password);
  if($T['password']!=$password_md5){
    $A['tip']='密码不正确';
    return $A;
  }
  onez('db')->open('device')->update(array('userid'=>0),"userid='$T[userid]'");
  onez('db')->open('device')->update(array('userid'=>$T['userid']),"id='$G[deviceid]'");
  $A['tip']='登录成功';
  $A['token']='reload';
  return $A;
}

$A['inForm']=true;
$A['once']=true;
$A['isFooter']=false;
$record[]=array(
  'type'=>'space',
);
$record[]=array(
  'type'=>'userinfo2',
  'nickname'=>'仿微信电脑版',
  'avatar'=>'http://cdn.onez.cn/icons/2020/0425/2020042518433537440001.svg',
  'userid'=>'0',
  'bgcolor'=>'transparent',
);
$record[]=array(
  'type'=>'space',
  'height'=>'5',
);
$record[]=array(
  'type'=>'input',
  'label'=>'登录账号',
  'key'=>'username',
  'hint'=>'您的登录账号',
  'notempty'=>'登录账号不能为空',
);
$record[]=array(
  'type'=>'input',
  'label'=>'登录密码',
  'key'=>'password',
  'inputype'=>'password',
  'hint'=>'您的登录密码',
  'notempty'=>'登录密码不能为空',
);
$record[]=array(
  'type'=>'space',
);
$record[]=array(
  'type'=>'button',
  'style'=>'2',
  'name'=>'立即登录',
  'token'=>'formSubmit',
);
$record[]=array(
  'type'=>'space',
);
$record[]=array(
  'type'=>'html',
  'html'=>'<p style="color:#999;text-align:center;font-size:14px">没有账号？点击这里免费注册</p>',
  'token'=>'page',
  'action'=>'register',
);
$record[]=array(
  'type'=>'html',
  'style'=>'',
  'html'=>'<p style="color:#999;text-align:center;font-size:14px;margin-top:30px;">忘记密码？<a href="findpwd">点此找回</a></p>',
);
$record[]=array(
  'type'=>'space',
);

#----------模板加载模式结束--------------
