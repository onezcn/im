<?php

/* ========================================================================
 * $Id: upyun.bak.php 1337 2017-04-06 11:31:06Z onez $
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
  $filetype=$arr['filetype']?$arr['filetype']:'';
  !$filetype && $filetype=$arr['ext']?$arr['ext']:'';
  !$filetype && $filetype='gif,jpg,jpeg,png';
  
  $arr['html']=onez('upyun')->showform($arr['key'],$arr['value'],onez('upyun')->filename($arr['pre']?$arr['pre']:'/game',$arr['ext']?$arr['ext']:'png'),$filetype);
  
  $box=onez('html')->create('div')->attr('class','form-group '.$arr['group']);
  
  $label=onez('html')->create('label')->attr('for','input-'.$arr['key'])->html($arr['label']);
  $input=onez('html')->create('div')->attr('class','')
                                    ->attr('id','html-'.$arr['key'])
                                  ;
  $input->html($arr['html'])->is_end=1;
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