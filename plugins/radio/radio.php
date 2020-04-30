<?php

/* ========================================================================
 * $Id: radio.php 1121 2020-04-28 11:49:27Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：表单组件-单选
#标识：radio

class onezphp_radio extends onezphp{
  function __construct(){
    
  }
  function form_code($arr){
    $select='';
    $s='';
    $value=$arr['value'];
    if($arr['options']){
      !$value && $value='';
      foreach($arr['options'] as $k=>$v){
        $s=$value==$k?' checked':'';
        $select.='<input type="radio" id="input-'.$arr['key'].'-'.md5($k).'" name="'.$arr['key'].'" value="'.$k.'"'.$s.' style="position: relative;top: 2px;"> <label for="input-'.$arr['key'].'-'.md5($k).'" style="font-style:normal;display:inline-block!important;font-weight:normal;margin-right:5px;">'.$v.'</label> ';
      }
    }
    
    $html='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
          '.$select.'
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
    return $select;
  }
}