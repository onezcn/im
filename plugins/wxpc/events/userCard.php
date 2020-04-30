<?php

/* ========================================================================
 * $Id: userCard.php 265 2020-04-27 16:38:14Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
return array(
  'token'=>'page',
  'target'=>'win',
  'width'=>'369',
  'dlgId'=>'userCard',
  'auto'=>'1',
  'pos'=>'mouse',
  'shade'=>true,
  'noTitle'=>true,
  'action'=>'userinfo&userid='.$event['userid'],
);