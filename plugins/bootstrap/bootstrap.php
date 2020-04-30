<?php

/* ========================================================================
 * $Id: bootstrap.php 1013 2017-10-16 17:07:41Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_bootstrap extends onezphp{
  function __construct(){
    
  }
  function head($return=0){
    global $G;
    $html.='<link rel="stylesheet" href="'.$this->url.'/css/bootstrap.min.css">';
    $html.='<!--[if lte IE 6]>
  <link rel="stylesheet" type="text/css" href="'.$this->url.'/css/bootstrap-ie6.min.css">
  <link rel="stylesheet" type="text/css" href="'.$this->url.'/css/ie.css">
  <![endif]-->
    <!--[if lt IE 9]>
  <script src="'.$this->url.'/js/html5shiv.min.js"></script>
  <script src="'.$this->url.'/js/respond.min.js"></script>
  <![endif]-->';
    $html.='<script src="'.$this->url.'/js/bootstrap.js"></script>';
    $G['footer'].='<!--[if lte IE 6]>
  <script type="text/javascript" src="'.$this->url.'/js/bootstrap-ie.js"></script>
  <![endif]-->';
    if($return){
      return $html;
    }else{
      echo $html;
    }
  }
  function rightmenu(){
    echo'<script src="'.$this->url.'/js/bootstrap-contextmenu.js"></script>';
  }
}