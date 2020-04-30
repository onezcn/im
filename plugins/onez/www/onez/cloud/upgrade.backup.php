<?php

/* ========================================================================
 * $Id: upgrade.backup.php 1985 2017-03-31 00:02:38Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$G['title']='更新回滚列表';
$xxx="";
$record=$G['this']->data()->open('upgrade')->page("1 order by id desc");
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
      <?php echo $G['title']?>
    </li>
  </ol>
</section>
<section class="content">
<div class="alert alert-success">
			恢复时，请手动将此目录中的文件上传至网站即可（选中全部文件和目录直接上传）
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
                      <th>日期</th>
                      <th>备份文件</th>
                      <th>文件大小</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($record[0] as $rs){
            $filesize=0;
            if(file_exists(ONEZ_ROOT.$rs['file'])){
              $filesize=filesize(ONEZ_ROOT.$rs['file']);
            }else{
              $rs['file']='<span style="color:red">文件已被删除</span>';
            }
             ?>
          <tr>
                      <td><?php echo date('Y-m-d H:i:s',$rs['addtime'])?></td>
                      <td><?php echo $rs['file']?></td>
                      <td><?php echo onez('files')->filesize($filesize)?></td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
    <?php if($record[1]){ ?>
    <div class="box-footer clearfix">
      <?php echo $record[1];?>
    </div>
    <?php }?>
  </div>
</section>
<?php
onez('admin')->footer();
?>