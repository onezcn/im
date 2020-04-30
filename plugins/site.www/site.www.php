<?php

/* ========================================================================
 * $Id: site.www.php 5609 2019-03-05 06:10:48Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：网站基础插件
#标识：site.www

class onezphp_site_www extends onezphp{
  function __construct(){
    
  }
  function option($key=false,$default=false){
    return $this->myoption($key,$default);
  }
  function option_set($arr){
    return $this->myoption_set($arr);
  }
  function db_init($tablename,$text='',$number='',$money=''){
    global $G;
    $arr=array();
    $text && $arr['text']=$text;
    $number && $arr['number']=$number;
    $money && $arr['money']=$money;
    
    if($arr){
      $this->data()->init('site.'.$this->token,$tablename,$arr,1);
    }
    return $this;
  }
  function siteinfo(){
    global $G;
    $item=$this->myoption();
    $appid=$item['onez_appid'];
    $appkey=$item['onez_appkey'];
    $hash=onez()->gp('hash');
    if($hash==md5("$appid\t$appkey")){
      $A=array('sitehash'=>onez('onez')->sitehash(),'addons'=>array_keys(onez('onez')->addons()));
      $this->siteinfo_add($A);
      $A['PHP_VERSION']=PHP_VERSION;
      $A['os']=php_uname();
      $A['sapi']=$_SERVER['SERVER_SOFTWARE'];
      $A['system']=PHP_SHLIB_SUFFIX == 'dll'?'Windows':'Linux';
      $A['freespace']=function_exists('disk_free_space')?disk_free_space(ONEZ_ROOT):'unknow';
      $A['upload_max_filesize']=@ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
      $A['post_max_size']=@ini_get('post_max_size') ? ini_get('post_max_size') : 'unknow';
      $extensions=onez()->gp('extensions');
      if($extensions){
        $A['extensions']=array();
        foreach(explode(',',$extensions) as $v){
          $A['extensions'][$v]=extension_loaded($v)?'Y':'N';
        }
      }
      $functions=onez()->gp('functions');
      if($functions){
        $A['functions']=array();
        foreach(explode(',',$functions) as $v){
          $A['functions'][$v]=function_exists($v)?'Y':'N';
        }
      }
      $classes=onez()->gp('classes');
      if($classes){
        $A['classes']=array();
        foreach(explode(',',$classes) as $v){
          $A['classes'][$v]=class_exists($v)?'Y':'N';
        }
      }
      onez()->output($A);
    }
    header("HTTP/1.0 404 Not Found");
  }
  function init(){
    global $G;
    $G['this']->data_alias=$this->cname;
  }
  function index(){
    global $G;
    $G['this']->init();
    $this->www();
  }
  
  function m(){
    global $G;
    $G['this']->init();
    $action=onez()->gp('action');
    if(strpos($action,'page_id_')!==false){
      $navID=(int)substr($action,8);
      $T=$G['this']->data()->open('navs')->record("1 order by step,id");
      if($navID>0){
        $navID--;
        $nav=$T[$navID];
        parse_str('action='.$nav['href'],$info);
        foreach($info as $k=>$v){
          $_REQUEST[$k]=$_GET[$k]=$v;
        }
      }
    }
    onez('m')->add_tmpl_path($G['this']->path.'/tmpls');
    onez('m')->add_tmpl_path(dirname(__FILE__).'/tmpls');
    onez('m')->auto();
  }
  function avatar($userid){
    global $G;
  	$myid = sprintf("%09d", $userid);
  	$dir1 = substr($myid, 0, 3);
  	$dir2 = substr($myid, 3, 2);
  	$dir3 = substr($myid, 5, 2);
    $avatarFile='/avatars/'.$G['this']->cname.'/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($myid, -2).'.jpg';
    if(file_exists(ONEZ_CACHE_PATH.$avatarFile)){
      return onez('m.plugin')->thumb(ONEZ_CACHE_URL.$avatarFile.'?t='.filemtime(ONEZ_CACHE_PATH.$avatarFile)).'?t='.filemtime(ONEZ_CACHE_PATH.$avatarFile);
    }else{
      return onez('m.plugin')->thumb($this->url.'/images/avatar.jpg');
    }
  }
  function avatar_set($userid,$url){
    global $G;
    $usertype=$this->usertype();
  	$myid = sprintf("%09d", $userid);
  	$dir1 = substr($myid, 0, 3);
  	$dir2 = substr($myid, 3, 2);
  	$dir3 = substr($myid, 5, 2);
    $avatarFile='/avatars/'.$G['this']->cname.'/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($myid, -2).'.jpg';
    $data=onez()->post($url);
    
    onez()->write(ONEZ_CACHE_PATH.$avatarFile,$data);
    return $this;
  }
  function cloud_code($href,$cloudid=0){
    global $G;
    if(file_exists(ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud/touch.lock')){
      $this->cloud_clear();
    }
    $hash=md5($href);
    $hrefFile=ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud/'.$cloudid.'/'.$hash.'.php';
    if(!file_exists($hrefFile)){
      $post=array(
        'cloudid'=>$cloudid,
      );
      $data=onez()->post('http://os.onez.cn/?_m=get_cloud_code',http_build_query($post));
      $files=onez('onezip')->unzip($data);
      foreach($files as $k=>$v){
        onez()->write(ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud/'.$cloudid.'/'.md5($k).'.php',$v);
      }
    }
    include_once($hrefFile);
    if(isset($cloud_nocache) && $cloud_nocache){
      $this->cloud_clear(ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud/'.$cloudid);
    }
  }
  function cloud_touch(){
    onez()->write(ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud/touch.lock',onez()->ip());
  }
  function cloud_url($href,$cloudid=0,$mobile=false){
    if(defined('IS_M_API')||$mobile){
      $url=onez('site.www')->action('cloud&href='.urlencode($href).'&cloudid='.$cloudid);
    }else{
      $url=onez('site.www')->href('/cloud.php').'&href='.urlencode($href).'&cloudid='.$cloudid;
    }
    return $url;
  }
  function cloud_clear($path=false){
    if($path===false){
      $path=ONEZ_CACHE_PATH.'/plugins/'.$G['this']->cname.'/cloud';
    }
    $glob=glob($path.'/*');
    if($glob){
      foreach($glob as $v){
        if(is_dir($v)){
          $this->cloud_clear($v);
          @rmdir($v);
        }else{
          @unlink($v);
        }
      }
    }
  }
}