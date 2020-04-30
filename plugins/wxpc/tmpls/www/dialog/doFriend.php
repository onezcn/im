<?php

/* ========================================================================
 * $Id: doFriend.php 2156 2020-04-30 17:34:43Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#好友相关操作
#----------模板加载模式开始--------------
if($MODE=='TEMPLATE'){
  $TPL['records']=false;
  return $MODE;
}
#----------模板加载模式结束--------------
$friendId=onez()->gp('userid');
$user=$G['this']->user($friendId);
$method=onez()->gp('method');
$ac=onez()->gp('ac');
if($method=='add'){
  $is=onez('factory.im')->isFriend($G['userid'],$friendId);
  if(2&$is){#
    return $G['this']->show('对方已经是您的好友了');
  }
  if($user['addfriend']=='all'){#所有人都可以添加我为好友
    $extra=array(
      'add-from'=>$G['userid'],
      'add-type'=>'search',
    );
    onez('factory.im')->addFriend($G['userid'],$friendId,$extra);
    onez('factory.im')->addFriend($friendId,$G['userid'],$extra);
    return $G['this']->show('添加好友成功');
  }elseif($user['addfriend']=='close'){#禁止任何人添加我为好友
    return $G['this']->show('对方拒绝了您的好友申请');
  }
  $A['title']='申请好友';
  #初始化表单
  $form=onez('factory.form')->widget('form')
    ->set('btnname','发送申请')
    ->set('values',$item)
  ;
  $form->add(array('type'=>'hidden','key'=>'ac','value'=>'save'));
  $form->add(array('type'=>'hidden','key'=>'userid','value'=>$friendId));
  $form->add(array('type'=>'hidden','key'=>'method','value'=>$method));
  $form->add(array('label'=>'','type'=>'textarea','key'=>'request','hint'=>'验证信息','notempty'=>'','default'=>'','group'=>''));
  if($onez=$form->submit('ac')){
    ob_clean();
    $A['status']='success';
    $A['message']='请求已发出';
    $A['exit']='1';
    $A['goto']='close';
    onez('factory.im')->addMessageRequest($friendId,$G['userid'],$onez['request']);
    onez()->output($A);
  }
  $form->btns[]=array(
    'name'=>'关闭',
    'class'=>'btn-default',
    'data-token'=>'close',
  );
  $record[]=array(
    'type'=>'doFriend',
    'nickname'=>$user['nickname'],
    'avatar'=>$G['this']->avatar($friendId),
    'form'=>$form->code2(),
  );
}elseif($method=='addGroup'){
  $G['title']='申请加群';
}elseif($method=='agree'){
  $G['title']='添加好友';
}