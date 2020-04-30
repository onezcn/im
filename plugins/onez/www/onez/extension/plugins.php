<?php

/* ========================================================================
 * $Id: plugins.php 4968 2017-10-19 14:50:04Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/extension/plugins.php',0));
$G['title']='系统插件扩展';
$plugins_info=onez('cache')->get('plugins.status.'.$G['this']->token);
!$plugins_info && $plugins_info=array();
$action=onez()->gp('action');
if($action=='upgrade'){//强制更新
  $ptoken=onez()->gp('ptoken');
  if(!defined('ONEZ_AUTO_FETCH')){
    onez()->error('您已关闭一键更新功能');
  }
  if(strpos(ONEZ_ROOT,'/git/onezblue')!==false){
    onez()->error('此插件不允许更新');
  }
  if(!onez()->exists($ptoken)){
    onez()->error('插件不存在或不允许更新');
  }
  if(strpos(onez($ptoken)->path,'/myplugins/')!==false){
    onez()->error('此插件已关闭自动更新');
  }
  
  if(!empty($plugins_info['all'])){
    unset($plugins_info['all'][$ptoken]);
    
    onez('cache')->set('plugins.status.'.$G['this']->token,$plugins_info);
  }
  @unlink(onez($ptoken)->path.'/'.$ptoken.'.php');
  onez($ptoken);
  
  onez()->ok('更新插件成功','reload');
}

$plugins=array();
$glob=glob(ONEZ_ROOT.ONEZ_NODE_PATH.'/*');
if($glob){
  foreach($glob as $v){
    $name=basename($v);
    if(file_exists("$v/$name.php")){
      if(!in_array($name,$plugins)){
        $plugins[]=$name;
      }
    }
  }
}
onez('admin')->header();
onez('animate.css')->init();
?>
<section class="content-header">
  <h1>
    <?php echo $G['title']?><small>所有涉及到的云端插件扩展(目录: <?php echo ONEZ_NODE_PATH?>)</small>
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
  <div class="btns" style="padding-bottom: 0px">
  <div class="form-group ">
    <input class="form-control" id="kw" placeholder="请填您要查询的插件名称或标识" name="kw" type="text">
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
            <th>插件名称</th>
            <th>所在目录</th>
            <th>版本</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody class="todo-list">
          <?php
          foreach($plugins as $ptoken){
            $plugin=$plugins_info['all'][$ptoken];
            if(!$plugin){
              continue;
            }
            $name=($plugin['name']?$plugin['name']:$ptoken);
          ?>
          <tr data-ptoken="<?php echo $ptoken?>" data-name="<?php echo $name?>" data-hash="<?php echo $plugin['hash']?>">
            <td class="plugin-ptoken">
              <?php echo $name?>
            </td>
            <td class="plugin-summary">
              <?php echo ONEZ_NODE_PATH?>/<code><?php echo $ptoken?></code>
            </td>
            <td class="plugin-version">
              <?php 
              if($plugins_info['new'] && in_array($ptoken,$plugins_info['new'])){
                echo '<span style="color:red;display:block" class="onez-has-new-version">发现新版本,建议升级！</span>';
              }else{
                echo '<span style="color:gray">当前已经是最新版本了</span>';
              }
              ?>
            </td>
            <td class="plugin-btns">
              <?php echo $plugin['btns']?>
              <a href="javascript:void(0)" onclick="_upgrade('<?php echo $ptoken;?>','<?php echo $name;?>')" class="btn btn-xs btn-warning">
                强制更新
              </a>
            </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
  </div>
  <p>如需更新所有，请使用“<a href="<?php echo onez('onez')->href('/onez/cloud/upgrade.php')?>">一键更新</a>”功能</p>
</section>
<script type="text/javascript">
$(function(){
  $('.onez-has-new-version').ani('flash',false);
  $('#kw').bind('input keyup',function(){
    var kw=$(this).val();
    if(kw.length<1){
      $('tr[data-ptoken]').show();
    }else{
      $('tr[data-ptoken]').each(function(){
        var ptoken=$(this).attr('data-ptoken');
        var name=$(this).attr('data-name');
        if(ptoken.indexOf(kw)!=-1 || name.indexOf(kw)!=-1){
          $(this).show();
        }else{
          $(this).hide();
        }
      });
    }
  });
});
function _upgrade(ptoken){
  onez.confirm('强制更新后此应用将会立即更新到官方最新版本，是否继续？',function(){
    $.post(window.location.href,{action:'upgrade',ptoken:ptoken},function(o){
      onez.doit(o);
    },'json');
  });
}
</script>
<?php 
onez('admin')->footer();