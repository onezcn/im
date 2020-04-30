<?php

/* ========================================================================
 * $Id: index.php 206 2020-04-26 19:24:41Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$G['title']='微信电脑版';
onez('template')->loadTemplate('template.wxpc');
onez('template')->set('path','/www/dialog');
onez('template')->index();
?>