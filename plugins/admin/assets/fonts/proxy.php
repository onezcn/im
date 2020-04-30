<?php

/* ========================================================================
 * $Id: proxy.php 924 2017-09-17 17:16:11Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

#解决跨域或没有设置MIME的问题


error_reporting(E_ERROR | E_WARNING | E_PARSE);

header("Cache-Control: public");
header("Pragma: cache");
$offset = 30*60*60*24; // cache 1 month
$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s", time() + $offset)." GMT";
header($ExpStr);

// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:POST');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');
$font=$_GET['font'];
if(strpos($font,'php')!==false){
  exit('Access Denied');
}
$font=str_replace(array('/','\\'),'',$font);
$fontFile=dirname(__FILE__).'/'.$font;
if(!file_exists($fontFile)){
  exit('Access Denied');
}

if($handle=@fopen($fontFile,'rb')){
  flock($handle,LOCK_SH);
  $size=filesize($fontFile);
  if($size>0){
    $filedata=fread($handle,$size);
  }
  fclose($handle);
}
echo $filedata;