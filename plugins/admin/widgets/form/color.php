<?php

/* ========================================================================
 * $Id: color.php 1657 2017-02-18 22:36:08Z onez $
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
  if(!$G['is_colorpicker']){
    $G['is_colorpicker']=1;
    $G['footer'].=onez('ui')->css(onez('admin')->url.'/assets/plugins/colorpicker/css/colorpicker.css');
    $G['footer'].=onez('ui')->js(onez('admin')->url.'/assets/plugins/colorpicker/js/bootstrap-colorpicker.js');
    $G['footer-js'].="$('.onez-color').colorpicker().on('changeColor', function(ev){
			$(ev.currentTarget).next().css({backgroundColor:ev.color.toHex()});
		});;";
  }
  
  $box=onez('html')->create('div')->attr('class','form-group'.$arr['group']);
  
  $label=onez('html')->create('label')->attr('for','input-'.$arr['key'])->html($arr['label']);
  $input=onez('html')->create('input')->attr('class','form-control onez-color')
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
  
  $group=onez('html')->create('div')->attr('class','input-group');
  $group->add($input);
  $span=onez('html')->create('span')->attr('class','input-group-btn')->attr('style','background-color:'.$arr['value'].';width:34px;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc')->html($arr['after']);
  $group->add($span);
  $box->add($group);
    
  $this->html.=$box->code();
}