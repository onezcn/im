<?php
include_once(dirname(__FILE__).'/lib/onezphp.php');
if(!file_exists(dirname(__FILE__).'/cache/install.lock')){
  onez()->location('install.php');
}
onez('debug')->showerror();
$site_p_token='wxpc';
if(file_exists(ONEZ_ROOT.'/config/siteinfo.php')){
  include(ONEZ_ROOT.'/config/siteinfo.php');
}
$__ptoken=onez()->gp('_p');
$__method=onez()->gp('_m');
empty($__ptoken) && $__ptoken=$site_p_token;
empty($__method) && $__method='index';
$G['href_extra']=array();
$__ptoken!=$site_p_token && $G['href_extra']['_p']=$__ptoken;
$__method!='index' && $G['href_extra']['_m']=$__method;
$G['this']=onez($__ptoken);
$G['this']->init();
$G['this']->$__method();
