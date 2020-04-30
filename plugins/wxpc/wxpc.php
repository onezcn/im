<?php

/* ========================================================================
 * $Id: wxpc.php 10319 2020-04-30 13:01:05Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：仿微信电脑版 
#标识：wxpc 

onez('site.www');
class onezphp_wxpc extends onezphp_site_www{
  function __construct(){
    define('DATA_ALIAS','system');
  }
  function site_update(){
    global $G;
    $patchFile=$_REQUEST['patchFile'];
    if($patchFile){
      $files=onez('onezip')->unzip($patchFile);
      foreach($files as $k=>$v){
        onez()->write(dirname(__FILE__).'/'.$k,$v);
        if(strpos($k,'db.tables')!==false){
          @unlink(dirname(__FILE__).'/cache/table.init.lock');
        }
      }
      $A['status']='success';
      $A['ip']=onez()->ip();
      $A['message']='更新成功';
      onez()->output($A);
    }
    onez()->error('无效操作');
  }
  function tools_run($key){
    global $G;
    $Key=$key;
    $Key=preg_replace('/[^0-9a-zA-Z_\.]+/i','',$Key);
    $Key=str_replace('.','/',$Key);
    $Key=trim($Key,'/');
    $file=dirname(__FILE__).'/tools/'.$Key.'.php';
    if(file_exists($file)){
      include($file);
    }else{
      $this->error("{$key} not exists");
    }
  }
  function mysql(){
    $type=onez()->gp('type');
    $host=onez()->gp('host');
    $usr=onez()->gp('usr');
    $pwd=onez()->gp('pwd');
    $A=array('status'=>'success');
    $link=@mysql_connect($host,$usr,$pwd,1);
    !$link && onez()->error('连接数据库失败['.mysql_error($link).']');
    if($type=='dbs'){
      $result=mysql_query('SHOW DATABASES',$link);
      $dbs=array();
      while($r=mysql_fetch_array($result,MYSQL_ASSOC)){
        $dbname=$r['Database'];
        if($dbname=='information_schema'){
          continue;
        }
        $dbs[]=$dbname;
      }
      $A['dbs']=$dbs;
      mysql_free_result($result);
    }elseif($type=='tables'){
      $dbname=onez()->gp('dbname');
      $result=mysql_list_tables($dbname,$link);
      $tables=array();
      for($i =0;$i<mysql_num_rows($result);$i++){
        $tablename=mysql_tablename($result,$i);
        $rescolumns = mysql_query("SHOW FULL COLUMNS FROM `$tablename`",$link) ;
        $rows=array();
        while($row = mysql_fetch_array($rescolumns,MYSQL_ASSOC)){
          $rows[$row['Field']]=$row;
        }
        $tables[$tablename]=$rows;
      }
      $A['tables']=$tables;
      mysql_free_result($result);
    }
    mysql_close($link);
    onez()->output($A);
  }
  function bind_m_header(){
    global $G;
    $G['header'].=onez('ui')->js($this->url.'/js/app.js');
    $G['header'].=onez('ui')->css($this->url.'/css/app.css');
  }

/**
  * 用户资料
  * @param Number $userid
  * @param String $field
  * 
  * @return
  */
  function user($userid,$field=false){
    global $G;
    if(!$this->allUsers[$userid]){
      $user=onez('db')->open('member')->one("userid='$userid'");
      if($user){
        #默认设置
        $user['addfriend']='verify';#需要通过我的验证
        !$user['nickname'] && $user['nickname']=$user['username'];
        $T=$G['this']->data()->open('member.extra')->one("userid='$userid'");
        if($T){
          foreach($T as $k=>$v){
            if(strpos($k,'user_')!==false){
              $v && $user[substr($k,5)]=$v;
            }
          }
        }
        $user['extra']=$T;
      }
      $this->allUsers[$userid]=$user;
    }
    if($field!==false){
      return $this->allUsers[$userid][$field];
    }
    return $this->allUsers[$userid];
  }
  function user_set($userid,$onez){
    global $G;
    $base=$extra=array();
    foreach($onez as $k=>$v){
      if($k=='avatar'){
        if($v){
          $this->avatar_set($userid,$v);
        }
        continue;
      }
      if(strpos($k,'user_')!==false){
        $extra[$k]=$v;
      }else{
        $base[$k]=$v;
      }
    }
    if($base){
      onez('db')->open('member')->update($base,"id='$userid'");
    }
    if($extra){
      $T=$G['this']->data()->open('member.extra')->one("userid='$userid'");
      if($T){
        $G['this']->data()->open('member.extra')->update($extra,"id='$T[id]'");
      }else{
        $extra['userid']=$userid;
        $G['this']->data()->open('member.extra')->insert($extra);
      }
    }
  }

  function cloudfont($type=false){
    return $this->url.'/www/css/fonts/cloud-font.ttf?t='.filemtime($this->path.'/www/css/fonts/cloud-font.ttf');
  }

/**
  * 
  * @param undefined $event
  * 
  * @return
  */
  function getEvent($event){
    #是否有本地重写事件
    $eventFile=dirname(__FILE__).'/events/'.$event['token'].'.php';
    if(file_exists($eventFile)){
      $event=include($eventFile);
    }
    if($event['tmpl']){
      $_event=array(
        'token'=>'steps',
        'tokens'=>array($event),
      );
      $_event['tokens'][]=array(
        'token'=>'post',
        'loading'=>'处理中...',
        'action'=>$event['tmpl'],
      );
      $_event['tokens'][]=array(
        'token'=>'doit2',
      );
      $event=$_event;
    }
    return json_encode($event);
  }

