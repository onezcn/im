<?php

/* ========================================================================
 * $Id: login.php 3152 2019-05-05 14:46:52Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$mod=onez()->gp('mod');
if(strpos($mod,'logout.php')!==false){
  onez()->location(onez()->cururl(false,array('mod')));
}
$action=onez()->gp('action');
if($action=='chklogin'){
  if(defined('ONEZ_LOGIN_RNDCODE')){
    if(!onez('rndcode')->check('rndcode')){
      onez()->error('验证码不正确');
    }
  }
  $username=onez()->gp('username');
  $password=onez()->gp('password');
  $password_md5=md5($password);
  if(function_exists('_login_callback')){
    $T=_login_callback($username,$password,$password_md5);
  }else{
    if(method_exists($G['this'],'_login_callback')){
      $T=$G['this']->_login_callback($username,$password,$password_md5);
    }else{
      onez()->error('请确保当前页有_login_callback函数');
    }
  }
  $T===false && onez()->error('账号或密码不正确');
  onez('cache')->cookie($this->login_token,$T,0);
  $goto=onez()->gp('goto');
  !$goto && $goto=onez('login')->url('');
  onez()->ok('登录成功',$goto);
}
$ui=onez('ui')->init();
$ui->header();
echo onez('ui')->css($this->url.'/php/images/signin.css');
?>
<link rel="stylesheet" href="<?php echo onez('admin')->url?>/assets/css/font-awesome.min.css">
<?php echo $this->get('header')?>
<div class="signin">
	<div class="signin-head">
    <img src="<?php echo $this->avatar?>" alt="" class="img-circle">
    <?php echo $this->get('head')?>
  </div>
	<form class="form-signin" method="post" role="form">
    <p class="form-item">
      <i class="fa fa-user"></i>
      <input type="text" name="username" class="form-control" placeholder="登录账号" required autofocus />
    </p>
    <p class="form-item">
      <i class="fa fa-key"></i>
      <input type="password" name="password" class="form-control" placeholder="登录密码" required />
    </p>
    <?php if(defined('ONEZ_LOGIN_RNDCODE')){?>
    <p class="form-item">
      <i class="fa fa-gg"></i>
      <input type="text" name="rndcode" class="form-control rndcode" placeholder="验证码" required />
      <img src="<?php echo onez('rndcode')->pic()?>" class="rndcode-img"/>
    </p>
    <?php }?>
    <?php echo $this->get('form')?>
		<button class="btn btn-lg btn-warning btn-block" type="submit">登录</button>
    <input type="hidden" name="action" value="chklogin" />
    
    <p><?php echo onez('login')->tip?></p>
    <div class="">
      <div class="pull-left">
<?php if($this->is_register){?>
  <a href="<?php echo onez('login')->url('register')?>">没有账号？点击这里注册！</a>
<?php }?>
      </div>
      <div class="pull-right">
<?php if($this->is_qrcode){?>
  <a href="<?php echo onez('login')->url('qrcode')?>"><i class="fa fa-qrcode"></i> 扫码登录</a>
<?php }?>
      </div>
      <div class="clearfix"></div>
    </div>
	</form>
</div>
<script type="text/javascript">
$(function(){
  $('.form-signin').unbind('submit').bind('submit',function(){
    $.post(window.location.href,$(this).serialize(),function(o){
      if(o.error){
        $('.rndcode-img').trigger('click');
        alert(o.error);
      }else{
        location.href=o.goto;
      }
    },'json');
    return false;
  });
});
</script>
<?php echo $this->get('footer')?>
<?php 
if($G['mypc.autologin']){
  onez('mypc')->autologin();
}
$ui->footer();
