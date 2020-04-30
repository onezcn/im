<?php

/* ========================================================================
 * $Id: store.edit.php 4153 2017-04-18 16:17:28Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */


/* ========================================================================
 * $Id: store.edit.php 1849 2017-04-13 06:02:40Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');

$id=(int)onez()->gp('id');
$item=$G['this']->data()->open('addons')->one("id='$id'");
$G['title']='编辑';
$btnname='保存修改';


$action=onez()->gp('action');
if($action=='develop'){
  $id=(int)onez()->gp('id');
  $files=onez('files')->find(onez($item['token'])->path)->files;
  foreach($files as $filename=>$data){
    $file=ONEZ_ROOT.ONEZ_MYNODE_PATH.'/'.$item['token'].'/'.$filename;
    onez()->write($file,$data);
  }
  onez()->ok('操作成功','reload');
}

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'请填写应用名称','type'=>'text','key'=>'subject','hint'=>'','notempty'=>'请填写应用名称','group'=>''));
$form->add(array('label'=>'权限组','type'=>'text','key'=>'key9','hint'=>'默认只有超级管理员可以看到，如不熟悉请勿填写','notempty'=>'','group'=>''));
if(strpos(onez($item['token'])->path,'/myplugins/')!==false){
  $form->add(array('label'=>'您已将此应用标记为二次开发','type'=>'html','html'=>'当前所在目录:<code>/myplugins/'.$item['token'].'</code>'));
  
}
$form->add(array('label'=>'隐藏这个应用的后台管理菜单','type'=>'checkbox','key'=>'is_hidemenu','hint'=>'','notempty'=>'','group'=>''));


#处理提交
if($onez=$form->submit()){
  ob_clean();
  $G['this']->data()->open('addons')->update($onez,"id='$id'");
  onez('onez')->update_addons();
  onez()->ok('操作成功','reload2');
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
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary">
          <?php echo $btnname?>
        </button>
        <?php if(strpos(onez($item['token'])->path,'/myplugins/')===false){?>
        <button type="button" onclick="_develop('<?php echo $item['id'];?>')" class="btn bg-black">
          我要二次开发这个应用
        </button>
        <?php }?>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<script type="text/javascript">
function _develop(id){
  var html='';
  html+='<p>您确定要二次开发这个应用吗？（点击确定前，请您务必慎重考虑！）</p>';
  html+='<p></p>';
  html+='<p></p>';
  html+='<p>1、此应用将会移至网站的<code>/myplugins/<?php echo $item['token']?></code>目录下，您可以任意修改，不再受一键更新的影响。同时也不能保持与官方最新版一致。</p>';
  html+='<p>2、由二次开发带来的任何问题，您将独立承担。</p>';
  html+='<p>3、二次开发的应用不再享受技术支持。</p>';
  html+='<p></p>';
  html+='<p></p>';
  html+='<p>如继续，表示您已接受以上几点并清楚可能带来的风险，确定准备好了吗？</p>';
  onez.confirm(html,function(){
    $.post(window.location.href,{action:'develop',id:id},function(data){
      onez.doit(data);
    },'json');
  });
}
</script>
<?php
echo $form->js();
onez('admin')->footer();
?>