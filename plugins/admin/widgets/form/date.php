<?php

/* ========================================================================
 * $Id: date.php 1863 2017-02-18 22:36:14Z onez $
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
  if(!$G['is_datepicker']){
    $G['is_datepicker']=1;
    $G['footer'].=onez('ui')->css(onez('admin')->url.'/assets/plugins/datepicker/datepicker3.css');
    $G['footer'].=onez('ui')->js(onez('admin')->url.'/assets/plugins/datepicker/bootstrap-datepicker.js');
    $G['footer'].=onez('ui')->js(onez('admin')->url.'/assets/plugins/datepicker/locales/bootstrap-datepicker.zh-CN.js');
    $G['footer-js'].="$('.onez-date').datepicker({
    format: 'yyyy-mm-dd',
		language: 'zh-CN',
    autoclose: true
  });";
  }
  
  $box=onez('html')->create('div')->attr('class','form-group '.$arr['group']);
  
  $label=onez('html')->create('label')->attr('for','input-'.$arr['key'])->html($arr['label']);
  $input=onez('html')->create('input')->attr('class','form-control onez-date')
                                      ->attr('id','input-'.$arr['key'])
                                      ->attr('name',$arr['key'])
                                      ->attr('type','text')
                                      ->attr('placeholder',$arr['hint'])
                                      ->attr('value',$arr['value'])
                                    ;
  if($arr['data-ajax']){
    $input->attr('data-ajax',$arr['data-ajax']);
  }
  $box->add($label);
  
  if($arr['before'] || $arr['after']){
    $group=onez('html')->create('div')->attr('class','input-group');
    if($arr['before']){
      $span=onez('html')->create('span')->attr('class','input-group-btn')->html($arr['before']);
      $group->add($span);
    }
    $group->add($input);
    if($arr['after']){
      $span=onez('html')->create('span')->attr('class','input-group-btn')->html($arr['after']);
      $group->add($span);
    }               ;
    $box->add($group);
  }else{
    $box->add($input);
  }
  $this->html.=$box->code();
}