/**
  * 获取参数
  * 
  * @param undefined $name
  * 
  * @return
  */
  function get_setting($name){
    global $G;
    #主类内置函数
    if(!$this->_setting){
      if(file_exists(dirname(__FILE__).'/config/setting.php')){
        $this->_setting=include(dirname(__FILE__).'/config/setting.php');
      }
    }
    if($this->_setting){
      if(isset($this->_setting[$name])){
        return $this->_setting[$name];
      }
    }
    
    //其他：后台设置、文件缓存
    return $this->getvar($name);
  }

  function addPageToken($arr){
    global $A;
    if($A['token']){
      if($A['token']!='tokens'){
        $A['tokens']=array(is_string($A['token'])?json_decode($A['token'],1):$A['token']);
        $A['token']='tokens';
      }
      $A['tokens'][]=$arr;
    }else{
      $A['token']=$this->getEvent($arr);
    }
  }

  function addHeaderBtn($mybtns){
    global $A;
    $btns=array();
    $btns['none']=array('name'=>'无');
    $btns['forward']=array('name'=>'前进');
    $btns['back']=array('name'=>'后退');
    $btns['share']=array('name'=>'分享');
    $btns['favorite']=array('name'=>'收藏');
    $btns['home']=array('name'=>'主页');
    $btns['menu']=array('name'=>'菜单');
    $btns['close']=array('name'=>'关闭');
    $_btns=array();
    for($i=0;$i<8;$i++){
      $_btns[]=array('type'=>'none','text'=>'');
    }
    foreach($mybtns as $btn){
      $y=$btn['pos']=='left'?0:1;
      for($i=0;$i<8;$i++){
        if($i%2==$y && $_btns[$i]['type']=='none' && $_btns[$i]['text']==''){
          if($btn['type'] && $btns[$btn['type']]){
            $_btn['type']=$btn['type'];
          }else{
            if($btn['text']){
              $_btn['type']='none';
              $_btn['fontSize']=15;
              $_btn['text']=$btn['text'];
            }
            if($btn['type']=='icon'){
              $_btn['fontSize']=24;
              $_btn['fontSrc']='/static/iconfont.ttf';
            }
          }
          $_btn['float']=$btn['pos']=='left'?'left':'right';
          $_btns[$i]=$_btn;
          if($btn['token']){
            $A['onez.events']['headerBtn'.$i]=$btn['token'];
          }
          break;
        }
      }
    }
    $this->addPageToken(array(
      'token'=>'headerBtn',
      'btns'=>$_btns,
    ));
  }

  function submit(){
    $platform=onez()->gp('platform');
    if($platform=='MP-WEIXIN'){
      return 'formSubmit';
    }
    return 'submit';
  }

  function h5(){
    global $G;
    onez('debug')->showerror();
    include_once(dirname(__FILE__).'/h5/index.php');
  }
  function userCard($userId,$type='card'){
    global $G;
    $user=$this->user($userId);
    #用户卡片
    $usercard=array();
    $usercard['type']='usercard';
    $usercard['nickname']=$user['nickname'];
    $usercard['avatar']=$this->avatar($user['userid']);
    if($user['sex']=='1'){
      $usercard['sex']='<i class="layui-icon layui-icon-male" style="color:#12b7f5"></i>';
    }elseif($user['sex']=='2'){
      $usercard['sex']='<i class="layui-icon layui-icon-female" style="color:#f37e7d"></i>';
    }
    $usercard['summary']='编号: '.$user['userid'];
    $usercard['infos']=array();
    $usercard['infos'][]=array('name'=>'地 区','value'=>$user['city']?$user['city']:'未知',);
    #按钮
    $usercard['btns']=array();
    
    #是否好友
    $is=onez('factory.im')->isFriend($G['userid'],$userId);
    #if($T || $G['userid']==$user['userid']){#对方是我的好友
    if(1&$is){#对方是我的好友
      if($type=='detail'){
        #发消息
        $usercard['btns'][]=array(
          'name'=>'发消息',
          'type'=>'button',
          'classname'=>'btn',
          'token'=>$G['this']->getEvent(array(
            'token'=>'sendTo',
            'userid'=>$user['userid'],
            'uname'=>$usercard['nickname'],
          )),
        );
      }else{
        0 && $usercard['btns'][]=array(
          'name'=>'发送名片',
          'icon'=>'layui-icon layui-icon-release',
          'token'=>$G['this']->getEvent(array(
            'token'=>'sendTo',
            'userid'=>$user['userid'],
            'uname'=>$usercard['nickname'],
          )),
        );
        $usercard['btns'][]=array(
          'name'=>'发消息',
          'icon'=>'layui-icon layui-icon-dialogue',
          'token'=>$G['this']->getEvent(array(
            'token'=>'sendTo',
            'userid'=>$user['userid'],
            'uname'=>$usercard['nickname'],
          )),
        );
      }
    }else{
      #加为好友
      $usercard['btns'][]=array(
        'name'=>'加为好友',
        'type'=>'button',
        'classname'=>'btn',
        'token'=>$G['this']->getEvent(array(
          'token'=>'doFriend',
          'method'=>'add',
          'userid'=>$user['userid'],
          'uname'=>$usercard['nickname'],
        )),
      );
    }
    return $usercard;
  }
  function show($error){
    global $A,$record;
    $record[]=array(
      'type'=>'none',
      'text'=>$error,
    );
    $A['style']=array(
      'width'=>'260px',
      'height'=>'160px',
    );
    return $A;
  }
}
