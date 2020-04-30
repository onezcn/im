<?php

/* ========================================================================
 * $Id: centerBox.php 578 2016-11-14 01:28:36Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_centerBox extends onezphp_admin_widgets{
  
  function __construct(){
    $this->css['centerBox']=<<<ONEZ
.widgets-centerBox{
  
}
ONEZ;
    $this->html='<section class="content">';
    $this->html.='<div class="row">';
    $this->html.='<div class="col-md-3"></div>';
    $this->html.='<div class="col-md-6">';
  }
  function code(){
    $this->html.='</div>';
    $this->html.='<div class="col-md-3"></div>';
    $this->html.='</div>';
    $this->html.='</section>';
    return $this->html;
  }
}