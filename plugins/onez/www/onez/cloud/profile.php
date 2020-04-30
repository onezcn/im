<?php

/* ========================================================================
 * $Id: profile.php 3139 2017-09-18 11:29:48Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/cloud/profile.php',0));

$goto=(int)onez()->gp('goto');
$market=(int)onez()->gp('market');
$G['title']='请注册您的站点';

$item=$G['this']->option();
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

$post=array('action'=>'profile','format'=>'json','platform'=>$G['platform']);

$json=onez('onez')->post($post);
if($json['form']){
  foreach($json['form'] as $arr){
    $form->add($arr);
  }
}


#处理提交
if($onez=$form->submit()){
  $post=$onez;
  
  $post['format']='json';
  $post['action']='register';
  $post['appid']=onez()->gp('onez_appid');
  $post['appkey']=onez()->gp('onez_appkey');
  if(!$post['appid']){
    $post['appid']=$G['this']->option('onez_appid');
    $post['appkey']=$G['this']->option('onez_appkey');
  }
  $post['homepage']=onez()->homepage();
  $post['version']=$item['version'];
  $post['_time']=time();
  $mymd5=md5("{$post['appid']}&{$post['homepage']}&{$post['_time']}".md5($post['appkey']));
  $post['_md5']=$mymd5;
  $json=onez('onez')->post($post);
  $json['error'] && onez()->error($json['error']);
  $arr=array();
  foreach($json as $k=>$v){
    if(strpos($k,'onez_')!==false){
      $arr[$k]=$v;
    }
  }
  $G['this']->option_set($arr);
  onez()->ok($json['tip']?$json['tip']:'注册成功','reload');
}
onez('admin')->header();
?>
<style>
.addok{
  color:green;
}
</style>
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
  <form id="form-common" method="post">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php echo $G['title']?>
        </h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <?php echo $form->code();?>
<?php if(!$json['appid']){?>
        <?php onez('qrcode.doit')->show('http://onez.vip.onezphp.com/index.php?action=appid&t='.uniqid(),'_get_appid')?>
<?php }?>
      </div>
      <div class="box-footer clearfix tipbox">
        <button type="submit" class="btn btn-primary">
          确定
        </button>
      </div>
    </div>
  </form>
</section>
<?php if(!$json['appid']){?>
<script type="text/javascript">
function _cleartip(){
  $('.mytip').hide('slow');
  $('.addok').removeClass('addok');
}
function _get_appid(o){
  $('#input-onez_appid').val(o.appid).addClass('addok');
  $('#input-onez_appkey').val(o.appkey).addClass('addok');
  $('.mytip').remove();
  $('<span class="mytip" style="color:#ff0000">获取成功，已自动为您填写！</span>').show('fast').appendTo('.tipbox');
  setTimeout('_cleartip()',3000);
}
</script>
<?php }?>
<?php
echo $form->js();
onez('admin')->footer();
?>