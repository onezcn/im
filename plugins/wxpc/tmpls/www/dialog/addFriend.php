<?php

/* ========================================================================
 * $Id: addFriend.php 867 2020-04-27 16:57:58Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#----------模板加载模式开始--------------
if($MODE=='TEMPLATE'){
  $TPL['records']=false;
  return $MODE;
}
#----------模板加载模式结束--------------
$A['title']='查找好友';
#初始化表单
$form=onez('factory.form')->widget('form')
  ->set('btnname','查找')
  ->set('values',$item)
;
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'','type'=>'text','key'=>'kw','hint'=>'用户昵称或ID','notempty'=>'关键词不能为空','default'=>'','group'=>''));
if($onez=$form->submit()){
  ob_clean();
  $A['status']='success';
  $A['exit']='1';
  $A['token']='page';
  $A['target']='win';
  $A['width']='800';
  $A['height']='600';
  $A['action']='findFriend&kw='.urlencode($onez['kw']);
  onez()->output($A);
  return;
}

$record[]=$form->code2();
