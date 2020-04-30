<?php

/* ========================================================================
 * $Id: message.php 2690 2020-04-30 18:50:38Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$type=onez()->gp('type');
if($type=='read'){
  $msgId=onez()->gp('msgId');
  $update=array('msglist');
  if($msgId){
    #当前对话id
    $toUserId=onez()->gp('toUserId');
    $r=onez('factory.im')->readMessage($msgId);
    if($r){
      $msgUserId='';
      $isMsg=false;
      if($r['token']=='dialog'){#私聊
        $isMsg=true;
        $msgUserId='dialog.'.$r['friendId'];
      }elseif(strpos($r['token'],'dialog.')!==false){#私聊
        $isMsg=true;
        $msgUserId=$r['token'];
        $r['friendId']=substr($r['friendId'],6);
      }elseif(strpos($r['token'],'group.')!==false){#群聊
        $isMsg=true;
        $msgUserId=$r['token'];
        $r['friendId']=$r['fromUserId'];
        $r['message']['from']=($r['fromUserId']==$G['userid']?'my':'other');
        if($r['message']['from']=='my'){
          return;
        }
      }elseif($r['token']=='system'){#系统通知
        if(strpos($r['summary'],'好友')!==false){#更新好友列表
          $update[]='userlist';
        }
      }else{
        print_r($r);exit();
        return;
      }
      if($isMsg){
        if($toUserId==$msgUserId){#当前窗口
          $update=array();#不更新列表
          $r['message']['msg']['id']=$r['id'];
          $r['message']['msg']['userinfo']=array(
            'userid'=>$r['friendId'],
            'nickname'=>$G['this']->user($r['friendId'],'nickname'),
            'avatar'=>$G['this']->avatar($r['friendId']),
          );
          $A['screenMsg']=$r['message'];
        }else{
          $A['sound']='msg';
        }
      }else{
        $A['sound']='msg';
      }
    }
  }
  $update && $A['update']=array('action'=>implode('|',$update));
}elseif($type=='active'){#将一个对话消息提到最前
  $userid=onez()->gp('userid');
  if(is_numeric($userid)){
    $userid='dialog.'.$userid;
  }
  onez('factory.im')->activeDialog($userid);
}elseif($type=='delete'){#删除一组消息
  $msgGId=(int)onez()->gp('msgGId');
  onez('factory.im')->deleteMessageGroup($msgGId);
}elseif($type=='delete2'){#删除一组消息
  $msgId=(int)onez()->gp('msgId');
  onez('factory.im')->deleteMessage($msgId);
}elseif($type=='send'){#发送消息
  $toUserId=onez()->gp('toUserId');
  $msgtype=onez()->gp('msgtype');
  if(is_numeric($toUserId)){
    $toUserId='dialog.'.$toUserId;
  }
  if(strpos($toUserId,'dialog.')!==false){
    $toUserId=substr($toUserId,7);
    onez('factory.im')->addMessageUser($toUserId,$_REQUEST['content'],$msgtype);
  }elseif(strpos($toUserId,'group.')!==false){
    $toUserId=substr($toUserId,6);
    onez('factory.im')->addMessageGroup($toUserId,$_REQUEST['content'],$msgtype);
  }
}
