<?php

/* ========================================================================
 * $Id: tip.php 388 2016-11-14 01:28:36Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_tip extends onezphp_admin_widgets{
  var $title='系统提示';
  var $content='';
  function __construct(){
    
  }
  function code(){
    $this->html.='<div class="callout callout-info">
          <h4>'.$this->title.'</h4>
          <p>'.$this->content.'</p>
        </div>';
    return $this->html;
  }
}