<?php

/* ========================================================================
 * $Id: less.php 884 2018-04-02 16:56:47Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_less extends onezphp{
  function __construct(){
    
  }
  function head($return=0){
    $html.='<script src="'.$this->url.'/js/less.min.js"></script>';
    if($return){
      return $html;
    }else{
      echo $html;
    }
  }
  function tocss($lessFile){
    if(!$this->less){
      include_once(dirname(__FILE__).'/lib/lessc.inc.php');
      $this->less = new lessc();
    }
    if(strlen($lessFile)>256 || !is_readable($lessFile)){
      global $G;
      if($this->baseLessFile){
        $lessFile=onez()->read($this->baseLessFile).$lessFile;
      }
      if($G['this'] && file_exists($G['this']->path.'/css/base.less')){
        $lessFile=onez()->read($G['this']->path.'/css/base.less').$lessFile;
      }
      return $this->less->compile($lessFile);
    }
    return $this->less->compileFile($lessFile);
  }
}