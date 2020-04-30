<?php

/* ========================================================================
 * $Id: store.php 10119 2018-09-01 16:48:46Z onez $
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
define('CUR_URL',onez('onez')->href('/onez/extension/store.php',0));
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
}elseif($action=='install'){//安装应用
  onez('onez')->install($_REQUEST);
  onez()->ok('安装应用成功',onez('onez')->href('/onez/extension/store.php?skill='.$skill));
}elseif($action=='upgrade'){//强制更新
  $id=(int)onez()->gp('id');
  $T=$G['this']->data()->open('addons')->one("id='$id'");
  if(!defined('ONEZ_AUTO_FETCH')){
    onez()->error('您已关闭一键更新功能');
  }
  if(strpos(ONEZ_ROOT,'/git/onezblue')!==false){
    onez()->error('当前网站所有文件已经是最新版了，无需更新',onez('onez')->href('/onez/cloud/upgrade.php'));
  }
  if(strpos(onez($T['token'])->path,'/myplugins/')!==false){
    onez()->error('此应用已关闭自动更新',onez('onez')->href('/onez/extension/store.php?skill='.$skill));
  }
  $PTokens=array($T['token']);
  @unlink(onez($T['token'])->path.'/'.$T['token'].'.php');
  onez($T['token']);
  $post=array('action'=>'addons.upgrade','format'=>'json','platform'=>$G['platform']);
  $json=onez('onez')->post($post);
  if($json['ptokens'] && is_array($json['ptokens'])){
    foreach($json['ptokens'] as $ptoken){
      if(!in_array($ptoken,$PTokens)){
        $PTokens[]=$ptoken;
        @unlink(onez($ptoken)->path.'/'.$ptoken.'.php');
        onez($ptoken);
      }
    }
  }
  
  onez()->ok('更新应用成功',onez('onez')->href('/onez/extension/store.php?skill='.$skill));
}elseif($action=='uninstall'){
  $id=(int)onez()->gp('id');
  $ptoken=onez()->gp('ptoken');
  $subject=onez()->gp('subject');
  $T=$G['this']->data()->open('addons')->one("id='$id'");
  if($T){
    $G['this']->data()->open('addons')->delete("id='$id'");
  }
  onez('onez')->update_addons();
  onez()->ok('卸载应用成功','reload');
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
$record=$G['this']->data()->open('addons')->page("key10='$skill' and index12='0'$xxx order by step,id");
onez('onezjs')->init();
$G['title']='应用中心';
if($skill!='none'){
  $T=$G['this']->data()->open('addons')->one("token='$skill'");
  if($T){
    $G['title']='应用中心 - '.$T['subject'].'的子插件';
    $sname=' ('.$T['subject'].')';
  }
}

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
    <?php echo $G['title']?><small>佳蓝云引擎官方应用</small>
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
      <a href="http://www.onezphp.com/?mod=/market/shop.php&sitehash=<?php echo onez('onez')->sitehash()?>" class="onez-miniwin btn btn-info" data-width="1280">
        佳蓝应用市场<?php echo $sname?>
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
                      <th width="48">应用图标</th>
                      <th>应用名称</th>
            <th>
              当前版本
            </th>
                      <th>操作</th>
          </tr>
        </thead>
        <tbody class="todo-list">
          <?php foreach($record[0] as $rs){
             ?>
          <tr>
                      <td>
                <img  class="handle" src="http://shop.onezphp.com/?method=icon&ptoken=<?php echo $rs['token'];?>" width="48" style="border-radius: 6px" data-id="<?php echo $rs['id'];?>" />
                      </td>
            <td>
              <div class="desc">
                <p style="font-size:14px"><a href="http://www.onezphp.com/?mod=/view.php&ptoken=<?php echo $rs['token']?>" target="_blank"><?php echo $rs['subject'];?></a> <code><?php echo $rs['token']?></code>
                <?php if(strpos(onez($rs['token'])->path,'/myplugins/')!==false){?>
              <span class="btn btn-xs bg-black">二次开发</span>
                <?php }?>
                </p>
                <p style="font-size:12px;color:#999"><?php echo $rs['summary'];?></p>
                <p class="links"><?php echo onez('onez')->urls_admin($rs['token'])?></p>
              </div>
                      </td>
            <td style="font-size:12px">
              v<?php echo $rs['version']?$rs['version']:'1.0'?>
            </td>
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
              <?php if(strpos(onez($rs['token'])->path,'/myplugins/')===false){?>
              <a href="javascript:void(0)" onclick="_upgrade('<?php echo $rs['id'];?>','<?php echo $rs['subject'];?>')" class="btn btn-xs btn-warning">
                强制更新
              </a>
              <?php }?>
              <?php if(strpos($rs['token'],'site.')!==false || onez($rs['token'])->has_addons){?>
              <a href="<?php echo onez('onez')->href('/onez/extension/store.php?skill='.$rs['token'])?>" data-width="1080" class="onez-miniwin btn btn-xs bg-purple">
                子插件(<?php echo $G['this']->data()->open('addons')->rows("key10='$rs[token]'")?>)
              </a>
              <?php }?>
              <a href="<?php echo onez('onez')->href('/onez/extension/store.edit.php?id='.$rs['id'])?>" class="onez-miniwin btn btn-xs btn-success">
                编辑
              </a>
              <a href="javascript:void(0)" onclick="_uninstall('<?php echo $rs['id'];?>')" class="btn btn-xs btn-danger">
                卸载
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
function _upgrade(id){
  onez.confirm('强制更新后此应用将会立即更新到官方最新版本，是否继续？',function(){
    $.post(window.location.href,{action:'upgrade',id:id},function(o){
      onez.doit(o);
    },'json');
  });
}
function _uninstall(id){
  onez.confirm('您确定要卸载这个应用吗？应用内容数据将被清空，且不可恢复！',function(){
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
