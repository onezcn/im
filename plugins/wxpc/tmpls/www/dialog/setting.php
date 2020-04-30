<?php

/* ========================================================================
 * $Id: setting.php 2123 2020-04-28 12:10:34Z onez $
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
$A['title']='设置';
$item=$G['this']->user($G['userid']);
$item['extra'] && $item=array_merge($item,$item['extra']);
!$item['user_nickname'] && $item['user_nickname']=$item['nickname'];
#初始化表单
$form=onez('factory.form')->horizontal()->widget('form')
  ->set('btnname','保存')
  ->set('values',$item)
;
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

#默认标签
$tab=false;
$form->add(array('label'=>'我的昵称','type'=>'text','key'=>'user_nickname','hint'=>'','notempty'=>'','default'=>'','group'=>''),$tab);
$options=array();
$options['']='保密';
$options['1']='先生';
$options['2']='女士';
$form->add(array('label'=>'我的性别','type'=>'radio','ptoken'=>'radio','key'=>'user_sex','options'=>$options,'notempty'=>'','default'=>'','group'=>''),$tab);
$form->add(array('label'=>'所在城市','type'=>'text','key'=>'user_city','notempty'=>'','default'=>'','group'=>''),$tab);

#好友设置
$tab='个性头像';
$avatar=$G['this']->avatar($G['userid']);
if(strpos($avatar,'?')!==false){
  $o=explode('?',$avatar);
  $avatar=$o[0];
}
$form->add(array('label'=>'我的头像','type'=>'icon','key'=>'avatar','hint'=>'','notempty'=>'','value'=>$avatar,'group'=>''),$tab);

#好友设置
$tab='好友设置';
$options=array();
$options['verify']='需要通过我的验证';
$options['all']='所有人都可以添加我为好友';
$options['close']='禁止任何人添加我为好友';
$form->add(array('label'=>'好友验证','type'=>'select','key'=>'user_addfriend','options'=>$options,'notempty'=>'','default'=>'','group'=>''),$tab);

if($onez=$form->submit()){
  if(!$onez['avatar'] || $onez['avatar']==$avatar){
    unset($onez['avatar']);
  }
  ob_clean();
  $G['this']->user_set($G['userid'],$onez);
  onez()->ok('保存成功','close');
  return;
}

$record[]=$form->code2(array(
  'basename'=>'基础信息',
  'tabSubmit'=>'1',
  'submit'=>false,
));
