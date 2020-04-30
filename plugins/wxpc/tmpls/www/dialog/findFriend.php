<?php

/* ========================================================================
 * $Id: findFriend.php 1645 2020-04-27 21:01:37Z onez $
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
$G['title']='查找结果';
$xxx='';
$kw=onez()->gp('kw');
if($kw){
  $kwId=intval($kw);
  $or=array();
  if($kwId){
    $or[]="userid='$kwId'";
  }else{
    
  }
  $or[]="nickname like '%$kw%'";
  $or[]="username like '%$kw%'";
  $xxx.=" and (".implode(' or ',$or).")";
}
$pagelimit=$this->pagelimit(21);
if($G['page']==1){
  #初始化表单
  $form=onez('factory.form')->widget('form')
    ->set('btnname','查找')
    ->set('values',$item)
  ;
  $form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
  $form->add(array('label'=>'','type'=>'text','key'=>'kw','hint'=>'用户昵称或ID','notempty'=>'关键词不能为空','default'=>$kw,'group'=>''));
  if($onez=$form->submit()){
    ob_clean();
    $A['status']='success';
    $A['token']='setAction';
    $A['action']='findFriend&kw='.urlencode($onez['kw']);
    onez()->output($A);
    return;
  }

  $record[]=$form->code2();
}

$T=onez('db')->open('member')->record("1$xxx order by userid desc$pagelimit");

if(!$this->page_init($T,array(
  'noneTip'=>'您要查找的用户不存在',
))){
  return;
}
foreach($T as $rs){
  $record[]=array(
    'type'=>'userlist',
    'avatar'=>$G['this']->avatar($rs['userid']),
    'nickname'=>$rs['nickname']?$rs['nickname']:$rs['username'],
    'sign'=>$rs['sign']?$rs['sign']:'暂无个性签名',
    'token'=>$G['this']->getEvent(array(
      'token'=>'userCard',
      'userid'=>$rs['userid'],
    )),
  );
}