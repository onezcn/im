<?php

/* ========================================================================
 * $Id: login.php 3675 2019-01-20 09:20:14Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_login extends onezphp{
  var $login_token='';
  var $is_register=0;
  function __construct(){
    
  }
  function css(){
    echo onez('ui')->css($this->url.'/css/page.css');
  }
  function url($type){
    $get=$_GET;
    $get['_method']=$type;
    if(!$type){
      unset($get['_method']);
    }
    return '?'.http_build_query($get);
  }
  /**
  * 检测是否登录，如果未登录，显示登录框
  * @return
  */
  function init($login_token='onez_login',$now=1){
    global $G;
    $this->login_token=$login_token;
    if($G['this']->option('site_login_register')){
      $this->is_register=1;
    }
    if($G['this']->option('site_login_qrcode')){
      $this->is_qrcode=1;
    }
    if($G['this']->option('site_login_rndcode')){
      define('ONEZ_LOGIN_RNDCODE',1);
    }
    if($tip=$G['this']->option('site_login_tip')){
      $this->tip=$tip;
    }
    $tip=onez()->gp('tip');
    if($tip){
      $this->tip=$tip;
    }
    
    if($avatar=$this->get('avatar')){
      $this->avatar=$avatar;
    }
    if(!$this->avatar && $avatar=$G['this']->option('site_login_avatar')){
      $this->avatar=$avatar;
    }
    !$this->avatar && $this->avatar=$this->url.'/php/images/test/avatar5.png';
    
    $_method=onez()->gp('_method');
    if($_method=='register'){
      if($this->is_register){
        return $this->register();
      }
    }elseif($_method=='findpwd'){
      return $this->findpwd();
    }elseif($_method=='logout'){
      return $this->logout();
    }elseif($_method=='login'){
      include_once(dirname(__FILE__)).'/php/login.php';
      exit();
    }elseif($_method=='qrcode'){
      include_once(dirname(__FILE__)).'/php/qrcode.php';
      exit();
    }
    
    $qhash=onez()->gp('qhash');
    if($qhash){
      list($userid,$id,$sid,$ptoken)=explode("\t",onez()->strcode($qhash,'DECODE'));
      if($ptoken==$G['this']->token){
        $info=onez('db')->open('member')->one("quick_hash='$qhash'");
        if($info['userid']==$userid){
          onez('cache')->cookie($this->login_token,"$info[userid]\t$info[nickname]",0);
          onez()->location('?');
        }
      }
    }
    $cookie=onez('cache')->cookie($login_token);

    $id='';$u='';
    if($cookie){
      list($id,$u)=explode("\t",$cookie);
    }
    if(!$cookie || !$id || !$u){
      if($now){
        include_once(dirname(__FILE__)).'/php/login.php';
        exit();
      }
    }
    $G['userid']=(int)$id;
    return array('id'=>$id,'username'=>$u);
  }
  function register(){
    global $G;
    $login_token=$this->login_token;
    include_once(dirname(__FILE__)).'/php/register.php';
    exit();
  }
  function findpwd(){
    global $G;
    $login_token=$this->login_token;
    include_once(dirname(__FILE__)).'/php/findpwd.php';
    exit();
  }
  /**
  * 退出登录状态
  * 
  * @return
  */
  function logout(){
    global $G;
    onez('cache')->cookie($this->login_token,'del');
    onez()->location($this->url(''));
  }
  /**
  * 判断时加入用户信息
  * 
  * @return
  */
  function auto($login_token){
    global $G;
    $login=onez('login')->init($login_token);
    $G['userid']=(int)$login['id'];
    $G['loginstr']=$login['extra'];

    $G['user']=onez('user')->info($G['userid']);
    if(!$G['user']){
      onez('login')->logout();
      onez('login')->init($login_token);
    }
  }
  function chklogin_local($users,$username,$password,$split='|'){
    if($users && is_array($users)){
      foreach($users as $k=>$v){
        list($id,$u,$p)=explode($split,$v);
        if($u==$username&&$password==$p){
          return "$id\t$u";
        }
      }
    }
    return false;
  }
}