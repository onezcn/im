<?php

/* ========================================================================
 * $Id: data.php 206 2020-04-26 19:22:32Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#数据中心
$G['time']=time();
onez('factory.data')->auto(array(
  'path'=>dirname(__FILE__),//tmpl目录
  'templates'=>'',//强制缓存的模板
));
