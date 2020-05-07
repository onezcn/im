<?php

/* ========================================================================
 * $Id: init.php 3715 2020-04-29 11:41:03Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$G['ico']=$G['this']->url.'/favicon.ico';
$G['title']=onez('admin')->title='';
$action=onez()->gp('action');
#$mod=onez()->gp('mod');
if($action=='logout' || $mod=='logout.php'){
  onez('cache')->cookie($G['this']->cname,'del');
  onez()->location(onez()->href('/index.php'));
}
$cookie=onez('cache')->cookie($G['this']->cname);
if($cookie){
  list($userid,$u)=explode("\t",$cookie);
}

if(!$userid){
  if(!function_exists('_login_callback')){
    function _login_callback($username,$password,$password_md5){
      global $G;
      $or[]="username='$username'";
      if(strpos($username,'@')!==false){
        $or[]="email='$username'";
      }elseif(is_numeric($username) && strlen($username)==11 && $username[0]=='1'){
        $or[]="mobile='$username'";
      }
      $T=onez('db')->open('member')->one(implode(' or ',$or));
      !$T && onez()->error('账号不存在');
      /*
      if(!$T['rndkey']){
        onez('debug')->showerror();
        $T['rndkey']=uniqid();
        $item=array();
        $item['rndkey']=$T['rndkey'];
        $item['password']=$T['password']=hash_hmac('md5',$password,$T['rndkey']);
        onez('db')->open('member')->update($item,"userid='$T[userid]'");
      }
      //*/
      $password_md5=hash_hmac('md5',$password,$T['rndkey']);
      $T['password']!=$password_md5 && onez()->error('密码不正确');
      return "$T[userid]\t$T[username]\t".time();
    }
  }
  if(!function_exists('_register_callback')){
    function _register_callback($username,$password,$password_md5){
      if(onez('db')->open('member')->rows("username='$username'")>0){
        onez()->error('此账号已被其他用户使用，请更换');
      }
      $item=array();
      $item['username']=$username;
      $item['nickname']=$username;
      $item['rndkey']=uniqid();
      $item['password']=hash_hmac('md5',$password,$item['rndkey']);
      $item['infotime']=time();
      $item['infoip']=onez()->ip();
      $userid=onez('db')->open('member')->insert($item);
      return "$userid\t$username\t".time();
    }
  }
  onez('login')->avatar=$G['this']->url.'/images/logo.jpg';
  $header=<<<ONEZ
<style>
.signin-head img.img-circle{
  border-radius:50%;
}
</style>
ONEZ;
  #define('ONEZ_LOGIN_RNDCODE',1);
  onez('login')->set('header',$header);
  #onez('login')->is_register=1;
  #onez('login')->init($G['this']->cname);
}
if($userid){
  $G['user']=onez('db')->open('member')->one("userid='$userid'");
  if($G['user']){
    $G['userid']=$G['user']['userid'];
  }else{
    onez('cache')->cookie($G['this']->cname,'del');
    onez()->location('/');
  }
}

$tmp_userid=(int)onez('cache')->cookie('tmp_userid');
if($tmp_userid>0){
  $G['TMP_USERID']=$tmp_userid;
  $G['userid']=$tmp_userid;
  $G['user']=onez('db')->open('member')->one("userid='$G[userid]'");
  !$G['user']['nickname'] && $G['user']['nickname']=$G['user']['username'];
  $action=onez()->gp('action');
  if($action=='tmp_leave'){
    ob_clean();
    onez('cache')->cookie('tmp_userid','demp');
    onez()->location('/?');
  }
  onez('admin')->menu_top_left='<ul class="nav navbar-nav">
  <li><a href="?action=tmp_leave" style="color:#ff0">您正在以“'.$G['user']['nickname'].'”的身份登录，点此恢复</a></li>
</ul>';
}
if($userid){
  $G['nickname']=$G['user']['nickname']?$G['user']['nickname']:$G['user']['username'];
  $G['avatar']=$G['this']->avatar($G['userid']);
  #即时通讯模块
  onez('factory.im')->init(array(
    'server'=>'ws://www.onez.cn:20201',
    'serverS'=>'wss://www.onez.cn:20202',
    'apiurl'=>'http://www.onez.cn:20203/api',
  ));
  $G['footer'].=onez('factory.im')->pc();
  $G['footer'].=onez('ui')->js($G['this']->url.'/js/app.js');
  $touchFile=ONEZ_CACHE_PATH.'/init.lock';
  if(!file_exists($touchFile)){
    @touch($touchFile);
    $extra=array(
      'add-from'=>$userid,
      'add-type'=>'init',
    );
    onez('factory.im')->addFriend($userid,$userid,$extra);
    onez('factory.im')->addMessageSystem($userid,'感谢使用佳蓝即时通讯系统开源版！如有任何问题，请联系QQ：6200103');
  }
}
