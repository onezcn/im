<?php

/* ========================================================================
 * $Id: upgrade.php 7468 2017-10-12 16:53:41Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/cloud/upgrade.php'));
$action=onez()->gp('action');
if($action=='message.view'){
  $plugins_info=onez('cache')->get('plugins.status.'.$G['this']->token);
  unset($plugins_info['message']);
  onez('cache')->set('plugins.status.'.$G['this']->token,$plugins_info);
  onez()->ok(1);
}
if($action=='upgrade'){
  $plugins_info=onez('cache')->get('plugins.status.'.$G['this']->token);
  $plugins=array();
  $glob=glob(ONEZ_ROOT.ONEZ_NODE_PATH.'/*');
  if($glob){
    foreach($glob as $v){
      $name=basename($v);
      if(file_exists("$v/$name.php")){
        if(!in_array($name,$plugins)){
          $plugin=$plugins_info['all'][$name];
          $plugins[]="$name\t$plugin[hash]";
        }
      }
    }
  }
  $plugins=implode("\n",$plugins);
  $json=onez('onez')->post(array('action'=>'upgrade','format'=>'json','version'=>$G['this']->open('version'),'plugins'=>$plugins));
  $A=array('status'=>'ok');
  //$A['result']=$json;
  #是否可以关闭持续更新
  if($json['nocheck']){
    onez('cache')->cookie('onez_nocheck',1);
  }elseif($json['focuscheck']){
    onez('cache')->cookie('onez_nocheck','del');
  }
  #是否安装了新的插件
  if($json['has_addon']){
    onez('cache')->cookie('has_addon',1);
  }
  if($json['plugin_save']){
    onez('cache')->set('plugins.status.'.$G['this']->token,array(
      'all'=>$json['plugin_all'],
      'new'=>$json['plugin_new'],
      'message'=>$json['message'],
    ));
  }
  onez()->output($A);
}elseif($action=='upgrade.ok'){
  onez('showmessage')->success('一键更新成功',onez('onez')->href('/onez/cloud/upgrade.php'));
}
$G['title']='一键更新';
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
function _del_allfiles($path){
  if(strpos(ONEZ_ROOT,'/git/onezblue')!==false){
    return;
  }
  $glob=glob("$path/*");
  if($glob){
    foreach($glob as $v){
      if(is_dir($v)){
        _del_allfiles($v);
      }else{
        @unlink($v);
      }
    }
    @rmdir($path);
  }
}
$ptokens=array();
$glob=glob(ONEZ_ROOT.ONEZ_NODE_PATH.'/*');
foreach($glob as $v){
  $ptoken=basename($v);
  if(onez()->exists($ptoken)){
    $ptokens[$ptoken]=onez($ptoken)->config['version'];
  }
}
$json=onez('onez')->post(array('action'=>'upgrade_check','format'=>'json','ptokens'=>json_encode($ptokens)));

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
if($json['error']){
  $form->add(array('label'=>'','type'=>'html','html'=>'<pre>'.$json['error'].'</pre>'));
}
if($json['ptoken']){
  $html=array();
  foreach($json['ptoken'] as $ptoken=>$v){
    $ver1=onez($ptoken)->config['version'];
    !$ver1 && $ver1='1.0';
    $ver2=$v['version'];
    $xxx=$v['xxx'];
    $html[]="<code style='width:160px;display:inline-block'>$ptoken</code>\t当前:$ver1\t最新:$ver2\t$xxx";
  }
  $form->add(array('label'=>'需要升级的文件','type'=>'html','html'=>'<pre>'.implode("\n",$html).'</pre>'));
}
$form->add(array('label'=>'<span style="font-weight:normal">我已经做好了相关文件的备份工作</span>','type'=>'checkbox','key'=>'agreement_0'));
$form->add(array('label'=>'<span style="font-weight:normal">认同官方的更新行为并自愿承担更新所存在的风险</span>','type'=>'checkbox','key'=>'agreement_1'));
$form->add(array('label'=>'<span style="font-weight:normal">认同“购买佳蓝云引擎商业授权后进行商业化运营”的协议</span>','type'=>'checkbox','key'=>'agreement_3'));


#处理提交
if($onez=$form->submit()){
  set_time_limit(0);
  $agreement_0=(int)onez()->gp('agreement_0');
  $agreement_1=(int)onez()->gp('agreement_1');
  $agreement_2=(int)onez()->gp('agreement_2');
  $agreement_3=(int)onez()->gp('agreement_3');
  if(!$agreement_0 || !$agreement_1 || !$agreement_3){
    onez('showmessage')->error('抱歉，更新前请仔细阅读更新协议！');
  }
  if($json['error']){
    onez('showmessage')->error($json['error']);
  }
  if(strpos(ONEZ_ROOT,'/git/onezblue')!==false){
    onez('showmessage')->success('当前网站所有文件已经是最新版了，无需更新',onez('onez')->href('/onez/cloud/upgrade.php'));
  }
  
  onez('files')->clear()->find(ONEZ_ROOT.ONEZ_NODE_PATH);
  $files=onez('files')->files();
  
  #内核文件
  $post=array(
    'action'=>'upgrade',
    'charset'=>$G['charset'],
    'platform'=>$G['platform'],
    'version'=>$G['version'],
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://www.onezphp.com/api/usersite.php');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $content = curl_exec($ch);
  curl_close($ch);
  $json=json_decode($content,1);
  if($json['error']){
    onez('showmessage')->error($json['error']);
  }
  foreach($json['files'] as $file=>$data){
    #备份旧的
    if(file_exists(ONEZ_ROOT.'/'.$file)){
      $files['_system/'.$file]=onez()->read(ONEZ_ROOT.'/'.$file);
    }
    $data=base64_decode($data);
    onez()->mkdirs(dirname(ONEZ_ROOT.'/'.$file));
    file_put_contents(ONEZ_ROOT.'/'.$file,$data);
    if(!file_exists(ONEZ_ROOT.'/'.$file)){
      onez('showmessage')->error('请确认你的安装程序目录有写入权限. 多次安装失败, 请访问论坛获取解决方案！');
    }
  }
  
  $file='/cache/plugins/onez/upgrade/'.date('Y/md/').uniqid().'.zip';
  $data=onez('files')->zip()->data;
  onez()->write(ONEZ_ROOT.$file,$data);
  #保存回滚记录
  $G['this']->data()->open('upgrade')->insert(array(
    'file'=>$file,
    'userid'=>$G['userid'],
  ));
  #更新fetch
  #删除旧文件，使所有文件启动更新下载
  _del_allfiles(ONEZ_ROOT.ONEZ_NODE_PATH);
  onez()->location(onez('onez')->href('/onez/cloud/upgrade.php?action=upgrade.ok'));
}
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
      <?php echo $G['title'];?>
    </li>
  </ol>
</section>
<section class="content">
<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> 更新时请注意备份网站数据和相关数据库文件！官方不强制要求用户跟随官方意愿进行更新尝试！
	</div>
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
      </div>
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary" onclick="$('#progress').show()">
          立即更新
        </button>
        <a href="<?php echo onez('onez')->href('/onez/cloud/upgrade.backup.php')?>" data-width="1000" class="onez-miniwin btn btn-success">
          更新回滚列表
        </a>
      </div>
      <div id="progress" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" style="width:100%;display: none;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">正在升级，请耐心等待...</div>
    </div>
  </form>
</section>
<?php
onez('admin')->footer();
?>