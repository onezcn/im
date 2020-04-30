<?php

/* ========================================================================
 * $Id: upload.php 4160 2017-10-15 04:16:02Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/union/upload.php'));
$action=onez()->gp('action');
$agreement=array();
$agreement[]='我同意将当前站点的所有数据上传至佳蓝服务器';
$agreement[]='我同意其他人使用这些数据做为测试或正式使用';
$agreement[]='我确认数据中没有涉及隐私或私有化的内容，如账号、密码、手机号等其他敏感信息';
$agreement[]='我同意将当前站点做为演示站点';

if($action){
  set_time_limit(0);
  foreach($agreement as $k=>$v){
    $agreement=(int)onez()->gp('agreement_'.$k);
    if(!$agreement){
      onez('showmessage')->error('抱歉，请仔细阅读更新协议！');
    }
  }
  $post=$_POST;
  $post['action']='sell.submit';
  $post['format']='json';
  $post['siteptoken']=$G['this']->token;
  if($G['this']->data_alias){
    $post['data']=serialize(onez('onez')->export($G['this']->token,0,'',false,$G['this']->data_alias));
  }else{
    $post['data']=serialize(onez('onez')->export($G['this']->token));
  }
  $json=onez('onez')->post($post);
  if($json['error']){
    onez('showmessage')->error($json['error']);
  }
  onez()->location(onez('onez')->href('/onez/union/sell.php'));
}
$G['title']='寄售产品';
$ptokens=array();
foreach(onez('call')->plugins() as $p){
  $ptokens[]=$p->token;
}
$json=onez('onez')->post(array('action'=>'sell.init','format'=>'json','ptokens'=>implode(',',$ptokens)));
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$json)
;
$form->add(array('type'=>'hidden','key'=>'action','value'=>'sell'));
$form->add(array('type'=>'hidden','key'=>'ptokens','value'=>implode(',',$ptokens)));
$form->add(array('label'=>'产品名称','type'=>'text','key'=>'name','hint'=>'','notempty'=>'请填写产品名称','group'=>''));
$form->add(array('label'=>'产品描述','type'=>'textarea','key'=>'summary','hint'=>'','notempty'=>'请填写产品描述','group'=>''));
$form->add(array('label'=>'成本价格(所包含的商业应用的总价格)','type'=>'html','html'=>'<code>'.$json['price_addons'].'</code> 元'));
$form->add(array('label'=>'手续费','type'=>'html','html'=>'<code>'.$json['fee'].'</code> %'));
$form->add(array('label'=>'计算公式','type'=>'html','html'=>'<code>您收到的最终金额</code> ＝ (<code>寄售价格</code> － <code>成本价格</code>) × <code>'.(100-$json['fee']).'%</code>'));
$form->add(array('label'=>'寄售价格','type'=>'text','key'=>'price','hint'=>'请勿低于成本价格','notempty'=>'请填写寄售价格','group'=>''));

foreach($agreement as $k=>$v){
  $form->add(array('label'=>'<span style="font-weight:normal">'.$v.'</span>','type'=>'checkbox','key'=>'agreement_'.$k));
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
		<i class="fa fa-exclamation-triangle"></i> 本功能主要用于开发者、或者不再使用此站的用户出售自己的网站。如果此网站正在运营，为了避免您的数据泄漏，请勿寄售！
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
        <p style="color:red">注：其他用户购买后获得的数据，以寄售时间的数据为准，后期产生的数据不会包含在里面。</p>
      </div>
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary" onclick="$('#progress').show()">
          确定寄售
        </button>
      </div>
    </div>
  </form>
</section>
<?php
onez('admin')->footer();
?>