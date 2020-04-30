<?php

/* ========================================================================
 * $Id: uicolor.php 2712 2017-05-16 17:44:54Z onez $
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
  if($arr['after']){
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
    <div class="input-group">
          <input type="'.$arr['type'].'" class="form-control" id="input-'.$arr['key'].'" name="'.$arr['key'].'" placeholder="'.$arr['hint'].'" value="'.$value.'">
          <span class="input-group-btn">'.$arr['after'].'</span>
    </div>
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }else{
    if(!$G['footer-uicolor']){
      $G['footer-uicolor']=1;
      $G['footer-js'].=<<<ONEZ
$(function(){
  $('.fc-color-picker li.current').css({'border-bottom':'2px #f00 solid'});
  $('.fc-color-picker li[data-color]').click(function(){
    var color=$(this).attr('data-color');
    $('.fc-color-picker li[data-color]').css({'border':'0'});
    $(this).css({'border-bottom':'2px #f00 solid'});
    $('.fc-color-picker').parent().find('input').val(color);
  });  
});
ONEZ;
    }
    $colors=array('aqua','blue','light-blue','teal','yellow','orange','green','lime','red','purple','fuchsia','muted','navy');
    $color_chooser='';
    foreach($colors as $v){
      $color_chooser.='<li'.($v==$value?' class="current"':'').' data-color="'.$v.'"><span class="text-'.$v.'"><i class="fa fa-square"></i></span></li>';
    }
    if($arr['style']=='btn'){
      $colors=array('default','primary','success','info','danger','warning');
      $color_chooser='';
      foreach($colors as $v){
        $color_chooser.='<li'.($v==$value?' class="current"':'').' data-color="'.$v.'"><span class="btn btn-'.$v.'"></span></li>';
      }
    }
    if($arr['colors']){
      $colors=$arr['colors'];
      $color_chooser='';
      foreach($colors as $v){
        $color_chooser.='<li'.($v==$value?' class="current"':'').' data-color="'.$v.'"><span style="color:'.$v.'"><i class="fa fa-square"></i></span></li>';
      }
    }
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
          <input type="hidden" id="input-'.$arr['key'].'" name="'.$arr['key'].'" value="'.$value.'">
          <ul class="fc-color-picker" id="color-chooser">'.$color_chooser.'<div class="clearfix"></div></ul>
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }
}