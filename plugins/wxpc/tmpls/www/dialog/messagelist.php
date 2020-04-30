<?php

/* ========================================================================
 * $Id: messagelist.php 697 2020-04-30 14:36:03Z onez $
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
#左侧消息列表
$A['title']='对话消息列表';
$upid=onez()->gp('upid');
if(!$upid){
  $userid=onez()->gp('userid');
  if(is_numeric($userid)){
    $upid='dialog.'.$userid;
  }else{
    $upid=$userid;
  }
}
$T=onez('factory.im')->readMessageList($upid);
$A['hasMore']=$G['pagesize']>0 && count($T)>=$G['pagesize'];
if($G['page']>1){
  
}else{
  $record[]=array(
    'type'=>'goto',
    'goto'=>'bottom',
  );
}
$T=array_reverse($T);
foreach($T as $rs){
  $record[]=$rs;
}
