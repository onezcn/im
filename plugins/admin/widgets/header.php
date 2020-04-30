<?php

/* ========================================================================
 * $Id: header.php 448 2016-11-14 01:28:36Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_header extends onezphp_admin_widgets{
  function __construct(){
    
  }
  function code(){
    $where.='<li class="active">'.$this->get('title').'</li>';
    $this->html.='<section class="content-header">
      <h1>
        '.$this->get('title').'
      </h1>
      <ol class="breadcrumb">
        '.$where.'
      </ol>
    </section>';
    return $this->html;
  }
}