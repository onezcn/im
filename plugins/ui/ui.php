<?php

/* ========================================================================
 * $Id: ui.php 4435 2018-04-18 15:24:27Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_ui extends onezphp{
  var $option;
  var $heads=array();
  function init($option=array()){
    if(!$option['type']){
      $option['type']='bootstrap';
    }
    $this->option=$option;
    return $this;
  }
  function header(){
    global $G;
    $html[]='<!DOCTYPE html>';
    $html[]='<html>';
    $html[]='<head>';
    $html[]='<meta charset="utf-8">';
    //$html[]='<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">';
    $html[]='<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9,chrome=1">';
    $html[]='<title>'.($G['title']?$G['title']:'').'</title>';
    $html[]='<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';
    if($this->option['type']=='bootstrap'){
      $html[]=onez('jquery')->head(1);
      $html[]=onez('bootstrap')->head(1);
    }elseif($this->option['type']=='app'){
      $html[]='<meta name="apple-mobile-web-app-capable" content="yes" />';
      $html[]='<meta name="apple-mobile-web-app-status-bar-style" content="black" />';
      $html[]='<meta content="telephone=no" name="format-detection"/>';
    }
    #$html[]='<link rel="stylesheet" href="'.$this->url.'/css/style.css">';
    if($this->heads){
      foreach($this->heads as $head){
        if(!$head){
          continue;
        }
        $html[]=$head;
      }
    }
    if($this->less_list){
      foreach($this->less_list as $less){
        if(!file_exists($less)){
          continue;
        }
        $cacheFile='/cache/css/'.md5($less).'.css';
        if(!file_exists(ONEZ_CACHE_PATH.$cacheFile) || filemtime($less)>filemtime(ONEZ_CACHE_PATH.$cacheFile)){
          $css=onez('less')->tocss($less);
          $baseurl=onez()->homepage().substr($less,strlen(ONEZ_CACHE_PATH),-strlen(basename($less)));
          $css=str_replace('url(','url('.$baseurl,$css);
          onez()->write(ONEZ_CACHE_PATH.$cacheFile,$css);
        }
        $html[]='<link rel="stylesheet" href="'.ONEZ_CACHE_URL.$cacheFile.'?t='.filemtime(ONEZ_CACHE_PATH.$cacheFile).'">';
      }
    }
    $html[]='</head>';
    if($this->option['type']=='bootstrap'){
      $html[]='<body>';
      $html[]='<div class="wrapper">';
    }else{
      $html[]='<body class="user-guest">';
    }
    echo implode("\n",$html);
  }
  function footer(){
    global $G;
    if($this->option['type']=='bootstrap'){
      $html[]='</div>';
    }elseif($this->option['type']=='app'){
      $html[]='<div id="loading"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div><div id="busy"></div>';
    }
    if($G['footer']){
      $html[]=$G['footer'];
    }
    if($G['footer-js']){
      $html[]='<script type="text/javascript">'.$G['footer-js'].'</script>';
    }
    0 && $html[]=<<<ONEZ
<script type="text/javascript">
$('[data-onez-cloud]').each(function(){
  $(this).bind();
});
window.addEventListener('message', function(event){
  if(typeof event.data=='undefined'){
    return;
  }
  if(typeof event.data.key=='undefined'){
    return;
  }
  $('[data-onez-cloud="'+event.data.key+'"]').val(event.data.value);
}, false);
</script>
ONEZ;
    $html[]='</body>';
    $html[]='</html>';
    echo implode("\n",$html);
  }
  function resurl($v){
    if(strpos($v,'?')===false){
      $file=str_replace(onez()->homepage(),ONEZ_ROOT,$v);
      if(file_exists($file)){
        $v.='?t='.filemtime($file);
      }
    }
    $v=str_replace(array('http://','https://'),'//',$v);
    return $v;
  }
  function js(){
    $html=array();
    foreach(func_get_args() as $v){
      $v=$this->resurl($v);
      $html[]='<script type="text/javascript" src="'.$v.'"></script>';
    }
    return implode("\n",$html);
  }
  function css(){
    $html=array();
    foreach(func_get_args() as $v){
      $v=$this->resurl($v);
      $html[]='<link rel="stylesheet" href="'.$v.'" />';
    }
    return implode("\n",$html);
  }
  function tmpl($action){
    list($name,$file)=func_get_args();
    echo '<script id="tmpl-'.$name.'" type="text/x-jquery-tmpl">';
    include($file);
    echo '</script>';
  }
  function less(){
    $html=array();
    !$this->less_list && $this->less_list=array();
    foreach(func_get_args() as $v){
      $this->less_list[]=$v;
      $v=$this->resurl($v);
      $html[]='<link rel="stylesheet/less" href="'.$v.'" />';
    }
    return implode("\n",$html);
  }
}