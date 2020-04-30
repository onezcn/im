<?php

/* ========================================================================
 * $Id: select.php 1931 2018-10-30 20:28:12Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
if($TYPE=='code'){
  $select='';
  $s='';
  if($arr['mul'] || $arr['multiple']){
    $s=' multiple="multiple"';
    $G['footer'].='<script src="'.onez('admin')->url.'/assets/plugins/select2/select2.full.min.js"></script>';
    $G['footer'].='<link rel="stylesheet" href="'.onez('admin')->url.'/assets/plugins/select2/select2.min.css">';
    $G['footer-js'].="$('select[multiple]').select2();";
    $select.='<select class="form-control" multiple="multiple" id="input-'.$arr['key'].'" name="'.$arr['key'].'[]">';
  }else{
    $select.='<select class="form-control" id="input-'.$arr['key'].'" name="'.$arr['key'].'">';
  }
  if($arr['options']){
    !$value && $value=array();
    foreach($arr['options'] as $k=>$v){
      if($arr['mul'] || $arr['multiple']){
        $s=in_array($k,$value)?' selected':'';
      }else{
        $s=((string)$value==$k?' selected':'');
      }
      $select.='<option value="'.$k.'"'.$s.'>'.$v.'</option>';
    }
  }
  $select.='</select>';
  
  if($arr['after']){
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
    <div class="input-group">
          '.$select.'
          <span class="input-group-btn">'.$arr['after'].'</span>
    </div>
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }else{
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
          '.$select.'
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }
}