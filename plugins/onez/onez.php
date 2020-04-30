<?php

/* ========================================================================
 * $Id: onez.php 12655 2019-06-17 23:36:48Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_onez extends onezphp{
  var $version='2.0';
  function __construct(){
  }
  function init(){
    global $G;
    list($grade)=func_get_args();
    if($grade=='admin'){
      onez('call')->add('onez');
    }
  }
  function bind_page_header(){
    global $G;
    $grade=$G['url.grade'];
    if($grade!='admin'){
      return;
    }
    #更新检测脚本
    $nocheck=onez('cache')->cookie('onez_nocheck');
    $url=onez('onez')->href('/onez/cloud/upgrade.php');
    !$nocheck && $G['footer-js'].=<<<ONEZ
$(function(){
  $.post('$url',{action:'upgrade',url:location.href},function(o){
    //console.log(o);
  },'json');
  $('.onez-plugin-upgrade-message a').bind('click',function(){
    $.post('$url',{action:'message.view',url:location.href},function(o){
      //console.log(o);
    },'json');
  });
});
ONEZ;
    
    list($type)=explode('|',onez()->gp('_onez'));
    $s1=$s2='';
    if($type=='onez'){
      $s2=' class="active"';
    }else{
      $s1=' class="active"';
    }
    onez('admin')->menu_top_left.='<ul class="nav navbar-nav">';
    onez('admin')->menu_top_left.='<li'.$s1.'><a href="'.onez()->href('/admin/index.php').'"><i class="fa fa-globe"></i> 后台</a></li>';
    onez('admin')->menu_top_left.='<li'.$s2.'><a href="'.onez('onez')->href('/onez/index.php').'"><i class="fa fa-sitemap"></i> 系统</a></li>';
    onez('admin')->menu_top_left.='</ul>';
    
    $plugins_info=onez('cache')->get('plugins.status.'.$G['this']->token);
    if($plugins_info['message']){
      $num=$plugins_info['message']['num'];
      $html=$plugins_info['message']['html'];
      onez('admin')->menu_top_right.='<ul class="nav navbar-nav">';
      onez('admin')->menu_top_right.='<li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell-o"></i>
                <span class="label label-danger">'.$num.'</span>
              </a>
              <ul class="dropdown-menu onez-plugin-upgrade-message">
                <li>
                  <ul class="menu">
                    '.$html.'
                  </ul>
                </li>
              </ul>
            </li>';
      onez('admin')->menu_top_right.='</ul>';
    }
    
    if($type=='onez'){
      $admin_menu=array();
      $file=dirname(__FILE__).'/www/onez/menu.inc.php';
      if(file_exists($file)){
        $menu=include($file);
        $G['this']->admin_menus($menu);
        if($menu){
          foreach($menu as $v){
            if($v['href']){
              $v['url']=$this->href($v['href'],0);
              unset($v['href']);
            }
            $admin_menu[]=$v;
          }
        }
      }
      onez('admin')->menu=$admin_menu;
      onez('admin')->style='skin-purple';
    }
  }
  function data_in($data){
    global $G;
    if($G['charset'] && $G['charset']!='utf-8'){
      $data=onez()->iconv('utf-8',$G['charset'],$data);
    }
    return $data;
  }
  function data_out($data){
    global $G;
    if($G['charset'] && $G['charset']!='utf-8'){
      $data=onez()->iconv($G['charset'],'utf-8',$data);
    }
    return $data;
  }
  function sitehash(){
    global $G;
    $item=$G['this']->option();
    $appid=$item['onez_appid'];
    $appkey=$item['onez_appkey'];
    $siteid=$item['onez_siteid'];
    $sitekey=$item['onez_sitekey'];
    if(!$appid){
      $item=onez()->myoption();
      $appid=$item['onez_appid'];
      $appkey=$item['onez_appkey'];
      $siteid=$item['onez_siteid'];
      $sitekey=$item['onez_sitekey'];
    }
    $time=time();
    $sitehash=base64_encode("$appid\t$siteid\t$sitekey\t$time\t".$G['this']->token."\t".md5($appkey)."\t".onez()->homepage()."\t".onez()->cururl());
    return $sitehash;
  }
  function post($post){
    global $G;
    $item=$G['this']->option();
    $appid=$item['onez_appid'];
    $appkey=$item['onez_appkey'];
    $siteid=$item['onez_siteid'];
    $sitekey=$item['onez_sitekey'];
    if(!$appid){
      $item=onez()->myoption();
      $appid=$item['onez_appid'];
      $appkey=$item['onez_appkey'];
      $siteid=$item['onez_siteid'];
      $sitekey=$item['onez_sitekey'];
    }
    $time=time();
    $post['version']=$this->version;
    $post['time']=$time;
    $post['siteid']=$siteid;
    $post['onez_usr']=$item['onez_usr'];
    $post['md5']=md5("$appid&$sitekey&$time".md5($appkey));
    
    $post=$this->data_out($post);
    if($post['format']=='url'){
      return 'http://www.onezphp.com/api/usersite.php?'.http_build_query($post);
    }
    $data=onez()->post('http://www.onezphp.com/api/usersite.php',http_build_query($post));
    if($post['format']=='json'){
      $json=json_decode($data,1);
      $json=$this->data_in($json);
      return $json;
    }
    $data=$this->data_in($data);
    return $data;
  }
  #引擎内所有应用
  function addons(){
    global $G;
    $ptokens=onez('cache')->get('store.$addons.'.$G['this']->token.".$G[appid].$G[siteid]");
    if(!$ptokens || !$ptokens['ptokens']){
      $ptokens=$this->update_addons();
    }
    $ptokens=$ptokens['ptokens'];
    $addons=array();
    if($ptokens){
      foreach($ptokens as $ptoken){
        if(!onez()->exists($ptoken)){
          continue;
        }
        $addons[$ptoken]=onez($ptoken);
      }
    }
    $G['store.$addons.'.$G['this']->token.".$G[appid].$G[siteid]"]=$addons;
    return $addons;
  }
  function addons_sync(){
    global $G;
    $G['this']->data()->set('noappid',1);
    $record=$G['this']->data()->open('addons')->record("1 order by step,id");
    $G['this']->data()->set('noappid',0);
    
  }
  function update_addons(){
    global $G;
    $ptokens=array();
    if(!$G['this']){
      return $ptokens;
    }
    $T=$G['this']->data()->open('addons')->record("index1='1' order by step,id");
    $product='';
    foreach($T as $rs){
      if(!$product && $rs['key12']=='product'){
        if(method_exists(onez($rs['token']),'index')){
          $product=$rs['token'];
        }
      }
      $ptoken=$rs['key11'];
      #添加并查找依赖
      $this->ptoken_abouts($ptokens,$ptoken,$rs);
    }
    if($G['this']->token!=$product){
      !$product && $product='main';
      onez()->write(ONEZ_CACHE_PATH.'/config/siteinfo.php',"<?php\n!defined('IN_ONEZ') && exit('Access Denied');\n\$site_p_token='$product';\n");
    }
    onez('cache')->set('store.$addons.'.$G['this']->token.".$G[appid].$G[siteid]",array('builder'=>'onezphp','ptokens'=>$ptokens));
    return $ptokens;
  }
  //查找依赖
  function ptoken_abouts(&$ptokens,$ptoken,$rs=array()){
    if(!$ptoken){
      return;
    }
    if(!onez()->exists($ptoken)){
      return;
    }
    if(in_array($ptoken,$ptokens)){
      return;
    }
    $ptokens[]=$ptoken;
    
    if(onez($ptoken)->config['abouts']){
      foreach(onez($ptoken)->config['abouts'] as $myptoken){
        $this->ptoken_abouts($ptokens,$myptoken,$rs);
      }
    }
  }
  function urls_admin($ptoken){
    if(!$ptoken){
      return;
    }
    if(!onez()->exists($ptoken)){
      return;
    }
    $html=array();
    if(method_exists(onez($ptoken),'setting')){
      $html[]='<a href="'.$this->href('/manage/setting.php?ptoken='.$ptoken).'" class="onez-miniwin btn btn-xs btn-success">设置</a>';
    }
    if(method_exists(onez($ptoken),'urls_admin')){
      $urls=onez($ptoken)->urls_admin();
      foreach($urls as $v){
        $html[]='<a href="'.$v['href'].'" class="onez-miniwin btn btn-xs btn-success">'.$v['name'].'</a>';
      }
    }
    return implode('|',$html);
  }
  function install($addon){
    global $G;
    if(!is_array($addon)){
      $addon=onez('onez')->post(array('action'=>'addon','format'=>'json','keyword'=>$addon));
    }
    $ptype=$addon['ptype'];
    $ptoken=$addon['ptoken'];
    $skill=$addon['skill'];
    $skill=='none' && $skill='';
    
    $subject=$addon['subject'];
    $summary=$addon['summary'];
    $version=$addon['version'];
    $__THIS__=$G['this'];
    onez($ptoken)->init();
    $G['this']=$__THIS__;
    $T=$G['this']->data()->open('addons')->one("token='$ptoken' and key12='$ptype'");
    if(!$T){
      $onez=array();
      $onez['userid']=$G['userid'];
      $onez['token']=$ptoken;
      $onez['key10']=$skill;
      $onez['key11']=$ptoken;
      $onez['key12']=$ptype;
      $onez['index1']=1;
      $onez['subject']=$subject;
      $onez['summary']=$summary;
      $onez['version']=$version;
      $G['this']->data()->open('addons')->insert($onez);
      $this->update_addons();
    }
  }
  function delete($upid){
    global $G;
    if(!$upid){
      return $this;
    }
    if($G['this']->data_alias){
      $this->delete_sitedata($G['this']->data_alias," and siteid='$G[siteid]'",$upid);
      return $this;
    }
    $datas=array();
    $namespace='site.'.$G['this']->token;
    $T=onez('db')->open('data')->record("namespace='$namespace' and upid='$upid' and id<>'$upid' order by id");
    foreach($T as $rs){
      $this->delete($rs['id']);
    }
    if($T){
      onez('db')->open('data')->delete("namespace='$namespace' and upid='$upid' and id<>'$upid'");
    }
    return $this;
  }
  function delete_sitedata($alias,$xxx,$upid=0){
    global $G;
    $datas=array();
    $namespace='site.'.$G['this']->token;
    if($upid==0){
      $old_data_alias=$G['this']->data_alias;
      $G['this']->data_alias=$alias;
    }
    $T=$G['this']->data()->open('data')->db->record($alias,'*',"namespace='$namespace' and upid='$upid' and id<>'$upid'$xxx order by id");
    foreach($T as $rs){
      $this->delete_sitedata($alias,$xxx,$rs['id']);
    }
    if($T){
      $G['this']->data()->open('data')->db->delete($alias,"namespace='$namespace' and upid='$upid' and id<>'$upid'$xxx");
    }
    if($upid==0){
      $G['this']->data_alias=$old_data_alias;
    }
    return $this;
  }
  function import(){
    global $G;
    list($datas,$upid,$extra,$alias,$function_before,$function_after)=func_get_args();
    $upid=(int)$upid;
    if(!$datas || !is_array($datas)){
      return;
    }
    $userids=array();
    foreach($datas as $rs){
      $oldid=$rs['id'];
      $children=$rs['children'];
      $rs['upid']=$upid;
      if($rs['extra']){
        $e=unserialize($rs['extra']);
        if(is_array($e)){
          $rs=array_merge($rs,$e);
        }
        unset($rs['extra']);
      }
      if($function_before){
        $function_before($rs,$oldid);
      }
      unset($rs['children']);
      unset($rs['id']);
      if($extra && is_array($extra)){
        $rs=array_merge($rs,$extra);
      }
      $newid=$G['this']->data()->open($rs['tablename'])->insert($rs);
      if($function_after){
        $function_after($rs,$oldid,$newid);
      }
      if($children){
        $this->import($children,$newid,$extra,$alias);
      }
      if($rs['tablename']=='member'){
        $userids[$oldid]=$newid;
      }
    }
    #重新调整用户对应关系
    if($userids){
      if(!$alias){
        $T=onez('db')->open('data')->record("namespace='$namespace' and userid<>0 order by id");
      }else{
        if($upid==0){
          $old_data_alias=$G['this']->data_alias;
          $G['this']->data_alias=$alias;
        }
        $T=$G['this']->data()->db->record($alias,'*',"namespace='$namespace' and userid<>0 order by id");
      }
      foreach($T as $rs){
        if(empty($userids[$rs['userid']])){
          $G['this']->data()->open($rs['tablename'])->update(array('userid'=>0),"id='$rs[id]'");
        }else{
          $G['this']->data()->open($rs['tablename'])->update(array('userid'=>$userids[$rs['userid']]),"id='$rs[id]'");
        }
      }
      if(!$alias){
        if($upid==0){
          $G['this']->data_alias=$old_data_alias;
        }
      }
    }
  }
  function export($ptoken,$upid=0,$xxx='',$tables=false,$alias=false){
    global $G;
    $datas=array();
    $namespace='site.'.$ptoken;
    if($alias===false){
      $T=onez('db')->open('data')->record("namespace='$namespace' and upid='$upid'$xxx order by id");
    }else{
      if($upid==0){
        $old_data_alias=$G['this']->data_alias;
        $G['this']->data_alias=$alias;
      }
      $T=$G['this']->data()->open('data')->db->record($alias,'*',"namespace='$namespace' and upid='$upid'$xxx order by id");
    }
    foreach($T as $rs){
      $rs['children']=$this->export($ptoken,$rs['id'],$xxx,$tables,$alias);
      if(count($rs['children'])==0){
        unset($rs['children']);
      }
      if($tables && is_array($tables) && !in_array($rs['tablename'],$tables)){
        continue;
      }
      $datas[]=$rs;
    }
    if($alias!==false){
      if($upid==0){
        $G['this']->data_alias=$old_data_alias;
      }
    }
    return $datas;
  }
}