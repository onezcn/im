<?php

/* ========================================================================
 * $Id: cloud.php 155 2019-03-05 05:15:06Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$href=onez()->gp('href');
$cloudid=(int)onez()->gp('cloudid');
$G['this']->cloud_code($href,$cloudid);
