<?php

/* ========================================================================
 * $Id: qrcode.php 2974 2017-05-22 12:42:35Z onez $
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
if($action=='checklogin'){
  $rndcode=onez()->gp('rndcode');
  !$rndcode && onez()->error('无效请求');
  if(onez('user')->usertype()=='user'){
    $info=onez('user')->info("key8='$rndcode'",0);
    if(!$info){
      onez()->error('登录失败');
    }
  }else{
    $info=onez('db')->open('member')->one("quick_hash='$rndcode'");
    if(!$info){
      onez()->error('请扫描二维码');
    }
    onez('db')->open('member')->update(array(
      'quick_hash'=>'',
    ),"userid='$info[userid]'");
  }
  !$info['nickname'] && $info['nickname']=$info['userid'];
  $T="$info[userid]\t$info[nickname]";
  onez('cache')->cookie($this->login_token,$T,0);
  onez()->ok('登录成功',onez('login')->url(''));
}
$ui=onez('ui')->init();
$ui->header();
$rndcode=uniqid();
$qrcode_url=onez('login')->get('qrcode_url');
!$qrcode_url && $qrcode_url=onez()->homepage().'/index.php?_m=m&action='.onez('qrcode.login')->action('login&rc='.$rndcode);
$qrcode_url=str_replace('@RNDCODE@',$rndcode,$qrcode_url);
?>
<link rel="stylesheet" href="<?php echo $this->url?>/php/images/signin.css" />
<link rel="stylesheet" href="<?php echo onez('admin')->url?>/assets/css/font-awesome.min.css">
<?php echo $this->get('header')?>
<div class="signin">
	<div class="signin-head"><img src="<?php echo $this->avatar?>" alt="" class="img-circle"></div>
	<form class="form-signin" method="post" role="form">
    <p style="text-align: center;">
      
      <?php 
      $qrcode_caption=onez('login')->get('qrcode_caption');
      !$qrcode_caption && $qrcode_caption='请使用微信或手机客户端扫描以下二维码';
      echo $qrcode_caption;
      ?>
    </p>
    <p class="form-item" style="background: transparent;text-align: center;">
      <?php echo onez('qrcode.doit')->set('size',200)->show($qrcode_url,'login')?>
    </p>
    <?php echo $this->get('form')?>
    <p><?php echo onez('login')->tip?></p>
    <div class="">
      <div class="pull-left">
<?php if($this->is_register){?>
  <a href="<?php echo onez('login')->url('register')?>">没有账号？点击这里注册！</a>
<?php }?>
      </div>
      <div class="pull-right">
<?php if($this->is_qrcode){?>
  <a href="<?php echo onez('login')->url('login')?>"><i class="fa fa-key"></i> 使用账号密码登录</a>
<?php }?>
      </div>
      <div class="clearfix"></div>
    </div>
	</form>
</div>
<script type="text/javascript">
function login(){
  $.post(window.location.href,{action:'checklogin',rndcode:'<?php echo $rndcode?>'},function(o){
    if(typeof o.goto!='undefined'){
      location.href=o.goto;
    }else{
      //location.href='<?php echo onez('login')->url('qrcode&tip='.urlencode('二维码已失效，请重新扫描'))?>';
    }
  },'json');
}
setInterval('login()',2000);
</script>
<?php echo $this->get('footer')?>
<?php 
$ui->footer();
