<?php

/* ========================================================================
 * $Id: register.php 2849 2019-06-04 14:00:44Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$action=onez()->gp('action');
if($action=='regpost'){
  if(defined('ONEZ_LOGIN_RNDCODE')){
    if(!onez('rndcode')->check('rndcode')){
      onez()->error('验证码不正确');
    }
  }
  $username=onez()->gp('username');
  $password=onez()->gp('password');
  $password2=onez()->gp('password2');
  $password_md5=md5($password);
  if($password!=$password2){
    onez()->error('两次密码不一致');
  }
  if(function_exists('_register_callback')){
    $T=_register_callback($username,$password,$password_md5);
  }else{
    if(method_exists($G['this'],'_register_callback')){
      $T=$G['this']->_register_callback($username,$password,$password_md5);
    }else{
      onez()->error('请确保当前页有_register_callback函数');
    }
  }
  $T['error'] && onez()->error($T['error']);
  onez('cache')->cookie($this->login_token,$T,0);
  onez()->ok('注册成功',onez('login')->url(''));
}
$ui=onez('ui')->init();
$ui->header();
?>
<link rel="stylesheet" href="<?php echo $this->url?>/php/images/signin.css">
<link rel="stylesheet" href="<?php echo onez('admin')->url?>/assets/css/font-awesome.min.css">
<?php echo $this->get('header')?>
<div class="signin">
	<div class="signin-head"><img src="<?php echo $this->avatar?>" alt="" class="img-circle"></div>
	<form class="form-signin" method="post" role="form">
    <p class="form-item">
      <i class="fa fa-user"></i>
      <input type="text" name="username" class="form-control" placeholder="请填写您要注册的登录账号" required autofocus />
    </p>
    <p class="form-item">
      <i class="fa fa-key"></i>
      <input type="password" name="password" class="form-control" placeholder="登录密码" required />
    </p>
    <p class="form-item">
      <i class="fa fa-key"></i>
      <input type="password" name="password2" class="form-control" placeholder="确认登录密码" required />
    </p>
    <?php if(defined('ONEZ_LOGIN_RNDCODE')){?>
    <p class="form-item">
      <i class="fa fa-gg"></i>
      <input type="text" name="rndcode" class="form-control rndcode" placeholder="验证码" required />
      <img src="<?php echo onez('rndcode')->pic()?>" class="rndcode-img"/>
    </p>
    <?php }?>
		<button class="btn btn-lg btn-success btn-block" type="submit">立即注册</button>
    <input type="hidden" name="action" value="regpost" />
    <p>
    </p>
    <p>
      <a href="<?php echo onez('login')->url('login')?>">已有账号？点击这里登录！</a>
    </p>
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
<?php 
$ui->footer();
