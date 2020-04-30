<?php

/* ========================================================================
 * $Id: template.php 7067 2020-04-30 16:45:31Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：佳蓝模板引擎
#标识：template

class onezphp_template extends onezphp{
  var $tpl=false;
  var $noTpl=true;
  var $Components=array();
  function __construct(){
    $this->tpl=$this;
  }
  function loadTemplate($ptoken){
    $this->tpl=onez($ptoken);
    $this->noTpl=($ptoken==$this->token);
    return $this;
  }
  function header(){
    global $G;
    echo onez('ui')->css($this->url.'/res/layui/css/layui.css');
    echo onez('ui')->css($this->url.'/res/nutui/nutui.min.css');
    echo onez('ui')->css($this->url.'/css/onez.css');
    echo $this->loadComponents();
  }
  function footer(){
    global $G;
    echo onez('ui')->js($this->url.'/res/layui/layui.all.js');
    echo onez('ui')->js($this->url.'/js/vue.min.js');
    echo onez('ui')->js($this->url.'/res/nutui/nutui.min.js');
    echo onez('ui')->js($this->url.'/js/onez.js');
    echo '<script type="text/javascript">onez.start();</script>';
  }
  function pagelimit($pagesize=12){
    global $G;
    $G['pagesize']=$pagesize;
    $G['page']=$page=max(1,(int)onez()->gp('page'));
    return' limit '.(($page-1)*$pagesize).','.$pagesize;
  }
  function page_init($T,$opt=array()){
    global $G,$A,$record;
    $page=max(1,(int)onez()->gp('page'));
    if($page==1){
      if(!$T){#没有数据
        $record[]=array(
          'type'=>'none',
          'text'=>$opt['noneTip']?$opt['noneTip']:'记录不存在',
        );
        return false;
      }
    }
    if(!$G['pagesize']|| count($T)<$G['pagesize']){
      $A['hasMore']=false;
    }else{
      $A['hasMore']=true;
    }
    return true;
  }
  function loadComponents(){
    global $G,$A,$record;
    $css=$html=array();
    $path=onez('template')->get('path');
    $o=explode('/',$path);
    $plat=$o[1];
    $glob=glob($this->tpl->path.'/components/'.$plat.'/*.php');
    if($glob){
      foreach($glob as $v){
        $type=substr(basename($v),0,-4);
        $json=array();
        include($v);
        if($json['less']){
          $json['css']=onez('less')->tocss($json['less']);
          unset($json['less']);
        }
        if($json['html']){
          $html[]='<script type="x-template" id="temp-'.$type.'">';
          $html[]=$json['html'];
          $html[]='</script>';
        }
        if($json['js']){
          $html[]='<script type="x-code" id="js-'.$type.'">';
          $html[]=$json['js'];
          $html[]='</script>';
        }
        if($json['css']){
          $css[]=$json['css'];
        }
      }
    }
    $glob=glob($G['this']->path.'/components'.$path.'/*.php');
    if($glob){
      foreach($glob as $v){
        $type=substr(basename($v),0,-4);
        $json=array();
        include($v);
        if($json['less']){
          $json['css']=onez('less')->tocss($json['less']);
          unset($json['less']);
        }
        if($json['html']){
          $html[]='<script type="x-template" id="temp-'.$type.'">';
          $html[]=$json['html'];
          $html[]='</script>';
        }
        if($json['css']){
          $css[]=$json['css'];
        }
      }
    }
    if($css){
      $html[]='<style type="text/css">';
      $html[]=implode("\n",$css);
      $html[]='</style>';
    }
    return implode("\n",$html);
  }
  function index(){
    global $G,$A,$record;
    $_ajax_action=onez()->gp('_ajax_action');
    if($_ajax_action){
      $json=array();
      $json['status']='success';
      $path=onez('template')->get('path');
      $_ajax_action=preg_replace('/[\.]+\//i','',$_ajax_action);
      $actionFile=$path.'/'.$_ajax_action.'.php';
      if(file_exists($G['this']->path.'/tmpls'.$actionFile)){
        include($G['this']->path.'/tmpls'.$actionFile);
      }
      onez()->output($json);
    }
    
    $_action=onez()->gp('_action');
    if($_action=='getData'){
      $A=array();
      $A['ver']='1.0';
      $A['action']=onez()->gp('action');
      $record=array();
      $action='action='.str_replace('?','&',$A['action']);
      parse_str($action,$info);
      $action=$info['action'];
      foreach($info as $k=>$v){
        $_GET[$k]=$_REQUEST[$k]=$v;
      }
      $path=onez('template')->get('path');
      $action=preg_replace('/[\.]+\//i','',$action);
      $actionFile=$path.'/'.$action.'.php';
      if($action=='welcome'){
        if(file_exists($G['this']->path.'/tmpls'.$actionFile)){
          include($G['this']->path.'/tmpls'.$actionFile);
        }
        $this->tpl->welcome();
      }else{
        if(!file_exists($G['this']->path.'/tmpls'.$actionFile)){
          //onez()->error('接口'.$actionFile.'不存在');
        }
        include($G['this']->path.'/tmpls'.$actionFile);
      }
      $A['_ajax_action']=$action;
      $record && $A['record']=$record;
      $json=array();
      $json['status']='success';
      $json['data']=$A;
      if(defined('IS_POSTS_MODE')){
        return $json;
      }
      onez()->output($json);
    }elseif($_action=='_component'){
      $type=onez()->gp('type');
      $json=array('type'=>$type);
      
      $path=onez('template')->get('path');
      $o=explode('/',$path);
      $plat=$o[1];
      $type=preg_replace('/[\.]+\//i','',$type);
      if(file_exists($G['this']->path.'/components'.$path.'/'.$type.'.php')){
        include($G['this']->path.'/components'.$path.'/'.$type.'.php');
      }elseif(file_exists($this->tpl->path.'/components/'.$plat.'/'.$type.'.php')){
        include($this->tpl->path.'/components/'.$plat.'/'.$type.'.php');
      }
      if($type=='default'){
        $json['html']='<div v-html="data.html"></div>';
      }
      if($json['less']){
        $json['css']=onez('less')->tocss($json['less']);
        unset($json['less']);
      }
      if(defined('IS_POSTS_MODE')){
        return $json;
      }
      onez()->output($json);
    }elseif($_action=='posts'){
      define('IS_POSTS_MODE',1);
      $json=array();
      $json['status']='success';
      $_postdatas=$_REQUEST['_postdatas'];
      if($_postdatas){
        foreach(json_decode($_postdatas,1) as $action=>$postdata){
          $_action=$action;
          if(strpos($action,'?')!==false||strpos($action,'&')!==false){
            $action='action='.str_replace('?','&',$action);
            parse_str($action,$info);
            foreach($info as $k=>$v){
              if($k=='action'){
                $action=$v;
              }else{
                $_REQUEST[$k]=$_GET[$k]=$v;
              }
            }
          }
          if($action=='posts'){
            continue;
          }
          $REQUEST=$_REQUEST;
          $GET=$_GET;
          $POST=$_POST;
          
          $_REQUEST['_action']=$action;
          foreach($postdata as $k=>$v){
            $_POST[$k]=$v;
            $_REQUEST[$k]=$v;
          }
          $json['actions'][$_action]=$this->index();
          
          $_REQUEST=$REQUEST;
          $_GET=$GET;
          $_POST=$POST;
        }
      }
      onez()->output($json);
    }
    include($this->tpl->path.'/php/index.php');
  }
}