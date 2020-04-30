<?php

/* ========================================================================
 * $Id: databases.php 3357 2016-11-14 01:28:37Z onez $
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

$G['title']='数据库管理';
$btnname='保存修改';

$item=onez('cache')->get('options');

!$item['db_databases'] && $item['db_databases']=array();


$record=$item['db_databases'];
$action=onez()->gp('action');
if($action=='delete'){
  $id=onez()->gp('token');
  onez()->ok('删除数据库配置成功','reload');
}elseif($action=='step'){
  $myRecord=array();
  $tokens=onez()->gp('tokens');
  foreach(explode(',',$tokens) as $v){
    $myRecord[$v]=$record[$v];
  }
  onez('cache')->option_set(array('db_databases'=>$myRecord));
  exit();
}
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    <?php echo $G['title']?>
  </h1>
  <ol class="breadcrumb">
    <li class="active">
      <?php echo $G['title']?>
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?php echo onez()->href('/databases_edit.php',1)?>" class="btn btn-success">
      添加数据库配置
    </a>
  </div>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        <?php echo $G['title']?>
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body  table-responsive no-padding">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>
              引用标识
            </th>
            <th>
              备注名称
            </th>
            <th>
              数据库地址
            </th>
            <th>
              数据库账号
            </th>
            <th>
              数据库密码
            </th>
            <th>
              数据库名称
            </th>
            <th>
              表名前缀
            </th>
            <th>
              数据库编码
            </th>
            <th>
              操作
            </th>
          </tr>
        </thead>
        <tbody class="todo-list">
          <?php foreach($record as $rs){
            ?>
          <tr>
            <td>
              <?php echo $rs['token'];?>
            </td>
            <td>
              <?php echo $rs['subject'];?>
            </td>
            <td>
              <?php echo $rs['dbhost'];?>
            </td>
            <td>
              <?php echo $rs['dbuser'];?>
            </td>
            <td>
              <?php echo $rs['dbpass'];?>
            </td>
            <td>
              <?php echo $rs['dbname'];?>
            </td>
            <td>
              <?php echo $rs['tablepre'];?>
            </td>
            <td>
              <?php echo $rs['dbcharset'];?>
            </td>
            <td>
              <a href="<?php echo onez()->href('/databases_edit.php?token='.$rs['token'],1)?>" class="btn btn-xs btn-success">
                编辑
              </a>
              <a href="javascript:void(0)" onclick="onez.del('<?php echo $rs['token'];?>')" class="btn btn-xs btn-danger">
                删除
              </a>
            </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php
onez('admin')->footer();
?>