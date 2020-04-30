<?php

/* ========================================================================
 * $Id: factory.im.php 21326 2020-04-30 20:41:42Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：即时通讯组件
#标识：factory.im

class onezphp_factory_im extends onezphp{
  var $siteId='';
  var $server='ws://www.onez.cn:20201';
  var $serverS='ws://www.onez.cn:20202';
  var $apiurl='http://www.onez.cn:20203/api';
  function __construct(){
    
  }
  /**
  * 初始化
  * @param undefined $opt
  * 
  * @return
  */
  function init($opt){
    global $G;
    $this->siteId=md5(__FILE__."\t".$G['this']->token."\t".$G['this']->get_setting('imkey'));
    $opt['server'] && $this->server=$opt['server'];
    $opt['serverS'] && $this->serverS=$opt['serverS'];
    $opt['serapiurlverS'] && $this->apiurl=$opt['apiurl'];
    return $this;
  }
  function pc(){
    global $G;
    if(!$this->siteId){
      return '<!-- 【ERROR】即时通信模块未初始化 -->';
    }
    $html=array();
    $html[]=onez('ui')->js($this->url.'/js/reconnecting-websocket.min.js');
    $html[]=onez('ui')->js($this->url.'/js/im.js');
    $html[]=onez('ui')->js($this->url.'/js/base64.js');
    $option=array();
    if($_SERVER['HTTPS']=='on'){
      $option['server']=$this->serverS;
    }else{
      $option['server']=$this->server;
    }
    $option['siteId']=$this->siteId;
    $option['udid']=md5($this->siteId."\t$G[userid]\t$_SERVER[HTTP_USER_AGENT]\t".onez()->ip());
    $option['userid']=$G['userid'];
    $option['resurl']=$this->url;
    $option=json_encode($option);
    $html[]=<<<ONEZ
<script type="text/javascript">
onez.im=new onez.IM($option);
</script>
ONEZ;
    return implode("\n",$html);
  }
  /**
  * 直接添加对方为好友
  * @param undefined $userId
  * @param undefined $friendId
  * 
  * @return
  */
  function addFriend($userId,$friendId,$extra=array()){
    global $G;
    $is=$this->isFriend($userId,$friendId);
    if(!(1&$is)){#我没有添加对方为好友
      $item=$extra?$extra:array();
      $item['userid']=$userId;
      $item['upid']=$friendId;
      $G['this']->data()->open('im.friends')->insert($item);
      $this->addMessageSystem($friendId,$G['this']->user($userId,'nickname').'已添加你为好友！');
      $this->addMessageSystem($userId,'你已添加'.$G['this']->user($friendId,'nickname').'为好友！');
      return true;
    }
  }
  //创建群组
  function addGroup($uname,$uids){
    global $G;
    $uids=$uids?explode(',',$uids):array();
    if(!in_array($G['userid'],$uids)){
      $uids[]=$G['userid'];
    }
    $item=array();
    $item['userid']=$G['userid'];
    $item['index1']=$G['userid'];
    $item['key1']=$uname;
    $gid=$G['this']->data()->open('im.groups')->insert($item);
    foreach($uids as $uid){
      $item=array();
      $item['upid']=$gid;
      $item['userid']=$uid;
      $G['this']->data()->open('im.groups.users')->insert($item);
    }
    $uids && $this->addMessageSystemDialog($uids,$G['this']->user($G['userid'],'nickname').'邀请你加入群聊',"group.$gid");
    return $gid;
  }
  //添加新用户
  function addGroupUsers($groupId,$uids){
    global $G;
    $uids=$uids?explode(',',$uids):array();
    foreach($uids as $uid){
      $T=$G['this']->data()->open('im.groups.users')->one("upid='$groupId' and userid='$uid'");
      if(!$T){
        $item=array();
        $item['upid']=$groupId;
        $item['userid']=$uid;
        $G['this']->data()->open('im.groups.users')->insert($item);
        
      }
    }
    $uids && $this->addMessageSystemDialog($uids,$G['this']->user($G['userid'],'nickname').'邀请你加入群聊',"group.$groupId");
    return $groupId;
  }
  //删除群成员
  function delGroupUsers($groupId,$uids){
    global $G;
    $uids=$uids?explode(',',$uids):array();
    if($uids){
      $uids=implode(',',$uids);
      $G['this']->data()->open('im.groups.users')->delete("upid='$groupId' and userid in ($uids)");
      $G['this']->data()->open('im.message')->delete("token='group.$groupId' and userid in ($uids)");
      $G['this']->data()->open('im.message.group')->delete("key12='group.$groupId' and userid in ($uids)");
    }
    #没有用户了
    $n=$G['this']->data()->open('im.groups.users')->rows("upid='$groupId'");
    if($n==0){
      $G['this']->data()->open('im.groups')->delete("id='$groupId'");
    }
  }
  //删除好友
  function delFriend($userId){
    global $G;
    $gKey='';
    if(strpos($userId,'dialog.')!==false){
      $gKey=$userId;
      $userId=substr($userId,7);
      $G['this']->data()->open('im.friends')->delete("userid='$G[userid]' and upid='$userId'");
    }elseif(strpos($userId,'group.')!==false){
      $gKey=$userId;
      $groupId=substr($userId,6);
      $G['this']->data()->open('im.groups.users')->delete("userid='$G[userid]' and upid='$groupId'");
    }elseif(is_numeric()){
      $gKey='dialog.'.$userId;
      $G['this']->data()->open('im.friends')->delete("userid='$G[userid]' and upid='$userId'");
    }
    if($gKey){
      $g=$G['this']->data()->open('im.message.group')->one("userid='$G[userid]' and key12='$gKey'");
      if($g){
        $this->deleteMessageGroup($g['id']);
      }
    }
  }
  /**
  * 增加消息（通用）
  * @param undefined $userId
  * @param undefined $req
  * 
  * @return
  */
  function addMessage($userId,$req){
    global $G;
    $groupToken=$req['type'];
    if(strpos($groupToken,'dialog.')!==false){
      $req['type']='dialog';
    }
    #消息分组
    $g=$G['this']->data()->open('im.message.group')->one("userid='$userId' and key12='$groupToken'");
    
    $newNum=$req['isread']?0:1;
    
    if(!$g){
      $item=array();
      $item['userid']=$userId;
      $item['key12']=$groupToken;
      $item['token']=$item['type']=$req['type'];
      $item['key1']=$item['subject']=$req['subject'];
      $item['key2']=$item['summary']=$req['summary'];
      $item['key11']=$req['msgKey'];
      $item['index1']=$newNum;
      $item['msgtime']=time();
      $gid=$G['this']->data()->open('im.message.group')->insert($item);
    }else{
      $gid=$g['id'];
      $item=array();
      $req['subject'] && $item['key1']=$item['subject']=$req['subject'];
      $item['key2']=$item['summary']=$req['summary'];
      $item['updatetime']=time();
      $item['msgtime']=time();
      
      $n=$G['this']->data()->open('im.message')->rows("upid='$g[id]' and index2=0");
      $item['index1']=$n+$newNum;
      $G['this']->data()->open('im.message.group')->update($item,"id='$g[id]'");
    }
    $item=$req;
    $item['upid']=$gid;
    $item['userid']=$userId;
    $item['token']=$req['type'];
    $item['key1']=$req['subject'];
    $item['key2']=$req['summary'];
    $item['index1']=$req['push']?1:0;
    $item['index2']=$req['isread']?1:0;#是否已读
    $item['key11']=$req['msgKey'];
    $msgId=$G['this']->data()->open('im.message')->insert($item);
    if($req['push']){#推送
      $this->push($userId,$req['push'],$req['summary'],$msgId);
    }
    return $msgId;
  }
  /**
  * 推送通知
  * @param undefined $userId
  * @param undefined $subject
  * @param undefined $message
  * @param undefined $msgId
  * 
  * @return
  */
  function push($userId,$subject,$message,$msgId=0){
    global $G;
    #websocket
    $userId=strval($userId);
    if($this->apiurl){
      $post=array(
        'action'=>'call',
        'method'=>'sendmsg',
        'req'=>array(
          'to'=>'all',
          'siteId'=>$this->siteId,
          'data'=>array(
            'action'=>'message',
            'fromUserId'=>$G['userid'],
            'msgId'=>$msgId,
          ),
        ),
      );
      if($userId=='all'){#所有人
        $post['req']['to']='all';
      }elseif(strpos($userId,'group.')!==false){#
        $post['req']['to']='scene';
        $post['req']['sceneId']=substr($userId,6);
        $post['req']['data']['groupId']=$post['req']['sceneId'];
      }elseif(strpos($userId,'dialog.')!==false){#
        $post['req']['to']='user';
        $post['req']['uids']=substr($userId,7);
      }else{#
        $post['req']['to']='user';
        $post['req']['uids']=$userId;
      }
      $r=onez()->post($this->apiurl,json_encode($post));
      return json_decode($r,1);
    }else{
      return array('status'=>'error','error'=>'即时通信模块未初始化',);
    }
  }
  //激活消息列表
  function activeDialog($groupToken){
    global $G;
    if(strpos($groupToken,'dialog.')!==false){
      $friendId=substr($groupToken,7);
      $user=$G['this']->user($friendId);
      if(!$user){
        return;
      }
      $req=array(
        'type'=>'dialog',
        'subject'=>$G['this']->user($friendId,'nickname'),
        'friendId'=>$friendId,
      );
    }elseif(strpos($groupToken,'group.')!==false){
      $gId=substr($groupToken,6);
      $group=$this->group($gId);
      if(!$group){
        return;
      }
      $req=array(
        'type'=>'group',
        'subject'=>$group['name'],
        'friendId'=>$gId,
      );
    }elseif($groupToken=='system'){
    }else{
      return;
    }
    #消息分组
    $g=$G['this']->data()->open('im.message.group')->one("userid='$G[userid]' and key12='$groupToken'");
    if(!$g){
      $item=$req;
      $item['userid']=$G['userid'];
      $item['key12']=$groupToken;
      $item['token']=$item['type']=$req['type'];
      $item['key1']=$item['subject']=$req['subject'];
      $item['key2']=$item['summary']=$req['summary'];
      $gid=$G['this']->data()->open('im.message.group')->insert($item);
    }else{
      $gid=$g['id'];
      $item=array();
      $item['key1']=$item['subject']=$req['subject'];
      $item['updatetime']=time();
      $G['this']->data()->open('im.message.group')->update($item,"id='$g[id]'");
    }
    
  }
  //读取我的消息
  function readMessageGroupList(){
    global $G;
    $messageList=array();
    $pagelimit=onez('template')->pagelimit(50);
    $T=$G['this']->data()->open('im.message.group')->record("userid='$G[userid]' order by updatetime desc $pagelimit");
    foreach($T as $rs){
      $rs['icon']=$this->url.'/images/icons/default.svg';
      if($rs['token']=='system'){#系统消息
        $rs['icon']=$this->url.'/images/icons/system.svg';
      }elseif($rs['token']=='dialog'){#对话框
        $rs['icon']=$G['this']->avatar($rs['friendId']);
      }elseif($rs['token']=='group'){#对话框
        $group=$this->group($rs['friendId']);
        $rs['icon']=$group['icon'];
      }
      $msg=array(
        'type'=>'msg',
        'msgGId'=>$rs['id'],
        'msgId'=>$rs['index3'],
        'icon'=>$rs['icon'],
        'gToken'=>$rs['key12'],
        'token'=>$rs['token'],
        'subject'=>$rs['subject'],
        'summary'=>$rs['summary'],
        'time'=>$rs['updatetime'],
        'num'=>$rs['index1'],
      );
      $messageList[]=$msg;
    }
    return $messageList;
  }
  //读取我的消息
  function readMessageList($upid){
    global $G;
    $g=$G['this']->data()->open('im.message.group')->one("userid='$G[userid]' and key12='$upid'");
    if(!$g){
      return array();
    }
    $upid=$g['id'];
    
    #清除未读
    $item=array();
    $item['index1']='0';
    $G['this']->data()->open('im.message.group')->update($item,"id='$upid'");
    
    $messageList=array();
    $pagelimit=onez('template')->pagelimit(50);
    $T=$G['this']->data()->open('im.message')->record("userid='$G[userid]' and upid='$upid' order by updatetime desc $pagelimit");
    $ids=array();
    foreach($T as $rs){
      if(!$rs['index2']){
        $ids[]=$rs['id'];
      }
      if(strpos($rs['type'],'group.')!==false){#群组
        $rs['message']['from']=($rs['fromUserId']==$G['userid']?'my':'other');
        $rs['friendId']=$rs['fromUserId'];
      }elseif($rs['token']=='system'){
        if($rs['$extra']['type']=='request'){
          $messageList[]=array(
            'type'=>'sysMsg',
            'msgid'=>$rs['id'],
            'msgtype'=>$rs['$extra']['type'],
            'name'=>$G['this']->user($rs['$extra']['fromUserId'],'nickname'),
            'avatar'=>$G['this']->avatar($rs['$extra']['fromUserId']),
            'time'=>$rs['addtime'],
            'requestCo'=>$rs['$extra']['requestCo'],
            'doTime'=>(int)$rs['$extra']['doTime'],
            'status'=>(string)$rs['$extra']['status'],
            'statusName'=>(string)$rs['$extra']['statusName'],
          );
        }else{
          $messageList[]=array(
            'type'=>'sysMsg',
            'msgid'=>$rs['id'],
            'msgtype'=>'default',
            'time'=>$rs['addtime'],
            'summary'=>$rs['summary'],
          );
        }
        continue;
      }
      $rs['time']=$rs['msgtime']?$rs['msgtime']:$rs['updatetime'];
      $rs['message']['msg']['id']=$rs['id'];
      $rs['message']['msg']['userinfo']=array(
        'userid'=>$rs['friendId'],
        'nickname'=>$G['this']->user($rs['friendId'],'nickname'),
        'avatar'=>$G['this']->avatar($rs['friendId']),
      );
      $messageList[]=$rs['message'];
    }
    #标记已读
    if($ids){
      $ids=implode(',',$ids);
      $item=array();
      $item['index2']=time();
      $G['this']->data()->open('im.message')->update($item,"id in ($ids)");
    }
    return $messageList;
  }
  //读取一条消息
  function readMessage($msgId){
    global $G;
    $T=$G['this']->data()->open('im.message')->one(strlen($msgId)==32?"key11='$msgId'":"id='$msgId'");
    if(!$T){
      return false;
    }
    if(!$T['index']){
      #标记已读
      $item=array();
      $item['index2']=time();
      $G['this']->data()->open('im.message')->update($item,"id='$T[id]'");
      $g=$G['this']->data()->open('im.message.group')->one("id='$T[upid]'");
      if($g['index2']){
        $g['index2']--;
        if($g['index2']<1){
          $g['index2']=0;
        }
        #标记已读
        $item=array();
        $item['index2']=0;
        $G['this']->data()->open('im.message.group')->update($item,"id='$g[id]'");
      }
    }
    
    return $T;
  }
  //
  function setMessageExtra($message,$new){
    global $G;
    if($new){
      $extra=$message['$extra']?$message['$extra']:array();
      $extra=array_merge($extra,$new);
      $item=array();
      $item['$extra']=$extra;
      $G['this']->data()->open('im.message')->update($item,"id='$message[id]'");
    }
  }
  //删除一组消息
  function deleteMessageGroup($msgGId){
    global $G;
    $G['this']->data()->open('im.message.group')->delete("userid='$G[userid]' and id='$msgGId'");
    $G['this']->data()->open('im.message')->delete("userid='$G[userid]' and upid='$msgGId'");
    return $T;
  }
  //删除一条消息
  function deleteMessage($msgId){
    global $G;
    $G['this']->data()->open('im.message')->delete("userid='$G[userid]' and id='$msgId'");
    return $T;
  }
  function siteinfo(){
    #websocket
    if($this->apiurl){
      $post=array(
        'action'=>'call',
        'method'=>'siteinfo',
        'req'=>array(
          'siteId'=>$this->siteId,
        ),
      );
      $r=onez()->post($this->apiurl,json_encode($post));
      return json_decode($r,1);
    }else{
      return array('status'=>'error','error'=>'即时通信模块未初始化',);
    }
  }
  /**
  * 添加系统消息
  * @param undefined $userId
  * @param undefined $message
  * 
  * @return
  */
  function addMessageSystem($userId,$message,$extra=array()){
    $msgId=$this->addMessage($userId,array(
      'type'=>'system',
      'subject'=>'系统通知',
      'summary'=>$message,
      'push'=>$message,
      '$extra'=>$extra,
    ));
    return $msgId;
  }
  function addMessageRequest($userId,$friendId,$request){
    $message='申请添加你为好友';
    $extra=array(
      'type'=>'request',
      'fromUserId'=>$friendId,
      'requestCo'=>$request,
    );
    $msgId=$this->addMessageSystem($userId,$message,$extra);
    return $msgId;
  }
  //发私聊
  function addMessageUser($userId,$content,$msgtype){
    global $G;
    $text='';
    if($msgtype=='text'){
      $text=trim(strip_tags($content['text']));
    }elseif($msgtype=='img'){
      $text='[图片]';
    }
    $text=onez()->substr($text,0,10);
    $message=array(
      'type'=>'user',
      'msg'=>array(
        'id'=>0,
        'type'=>$msgtype,
        'time'=>time(),
        'content'=>$content,
      ),
    );
    //给对方的消息
    $message['from']='other';
    $msgId=$this->addMessage($userId,array(
      'type'=>'dialog.'.$G['userid'],
      'friendId'=>$G['userid'],
      'subject'=>$G['this']->user($G['userId'],'nickname'),
      'summary'=>$text,
      'push'=>$text,
      'message'=>$message,
    ));
    //给自己的消息
    $message['from']='my';
    $msgId=$this->addMessage($G['userid'],array(
      'type'=>'dialog.'.$userId,
      'friendId'=>$userId,
      'subject'=>$G['this']->user($userId,'nickname'),
      'summary'=>$text,
      'push'=>'',//不推送给自己
      'isread'=>'1',//已读
      'message'=>$message,
    ));
    return $msgId;
  }
  //系统消息
  function addMessageSystemDialog($uids,$content,$gKey,$fromUserId=0){
    global $G;
    $uids=is_array($uids)?implode(',',$uids):$uids;
    
    $message=array(
      'type'=>'system',
      'msg'=>array(
        'id'=>0,
        'type'=>'text',
        'time'=>time(),
        'content'=>array('text'=>$content),
      ),
    );
    $msgKey='';
    if(strpos($gKey,'group.')!==false){
      $groupId=substr($gKey,6);
      $msgKey=md5("$groupId\t".uniqid());
      foreach(explode(',',$uids) as $uid){
        $msg=array(
          'type'=>'group.'.$groupId,
          'groupId'=>$groupId,
          'fromUserId'=>$fromUserId,
          'subject'=>'',
          'summary'=>$content,
          'push'=>'',
          'message'=>$message,
          'msgKey'=>$msgKey,
        );
        $msgId=$this->addMessage($uid,$msg);
      }
      $this->push($uids,$content,'',$msgKey);
    }elseif(strpos($gKey,'dialog.')!==false){
      $friendId=substr($gKey,7);
      //给对方的消息
      foreach(explode(',',$uids) as $uid){
        $message['from']='other';
        $msgId=$this->addMessage($uid,array(
          'type'=>'dialog.'.$friendId,
          'friendId'=>$friendId,
          'subject'=>$G['this']->user($friendId,'nickname'),
          'summary'=>$content,
          'push'=>$content,
          'message'=>$message,
        ));
      }
    }
    return $msgKey;
  }
  //发群聊
  function addMessageGroup($groupId,$content,$msgtype){
    global $G;
    $text='';
    if($msgtype=='text'){
      $text=trim(strip_tags($content['text']));
    }
    $text=onez()->substr($text,0,10);
    $message=array(
      'type'=>'user',
      'msg'=>array(
        'id'=>0,
        'type'=>$msgtype,
        'time'=>time(),
        'content'=>$content,
      ),
    );
    $nickname=$G['this']->user($G['userid'],'nickname');
    $msgKey=md5("$groupId\t".uniqid());
    #$msgKey=onez()->strcode("group\t$groupId\t".time(),'ENCODE');
    //给所有人
    $T=$G['this']->data()->open('im.groups.users')->record("upid='$groupId' order by id desc");
    foreach($T as $rs){
      $msg=array(
        'type'=>'group.'.$groupId,
        'groupId'=>$groupId,
        'fromUserId'=>$G['userid'],
        'subject'=>'',
        'summary'=>$text,
        'push'=>'',
        'message'=>$message,
        'msgKey'=>$msgKey,
      );
      if($rs['userid']==$G['userid']){
        $msg['isread']=1;
      }
      $msgId=$this->addMessage($rs['userid'],$msg);
    }
    $this->push('group.'.$groupId,$text,'',$msgKey);
    return $msgKey;
  }
  function isFriend($userId,$friendId){
    global $G;
    $is=0;
    $T=$G['this']->data()->open('im.friends')->one("userid='$userId' and upid='$friendId'");
    if($T){#对方是我的好友
      $is+=1;
    }
    $T=$G['this']->data()->open('im.friends')->one("userid='$friendId' and upid='$userId'");
    if($T){#我是对方的好友
      $is+=2;
    }
    return $is;
  }
  //好友列表
  function friendList(){
    global $G;
    $T=$G['this']->data()->open('im.friends')->record("userid='$G[userid]' order by id desc");
    $list=array();
    foreach($T as $rs){
      if(!$rs['upid']){
        $G['this']->data()->open('im.friends')->delete("id='$rs[id]'");
        continue;
      }
      $list[]=array(
        'friendId'=>$rs['upid'],
        'time'=>$rs['addtime'],
      );
    }
    return $list;
  }
  function group($gId){
    global $G;
    $group=$G['this']->data()->open('im.groups')->one("id='$gId'");
    if($group){
      !$group['name'] && $group['name']=$group['key1'];
      !$group['icon'] && $group['icon']=$this->url.'/images/icons/group.svg';
    }
    return $group;
  }
  //群聊列表
  function groupList(){
    global $G;
    $T=$G['this']->data()->open('im.groups.users')->record("userid='$G[userid]' order by id desc");
    $list=array();
    foreach($T as $rs){
      $group=$this->group($rs['upid']);
      $list[]=array(
        'groupId'=>$group['id'],
        'icon'=>$group['icon'],
        'name'=>$group['key1'],
        'time'=>$rs['addtime'],
      );
    }
    return $list;
  }
  //群成员列表
  function groupUsers($gId){
    global $G;
    $T=$G['this']->data()->open('im.groups.users')->record("upid='$gId' order by id desc");
    $list=array();
    foreach($T as $rs){
      $user=$G['this']->user($rs['userid']);
      $list[]=array(
        'userid'=>$rs['userid'],
        'avatar'=>$G['this']->avatar($rs['userid']),
        'name'=>$user['nickname'],
        'time'=>$rs['addtime'],
      );
    }
    return $list;
  }
  
}