<?php

/* ========================================================================
 * $Id: doFriend.php 337 2020-04-28 10:55:52Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
if($event['method']=='add'){#申请好友
  return array(
    'token'=>'page',
    'target'=>'win',
    'width'=>'430',
    'dlgId'=>'doFriend',
    'noScroll'=>'1',
    'auto'=>'1',
    'exit'=>'1',
    'action'=>'doFriend&method='.$event['method'].'&userid='.$event['userid'],
  );
}