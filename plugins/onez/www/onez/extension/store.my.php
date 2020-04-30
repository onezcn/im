<?php

/* ========================================================================
 * $Id: store.my.php 6866 2017-10-12 11:31:56Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */


/* ========================================================================
 * $Id: store.php 7749 2017-04-18 09:46:56Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/extension/store.my.php',0));
$skill=onez()->gp('skill');
$skill=='none' && $skill='';
$type=onez()->gp('type');
$market=onez()->gp('market');
$input=onez()->gp('input');
$urlstr="market=$market&input=$input";
$action=onez()->gp('action');

#检测到安装了新的插件
$addonid=(int)onez()->gp('addonid');
if($addonid){
  $json=onez('onez')->post(array('action'=>'addon','format'=>'json','addonid'=>$addonid));
  if($json['error']){
    !empty($_POST) && onez()->error($json['error']);
    onez('showmessage')->error($json['error'],onez('onez')->href('/onez/extension/store.php',0));
  }
  if($json['addons']){
    foreach($json['addons'] as $v){
      onez('onez')->install($v);
    }
  }
  !empty($_POST) && onez()->ok('安装应用成功',onez()->cururl(false,array('addonid')));
  onez()->location(onez()->cururl(false,array('addonid')));
}

$types=array();
$types['all']='全部';
$types['site']='综合';
$types['app_event']='APP';
$types['weixin_event']='微信';
$types['auto_event']='移动';
if($market && $types[$market]){
  $types=array($market=>$types[$market]);
}
!$type && $type=key($types);
if($action=='enabled'){
  $id=(int)onez()->gp('id');
  $s=(int)onez()->gp('s');
  $T=$G['this']->data()->open('addons')->update(array('index1'=>$s),"id='$id'");
  onez('onez')->update_addons();
  onez()->ok('操作成功');
}elseif($action=='uninstall'){
  $id=(int)onez()->gp('id');
  $ptoken=onez()->gp('ptoken');
  $subject=onez()->gp('subject');
  $T=$G['this']->data()->open('addons')->one("id='$id'");
  if($T){
    $G['this']->data()->open('addons')->delete("id='$id'");
  }
  onez('onez')->update_addons();
  onez()->ok('删除应用成功','reload');
}elseif($action=='steps'){
  ob_clean();
  $ids=explode(',',onez()->gp('ids'));
  $n=-1000;
  foreach($ids as $k=>$id){
    $rs=array();
    $rs['step']=$n;
    $G['this']->data()->open('addons')->update($rs,"id='$id'");
    $n++;
  }
  onez('onez')->update_addons();
  onez()->ok('重新排序成功','reload');
}
$xxx='';
$record=$G['this']->data()->open('addons')->page("index12='1'$xxx order by step,id");
onez('onezjs')->init();
$G['title']='自定义应用';

onez('admin')->header();
onez('animate.css')->head();
?>
<style>
.desc p{
  padding-bottom: 0;
  margin-bottom: 0;
  line-height: 1.8;
}
</style>
<section class="content-header">
  <h1>
    <?php echo $G['title']?><small>您自己创建和开发的非官方应用</small>
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
  <div class="btns" style="padding-bottom: 10px">
    <div class="pull-left">
      <a href="<?php echo onez('onez')->href('/onez/extension/store.my.edit.php')?>" class="onez-miniwin btn btn-info" data-width="960">
        创建新应用
      </a>
    </div>
    <div class="pull-right">
    </div>
    <div class="clearfix"></div>
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
            <th>应用名称</th>
            <th>所在目录</th>
            <th>创建时间</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody class="todo-list">
          <?php foreach($record[0] as $rs){
             ?>
          <tr>
            <td><?php echo $rs['subject']?></td>
            <td><?php echo ONEZ_MYNODE_PATH?>/<code><?php echo $rs['ptoken']?></code></td>
            <td><?php echo date('Y-m-d H:i:s',$rs['addtime'])?></td>
            <td>
              <?php if($rs['index1']){?>
              <a href="javascript:void(0)" onclick="_enabled('<?php echo $rs['id'];?>',0)" class="btn btn-xs btn-info" title="点击停用">
                已启用
              </a>
              <?php }else{?>
              <a href="javascript:void(0)" onclick="_enabled('<?php echo $rs['id'];?>',1)" class="btn btn-xs btn-default" title="点击启用">
                已停用
              </a>
              <?php }?>
              <a href="<?php echo onez('onez')->href('/onez/extension/store.my.edit.php?id='.$rs['id'])?>" data-width="960" class="onez-miniwin btn btn-xs btn-success">
                编辑
              </a>
              <a href="javascript:void(0)" onclick="_uninstall('<?php echo $rs['id'];?>','<?php echo $rs['ptoken']?>')" class="btn btn-xs btn-danger">
                删除
              </a>
            </td>    
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
<?php if($record[1]){ ?>
    <div class="box-footer clearfix">
      <?php echo $record[1]?>
    </div>
<?php }?>
  </div>
</section>
<?php if($input){?>
<script type="text/javascript">
function _select(v){
  parent.postMessage({
    '<?php echo $input?>':v
  },'*');
}
</script>
<?php }?>
<script type="text/javascript">
$(function(){
  $(".todo-list").sortable({
    placeholder: "tr",
    handle: ".handle",
    forcePlaceholderSize: true,
    stop: function(){
      var ids=[];
      $('[data-id]').each(function(){
        ids.push($(this).attr('data-id'));
      });
      $.post(window.location.href,{action:'steps',ids:ids.join(',')},function(){
      });
    },
    zIndex: 999999
  });
});
function _enabled(id,s){
  $.post(window.location.href,{action:'enabled',id:id,s:s},function(data){
    location.reload();
  },'json');
}
function _uninstall(id,ptoken){
  onez.confirm('您确定要删除这个应用吗？应用内容数据将被清空，且不可恢复！<p>这是一个自定义应用，本操作仅会删除数据库记录，源代码文件无法自动删除，请使用FTP手动删除<?php echo ONEZ_MYNODE_PATH?>/'+ptoken+'</p>',function(){
    $.post(window.location.href,{action:'uninstall',id:id},function(data){
      location.reload();
    },'json');
  });
}
$(function(){
  window.addEventListener('message', function(event){
    var data=event.data;
    data.action='install';
    closeWin();
    $.post(window.location.href,data,function(o){
      if(o.error){
        onez.alert(o.error);
      }else{
        location.reload();
      }
    },'json');
  }, false);
});
</script>
<?php
onez('admin')->footer();
?>
