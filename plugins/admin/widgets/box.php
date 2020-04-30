<?php

/* ========================================================================
 * $Id: box.php 1081 2017-03-01 11:58:32Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_box extends onezphp_admin_widgets{
  function __construct(){
    
  }
  function addbtn($name,$url,$style='success'){
    $this->btns.='<a href="'.$url.'" class="btn btn-success">'.$name.'</a> ';
    return $this;
  }
  function code(){
    //$this->html.='<section class="content">';
    
    $footer='';
    if($this->get('footer')){
      $footer='<div class="box-footer clearfix">'.$this->get('footer').'</div>';
    }
    if($this->btns){
      $this->html.='<div class="btns" style="padding-bottom: 10px">'.$this->btns.'</div>';
    }
    $this->html.='
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">'.$this->get('title').'</h3>
      <div class="box-tools pull-right"></div>
    </div>
    <!-- /.box-header -->
    <div class="box-body '.$this->get('attr-body').'">
    '.$this->get('html').'
    </div>
    <!-- /.box-body -->
    '.$footer.'
    <!-- /.box-footer --></div>
';
    //$this->html.='</section>';
    return $this->html;
  }
}