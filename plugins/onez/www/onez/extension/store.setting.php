<?php

/* ========================================================================
 * $Id: store.setting.php 2010 2017-03-31 00:24:10Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/extension/store.setting.php',0));

$id=(int)onez()->gp('id');
$G['title']='全局设置';
$btnname='保存修改';

$item=$G['this']->option();

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

onez('call')->set('setting',array())->call('setting',array('values'=>$item));
$setting=onez('call')->get('setting');
if($setting){
  $form->add(array('label'=>'系统提示','type'=>'html','html'=>'以下为来自各应用的配置参数，请认真填写'));
  foreach($setting as $arr){
    $form->add($arr);
  }
}else{
  $form->add(array('label'=>'系统提示','type'=>'html','html'=>'<span style="color:red">当前网站安装的应用中，没有需要配置的参数</span>'));
}

#处理提交
if($onez=$form->submit()){
  ob_clean();
  $G['this']->option_set($onez);
  onez()->ok('操作成功','reload');
}
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    <?php echo $G['title']?>
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
      <?php echo $G['title']?>
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
        <?php echo $form->code()?>
      </div>
    </div>
<?php if($setting){?>
        <button type="submit" class="btn btn-primary">
          <?php echo $btnname?>
        </button>
<?php }?>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<?php
echo $form->js();
onez('admin')->footer();
?>