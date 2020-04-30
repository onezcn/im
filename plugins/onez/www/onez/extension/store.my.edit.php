<?php

/* ========================================================================
 * $Id: store.my.edit.php 5183 2017-10-12 14:03:10Z onez $
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
if($id){
  $item=$G['this']->data()->open('addons')->one("id='$id'");
  $G['title']='编辑';
  $btnname='保存修改';
}else{
  $G['title']='创建我的应用';
  $btnname='立即创建';
}


#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'应用名称','type'=>'text','key'=>'subject','hint'=>'','notempty'=>'请填写应用名称','group'=>''));
if($id){
  $form->add(array('label'=>'唯一标识','type'=>'html','html'=>'<pre>'.$item['ptoken'].'</pre>'));
}else{
  $form->add(array('label'=>'唯一标识 (只允许英文、数字、下划线(_)和点(.),且必须是英文字母开头<p style="color:red">创建后不可修改</p>','type'=>'text','key'=>'ptoken','hint'=>'','notempty'=>'唯一标识不能为空','group'=>'','data-ajax'=>'//fanyi.onezphp.com/?_m=ajax&by=subject','data-inputs'=>'subject'));
  $plugin_tpls=onez('onez')->post(array('action'=>'plugin.tpls','format'=>'json'));
  if($plugin_tpls['options']){
    $form->add(array('label'=>'应用模板','type'=>'select','key'=>'tpl','options'=>$plugin_tpls['options']));
  }
}
#处理提交
if($onez=$form->submit()){
  ob_clean();
  if($id){
    //$onez['key11']=$item['ptoken'];
    $G['this']->data()->open('addons')->update($onez,"id='$id'");
  }else{
    $onez['index1']=1;
    $onez['key10']='none';
    $onez['index12']=1;
    $ptoken=$onez['ptoken']=onez()->getToken($onez['ptoken']);
    !$ptoken && onez()->error('唯一标识有误');
    #onez()->exists($ptoken)&& onez()->error('此标识已存在');
    $G['this']->data()->open('addons')->rows("token='$ptoken'")>0 && onez()->error('此标识已在应用中心存在');
    if(!onez()->exists($ptoken)){
      $res=onez('onez')->post(array('action'=>'plugin.create','format'=>'json','ptoken'=>$ptoken,'tpl'=>$onez['tpl'],'pname'=>$onez['subject']));
      $res['error'] && onez()->error($res['error']);
      $onez['ptoken']=$res['ptoken'];
      $canWrite=1;
      $files=array();
      if($res['files']){
        foreach($res['files'] as $file=>$data){
          $file=trim($file,'./ ');
          $data=base64_decode($data);
          $filepath=ONEZ_ROOT.ONEZ_MYNODE_PATH.'/'.$file;
          onez()->write($filepath,$data);
          if(!file_exists($filepath)){
            $canWrite=0;
          }
          $files[trim(ONEZ_MYNODE_PATH,'/').'/'.$file]=$data;
        }
        
      }
    }
    $onez['token']=$onez['ptoken'];
    $onez['key11']=$onez['ptoken'];
    $G['this']->data()->open('addons')->insert($onez);
    if(!$canWrite){
      onez()->write(ONEZ_CACHE_PATH.'/tmp/myplugin.zip',onez('zip')->zip($files)->data);
      onez()->ok('创建应用成功，但'.ONEZ_MYNODE_PATH.'目录无法写入文件，请下载后手动上传<br /><br />下载后需刷新浏览器才能看到新创建的应用',ONEZ_CACHE_URL.'/tmp/myplugin.zip');
    }
    onez()->ok('创建应用成功，','reload2');
  }
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
  <div class="row">
    <div class="col-md-7 col-xs-7">
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
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
    </div>
    <div class="col-md-5 col-xs-5">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">
            应用说明
          </h3>
          <div class="box-tools pull-right">
          </div>
        </div>
        <div class="box-body">
          <p>1、应用创建后存储在/myplugins中</p>
          <p>2、一旦创建成功，只能通过FTP手动修改代码，云引擎不会修改或删除/myplugins中的任何文件</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
echo $form->js();
onez('admin')->footer();
?>