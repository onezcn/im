<?php

/* ========================================================================
 * $Id: m.plugin.php 2031 2019-09-11 06:19:47Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：移动版扩展
#标识：m.plugin

class onezphp_m_plugin extends onezphp{
  function __construct(){
    
  }
  function action($action){
    return $action.'&myaction=event&ptoken='.$this->token;
  }
  function action_url($ptoken,$action){
    global $G;
    $url_app=$G['this']->homepage().'/m.php';
    $r=onez('call')->call('mobile_url');
    if($r){
      $url_app=$r[0];
    }
    if(strpos($url_app,'?')===false){
      $url_app.='?';
    }else{
      $url_app.='&';
    }
    return $url_app.'action='.$this->action($action);
  }
  function tmpl($action){
    global $A,$record,$G;
    $file=$this->path.'/tmpls/'.$action.'.php';
    if(file_exists($file)){
      include($file);
    }
  }
  function event(&$item,$rs,$pre){
  }
  function background($ptoken=false){
    global $G;
    $background=$this->get('background');
    if($ptoken!==false){
      if(!$background){
        $background=array();
      }
      if(!in_array($ptoken,$background)){
        $background[]=$ptoken;
      }
      $this->set('background',$background);
      return $this;
    }
    $js='';
    foreach($background as $ptoken){
      $js.=onez()->read(onez($ptoken)->path.'/js/background.js');
    }
    //onez()->write(ONEZ_ROOT.'/cache/plugins/m.plugin/background.js',$js);
    return $js;
  }
  function thumb($url,$size=64){
    if(strpos($url,'cdn.onez.cn')!==false){
      return $url.'!w120';
    }
    if(!$url){
      return onez('m.plugin')->url.'/images/nopic.png';
    }
    $thumbFile='/thumbs/'.md5($url.$size).'.jpg';
    if(!file_exists(ONEZ_CACHE_PATH.$thumbFile)){
      $data=onez('image')->load($url)->resize($size,$size)->data();
      onez()->write(ONEZ_CACHE_PATH.$thumbFile,$data);
    }
    return ONEZ_CACHE_URL.$thumbFile.'?t='.@filemtime(ONEZ_CACHE_PATH.$thumbFile);
  }
  function summary($text,$len=30){
    $text=strip_tags($text);
    $text=preg_replace('/[\s\r\n\t]+/is','',$text);
    $text=onez()->substr($text,0,$len);
    return $text;
  }
}