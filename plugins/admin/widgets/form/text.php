<?php

/* ========================================================================
 * $Id: text.php 1508 2019-01-25 08:44:12Z onez $
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
  
  $box=onez('html')->create('div')->attr('class','form-group '.$arr['group']);
  
  $label=onez('html')->create('label')->attr('for','input-'.$arr['key'])->html($arr['label']);
  $input=onez('html')->create('input')->attr('class','form-control')
                                      ->attr('id','input-'.$arr['key'])
                                      ->attr('name',$arr['key'])
                                      ->attr('type',$arr['type'])
                                      ->attr('placeholder',$arr['hint'])
                                      ->attr('value',$arr['value'])
                                    ;
  if($arr['data-ajax']){
    $input->attr('data-ajax',$arr['data-ajax']);
  }
  if($arr['data-inputs']){
    $input->attr('data-inputs',$arr['data-inputs']);
  }
  if($arr['data-args']){
    $input->attr('data-args',$arr['data-args']);
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