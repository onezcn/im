<?php

/* ========================================================================
 * $Id: fileupload.php 2493 2020-04-22 11:10:29Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

if(!defined('IN_ONEZ')){
  $path=dirname(__FILE__);
  while(!file_exists($path.'/lib/onezphp.php')){
    $path=dirname($path);
    if($path=='.'){
      exit('onezphp not exists');
    }
  }
  include_once($path.'/lib/onezphp.php');
  $sitetoken=onez()->gp('sitetoken');
  if($sitetoken){
    $G['this']=onez($sitetoken);
    $G['this']->init();
  }
}
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:POST');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');
!$_FILES['Filedata'] && $_FILES['Filedata']=$_FILES['file'];
$tmpfile=$_FILES['Filedata']['tmp_name'];
if(!$tmpfile || !file_exists($tmpfile)){
  onez()->error('文件无效'.var_export($_FILES,1));
}
$data=onez()->read($tmpfile);

$file=false;
#
switch($_FILES['Filedata']['type']){
  case 'image/jpeg':
    $im=imagecreatefromstring($data);
    if($im){
      $file='/cache/plugins/m/'.date('Y/m/d').'/'.uniqid().'.jpg';
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      imagejpeg($im,ONEZ_CACHE_PATH.$file);
      imagedestroy($im);
      onez()->write(ONEZ_CACHE_PATH.$file,$data);
    }
    break;
  case 'image/gif':
    $im=imagecreatefromstring($data);
    if($im){
      $file='/cache/plugins/m/'.date('Y/m/d').'/'.uniqid().'.gif';
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      imagecolortransparent($im);
      imagegif($im,ONEZ_CACHE_PATH.$file);
      imagedestroy($im);
      onez()->write(ONEZ_CACHE_PATH.$file,$data);
    }
    break;
  case 'image/png':
    $im=imagecreatefromstring($data);
    if($im){
      $file='/cache/plugins/m/'.date('Y/m/d').'/'.uniqid().'.png';
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      imagecolortransparent($im);
      imagepng($im,ONEZ_CACHE_PATH.$file);
      imagedestroy($im);
      onez()->write(ONEZ_CACHE_PATH.$file,$data);
    }
    break;
  case 'audio/mpeg':
    $file='/cache/plugins/m/'.date('Y/m/d').'/'.uniqid().'.mp3';
    onez()->write(ONEZ_CACHE_PATH.$file,$data);
  default:
    $file='/cache/plugins/m/'.date('Y/m/d').'/'.uniqid().'.'.$_FILES['Filedata']['type'].'.tmp';
    onez()->write(ONEZ_CACHE_PATH.$file,$data);
    break;
}

$result=array();
$result['code']=-1;
if($file && file_exists(ONEZ_CACHE_PATH.$file)){
  $result['type']=$_FILES['Filedata']['type'];
  $result['code']=100;
  $result['url']=ONEZ_CACHE_URL.$file;
}else{
  onez()->error('文件无效');
}
onez()->output($result);