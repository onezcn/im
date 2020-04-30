<?php

/* ========================================================================
 * $Id: body_nosidebar.php 1183 2019-06-17 10:25:19Z onez $
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
<body class="hold-transition <?php echo $this->style?><?php if($this->boxed)echo' layout-boxed'?> layout-top-nav nosidebar sidebar-mini <?php echo $miniwin?'miniwin':''?>" <?php echo $this->body_attr?>>
<div class="wrapper">

  <header class="main-header">
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <div class="navbar-header">
        <a href="<?php echo $this->homeurl?>" class="navbar-brand"><?php echo $this->title?></a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
          <i class="fa fa-bars"></i>
        </button>
      </div>
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
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  