<?php

/* ========================================================================
 * $Id: body_sidebar.php 3192 2019-06-17 10:25:08Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
global $G;
?>
<body class="hold-transition <?php echo $this->style?><?php if($this->boxed)echo' layout-boxed'?> sidebar sidebar-mini <?php echo $miniwin?'miniwin':''?>" <?php echo $this->body_attr?>>
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo $this->homeurl?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><?php echo $this->title_mini?></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><?php echo $this->title?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <?php if($this->menu_top_left){?>
      <div class="collapse navbar-collapse pull-left">
        <?php echo $this->menu_top_left?>
      </div>
      <?php }?>
      <div class="navbar-custom-menu">
        <?php echo $this->menu_top_right?>
      </div>
<script type="text/javascript">
menu_check_nums('header div.navbar-collapse');
menu_check_nums('header div.navbar-custom-menu');
</script>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
<?php echo $this->sidebar_top?>
<?php if(onez()->exists('account') && $options['dbinfo']){?>
<div class="user-panel">
  <div class="pull-left image">
    <img src="<?php echo onez('account')->user()->avatar()?>" class="img-circle" alt="User Image">
  </div>
  <div class="pull-left info">
    <p><?php echo onez('account')->user()->info('nickname')?></p>
    <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
  </div>
</div>
<?php }else{?>
<?php }?>
<?php /*
if(file_exists(getcwd().'/www/search.php')){
  $search_url=onez()->href('search');
  list(,$search_url)=explode('?',$search_url);
  parse_str($search_url,$info);
  ?>
<form method="get" class="sidebar-form">
<?php foreach($info as $k=>$v){?>
<input type="hidden" name="<?php echo $k?>" value="<?php echo $v?>" />
<?php }?>
  <div class="input-group">
    <input type="text" name="q" class="form-control" placeholder="查找后台功能">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
          </button>
        </span>
  </div>
</form>
<script type="text/javascript">
$(function(){
  $('.sidebar-form').unbind('submit').bind('submit',function(){
    var q=$(this).find('input[name="q"]').val();
    if(q.length<1){
      return false;
    }
  });
});
</script>
<?php }*/?>
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
<?php 
if(!defined('CUR_URL')){
  $mod=onez()->gp('mod');
  define('CUR_URL',$mod);
}
$this->showmenu($this->menu);
?>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
