<?php

/* ========================================================================
 * $Id: html.php 1593 2017-02-20 14:20:58Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_html extends onezphp{
  var $tagname='div';
  var $html='';
  var $attrs=array();
  var $childs=array();
  function create($tagname){
    $spr=new onezphp_html();
    $spr->tagname=$tagname;
    return $spr;
  }
  function code(){
    $code='<'.$this->tagname;
    if($this->attrs){
      foreach($this->attrs as $k=>$v){
        $code.=' '.$k.'="'.$v.'"';
      }
    }
    if($this->childs){
      foreach($this->childs as $k=>$v){
        $this->html.=$v->code();
      }
    }
    if($this->html || $this->is_end){
      $code.='>';
      $code.=$this->html;
      $code.='</'.$this->tagname.'>';
    }else{
      $code.=' />';
    }
    return $code;
  }
  function html($html=false){
    if($html===false){
      return $this->html;
    }else{
      $this->html=$html;
      return $this;
    }
  }
  function add($sprite,$name=false){
    if(!$name){
      $name=$sprite->tagname;
      $n=0;
      while(isset($this->childs[$name])){
        $n++;
        $name=$sprite->tagname.$n;
      }
    }
    $this->childs[$name]=$sprite;
    return $this;
  }
  function get($key, $def = false){#避免冲突
    $name=$key;
    return $this->childs[$name];
  }
  function remove($name){
    unset($this->childs[$name]);
  }
  function attr($key,$value=false){
    if($html===false){
      return $this->attrs[$key];
    }
    if($value){
      if($key=='tagname'){
        $this->tagname=$value;
      }else{
        $this->attrs[$key]=$value;
      }
    }else{
      unset($this->attrs[$key]);
    }
    return $this;
  }
}