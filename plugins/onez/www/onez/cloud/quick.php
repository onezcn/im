<?php

/* ========================================================================
 * $Id: quick.php 1133 2017-09-18 12:21:19Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
define('CUR_URL',onez('onez')->href('/onez/cloud/quick.php',0));
$G['title']='快速安装';
$action=onez()->gp('action');
if($action=='install'){
  $pkey=onez()->gp('pkey');
  $response = onez()->post('http://www.onezphp.com/api/usersite.php', http_build_query(array('action'=>'install.pkey','pkey'=>$pkey,'sitehash'=>onez('onez')->sitehash())));
  $json=json_decode($response,1);
  if($json['addon']){
    onez('onez')->install($json['addon']);
  }
  if($json['addons']){
    foreach($json['addons'] as $v){
      onez('onez')->install($v);
    }
  }
  if($json['error']){
    onez()->error($json['error']);
  }
  if($json['datas']){
    onez('onez')->import($json['datas']);
  }
  if($json['tip']){
    onez()->ok($json['tip'],'reload');
  }
  onez()->output($json);
}
onez('admin')->header();
$response = onez()->post('http://www.onezphp.com/api/usersite.php', http_build_query(array(
  'action'=>'welcome',
  'sitehash'=>onez('onez')->sitehash(),
)));
$json=json_decode($response,1);
if(!empty($json) && !empty($json['html'])){
  echo $json['html'];
}
onez('admin')->footer();