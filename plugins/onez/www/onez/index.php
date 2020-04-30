<?php

/* ========================================================================
 * $Id: index.php 1586 2019-01-20 09:08:55Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez()->href('/onez/index.php'));
$G['title']='系统设置';
onez('admin')->header();
?>
<style>
h5.page-header{
  font-size: 14px;
  margin: 10px 0px;
  border: none;
  line-height: 1.1;
  border-left: 0.3em #333 solid;
  padding-left: 1em;
  clear: both;
  padding-bottom: 0;
  position: relative;
  top:5px;
}
.gw-container .page-header{border:none; border-left:0.3em #333 solid; padding-left:1em;}
.gw-container .tile{display:block; float:left; margin:0.4em;padding:.2em 1em .5em 1em; width:8em; text-align:center; background:#E0E0E0; color:#333; text-decoration:none;}
.gw-container .tile.tile-2x{width:10em;margin-top: 0.5em}
.gw-container .tile.tile-3x{width:15em;}
.gw-container .tile:hover{background:#7dacdd; color:#FFF;}
.gw-container .tile > i{display:block; font-size:2em; margin:0.3em auto 0 auto;}
.gw-container .tile > span{display:block;}
</style>
<section class="content gw-container">
<?php foreach(onez('admin')->menu as $k=>$v){?>
<?php if(!$v['href'] && !$v['url']){?>
  <h5 class="page-header"><?php echo $v['name']?></h5>
  <div class="clearfix"></div>
<?php }else{
  if(strpos($v['url'],urlencode('/onez/index.php'))!==false){
    continue;
  }
  $href='';
  if($v['href']){
    $href=onez()->href($v['href']);
  }elseif($v['url']){
    $href=$v['url'];
  }
  ?>
<a href="<?php echo $href?>" target="<?php echo $v['target']?$v['target']:'_self'?>" class="tile tile-2x img-rounded <?php echo $v['classname']?>">
	<i class="<?php echo $v['icon']?>"></i>
	<span><?php echo $v['name']?></span>
</a>
<?php }?>
<?php }?>
</section>
<?php
onez('admin')->footer();
?>