<?php

/* ========================================================================
 * $Id: databases_edit.php 3543 2016-11-14 01:28:37Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez()->href('/databases.php?_onez=db'));


$items=onez('cache')->get('options');

!$items['db_databases'] && $items['db_databases']=array();

$token=onez()->gp('token');
if($token){
  $item=$items['db_databases'][$token];
  $G['title']='编辑数据库配置';
  $btnname='保存修改';
}else{
  $G['title']='添加新数据库配置';
  $btnname='立即添加';
}

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'引用标识','type'=>'text','key'=>'token','hint'=>'请填写引用标识','notempty'=>'引用标识不能为空'));
$form->add(array('label'=>'备注名称','type'=>'text','key'=>'subject','hint'=>'备注名称不能为空','notempty'=>'备注名称不能为空'));
$options=array();
$options['dbhost']=array('label'=>'数据库地址','type'=>'text','key'=>'dbhost','hint'=>'如:localhost','notempty'=>'数据库地址不能为空','value'=>$item['dbhost']);
$options['dbuser']=array('label'=>'数据库账号','type'=>'text','key'=>'dbuser','hint'=>'如:root','notempty'=>'数据库账号不能为空','value'=>$item['dbuser']);
$options['dbpass']=array('label'=>'数据库密码','type'=>'text','key'=>'dbpass','hint'=>'','notempty'=>'','value'=>$item['dbpass']);
$options['dbname']=array('label'=>'数据库名称','type'=>'text','key'=>'dbname','hint'=>'如:onez_app，必须已创建完成','notempty'=>'数据库名称不能为空','value'=>$item['dbname']);
$options['tablepre']=array('label'=>'表名前缀','type'=>'text','key'=>'tablepre','hint'=>'onez_','notempty'=>'请勿修改','value'=>$item['tablepre']);

$options_charset=array(
  'utf8'=>'UTF-8',
);
$options['dbcharset']=array('label'=>'数据库编码','type'=>'select','key'=>'dbcharset','options'=>$options_charset);
foreach($options as $v){
  $form->add($v);
}

#处理提交
if($onez=$form->submit()){
  if($token){
    $items['db_databases'][$token]=$onez;
  }else{
    $items['db_databases'][$onez['token']]=$onez;
  }
  
  onez('cache')->option_set(array('db_databases'=>$items['db_databases']));
  
  onez()->ok('操作成功',onez()->href('/databases.php',1));
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
    <li>
      <a href="<?php echo onez()->href('/databases.php',1)?>">
        <?php echo $G['title']?>
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
          产品管理
        </h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <?php echo $form->code();?>
      </div>
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary">
          <?php echo $btnname;?>
        </button>
        <a href="<?php echo onez()->href('/databases.php',1)?>" class="btn btn-default">
          返回
        </a>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<?php
echo $form->js();
onez('admin')->footer();
?>