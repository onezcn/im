<?php

/* ========================================================================
 * $Id: sell.php 1261 2017-04-28 11:36:48Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/union/sell.php'));
$action=onez()->gp('action');
if($action=='doit'){
  $post=$_POST;
  $post['action']='sell.doit';
  $post['format']='json';
  $json=onez('onez')->post($post);
  onez()->output($json);
}
$G['title']='寄售站点';
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    <?php echo $G['title']?><small></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="<?php echo onez()->href('/')?>">
        <i class="fa fa-dashboard">
        </i>
        管理首页
      </a>
    </li>
    <li class="active">
      <?php echo $G['title'];?>
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <div class="pull-left">
          <a href="<?php echo onez('onez')->href('/onez/union/upload.php')?>" class="btn btn-info">我要寄售这个站点</a>
    </div>
    <div class="pull-right">
    </div>
    <div class="clearfix"></div>
  </div>
<?php 

$response = onez('onez')->post(array('action'=>'sell.list','siteptoken'=>$G['this']->token));
$json=json_decode($response,1);
if(!empty($json) && !empty($json['html'])){
  echo $json['html'];
}
?>
</section>
<?php
onez('admin')->footer();
?>