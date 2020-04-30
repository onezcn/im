<?php

/* ========================================================================
 * $Id: header.php 9199 2019-08-03 08:11:53Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
global $G;
$options=onez('cache')->get('options');
if($G['userid'] && $G['nickname']){
  $avatar=$G['avatar'];
  $nickname=$G['nickname'];
  $gradename=$G['gradename'];
  $href_main=$G['href_main']?$G['href_main']:onez()->href('');
  $href_logout=$G['href_logout']?$G['href_logout']:onez()->href('/logout.php');
  $this->menu_top_right.=<<<ONEZ
<ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="$avatar" class="user-image" alt="$nickname">
              <span class="hidden-xs">$nickname</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="$avatar" class="img-circle" alt="$nickname">

                <p>
                  $gradename
                  <small>$nickname</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="$href_main" class="btn btn-default btn-flat">管理首页</a>
                </div>
                <div class="pull-right">
                  <a href="$href_logout" class="btn btn-default btn-flat">安全退出</a>
                </div>
              </li>
            </ul>
          </li>
</ul>
ONEZ;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
  <title><?php echo $G['title']?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $this->url?>/assets/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $this->url?>/assets/css/ionicons.min.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<?php 
$ico=$G['ico'];
!$ico && $ico=$this->ico;
if($ico){?>
<link rel="icon" href="<?php echo $ico?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $ico?>" type="image/x-icon" />
<?php }?>
  <?php onez('jquery')->head()?>
  <?php onez('jqueryui')->head()?>
  <?php onez('bootstrap')->head()?>
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $this->url?>/assets/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo $this->url?>/assets/css/skins/<?php echo $this->style?>.min.css">
  
  <script src="<?php echo $this->url?>/assets/js/app.min.js"></script>
  <script src="<?php echo $this->url?>/assets/js/bootbox.js"></script>
  <?php 
  echo onez('ui')->js($this->url.'/assets/js/onez.js');
  ?>
  
  <link rel="stylesheet" href="<?php echo $this->url?>/assets/images/style.css">

<style>
.content-wrapper{
  
}
.table th{
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.miniwin{
  background: #ecf0f5;
}
.miniwin .main-header,.miniwin .content-header,.miniwin .main-sidebar{
  display: none;
}
.miniwin .wrapper{
  background-color: #fff;
  box-shadow: none;
}
.miniwin .content-wrapper{
  margin-left: 0;
}
.modal-header{
  padding:10px;
}
</style>
<script type="text/javascript">
window.onerror=function(){
  return false;
};
<?php if($G['webdomain']){
  echo 'document.domain="'.$G['webdomain'].'";';
}?>
<?php 
if(!$G['not_check_miniwin']){
$miniwin=onez()->gp('miniwin');
if(!$miniwin){
  ?>
if(top!=self){
  location.href='<?php echo onez()->cururl(array('miniwin'=>1))?>';
}
<?php }}?>
function menu_check_nums(s){
  var p=$(s);
  var maxnum=5;
  if(p.find('.navbar-nav').length>maxnum){
    var more=$('<ul class="dropdown-menu" role="menu"></ul>');
    p.find('.navbar-nav').each(function(i){
      if(i>=maxnum){
        $(this).find('li').appendTo(more);
        $(this).remove();
      }
    });
    var ul=$('<ul class="nav navbar-nav"></ul>');
    var li=$('<li class="dropdown"></li>');
    $('<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">更多 <span class="caret"></span></a>').appendTo(li);
    more.appendTo(li);
    li.appendTo(ul);
    ul.appendTo(p);
  }
}
</script>
<?php echo $G['header']?>
</head>
<?php 
if($this->topmenu){
  $menu='';
  $menu.='<ul class="nav navbar-nav">';
  foreach($this->topmenu as $v){
    $s='';
    if($v['url']==TOP_URL || $v['href']==TOP_URL){
      $s=' class="active"';
    }
    $target=$v['target'];
    !$target && $target='_self';
    $href=onez()->href($v['href']);
    if($v['url']){
      $href=$v['url'];
    }
    $menu.='<li'.$s.'><a href="'.$href.'" target="'.$target.'">'.$v['name'].'</a></li>';
  }
  $menu.='</ul>';
  $this->menu_top_left.=$menu;
}

#菜单处理开始
/*
if($G['this']){
  $mod=onez()->gp('mod');
  $mod=trim($mod,'/');
  list($grade)=explode('/',$mod);
  !$this->menu && $this->menu=array();
  #$this->old_menus=array($this->menu_top_left,$this->menu_top_right,$this->menu);
  $menus_hash=md5(serialize($this->menu));
  if($menus_hash!=$G['this']->option('menus.hash.left')){
    $G['this']->option_set(array('menus.hash.left'=>$menus_hash));
    $pos='left';
    $upid=0;
    $menus=$cmenus=array();
    foreach($this->menu as $v){
      if(!$v['href'] && !$v['url']){
        if($cmenus){
          $menus[]=$cmenus;
        }
        $hash=md5(serialize($v));
        $cmenus=array('name'=>$v['name'],'hash'=>$hash,'children'=>array());
        continue;
      }
      $hash=md5(serialize($v));
      $v['hash']=$hash;
      $cmenus['children'][]=$v;
    }
    if($cmenus){
      $menus[]=$cmenus;
    }
    $T=$G['this']->data()->open('menus')->delete("token='$grade' and key1='$pos' and key2<>'none'");
    foreach($menus as $v1){
      $onez=array();
      $onez['upid']=0;
      $onez['name']=$v1['name'];
      $onez['href']=$v1['href'];
      $onez['url']=$v1['url'];
      $onez['target']=$v1['target'];
      $onez['class']=$v1['class'];
      $onez['token']=$grade;
      $onez['key1']=$pos;
      $onez['key2']=$v1['hash'];
      $onez['index1']=1;
      $mid=$G['this']->data()->open('menus')->insert($onez);
      foreach($v1['children'] as $v2){
        $onez=array();
        $onez['upid']=$mid;
        $onez['name']=$v2['name'];
        $onez['href']=$v2['href'];
        $onez['url']=$v2['url'];
        $onez['target']=$v2['target'];
        $onez['class']=$v2['class'];
        $onez['token']=$grade;
        $onez['key1']=$pos;
        $onez['key2']=$v2['hash'];
        $onez['index1']=1;
        $G['this']->data()->open('menus')->insert($onez);
      }
    }
  }
  
  $menus=$G['this']->data()->open('menus')->record("upid='0' and token='$grade' and index1=1 order by step,id");
  
  if($menus){
    $this->menu_top_left.='<ul class="nav navbar-nav">';
    $this->menu_top_right.='<ul class="nav navbar-nav">';
    $this->menu=array();
    foreach($menus as $rs){
      $cmenus=$G['this']->data()->open('menus')->record("upid='$rs[id]' and index1=1 order by step,id");
      $mkey='';
      if($rs['key1']=='top.left'){
        $mkey='menu_top_left';
      }elseif($rs['key1']=='top.right'){
        $mkey='menu_top_right';
      }elseif($rs['key1']=='left'){
        if($rs['target']=='miniwin'){
          $rs['target']='_self';
          $rs['classname']='onez-miniwin';
        }
        $this->menu[]=$rs;
        foreach($cmenus as $r){
          if($r['target']=='miniwin'){
            $r['target']='_self';
            $r['classname']='onez-miniwin';
          }
          $this->menu[]=$r;
        }
      }
      if($mkey){
        if($cmenus){
          if($rs['target']=='miniwin'){
            $rs['target']='_self';
            $rs['class']='onez-miniwin';
          }
          $this->$mkey.='<li><a href="'.$rs['href'].'" target="'.$rs['target'].'" class="'.$rs['class'].'">'.$rs['name'].'</a></li>';
          $this->$mkey.='<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'.$rs['name'].' <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">';
          foreach($cmenus as $r){
            if($r['target']=='miniwin'){
              $r['target']='_self';
              $r['class']='onez-miniwin';
            }
            $this->$mkey.='<li><a href="'.$r['href'].'" target="'.$r['target'].'" class="'.$r['class'].'">'.$r['name'].'</a></li>';
          }
          $this->$mkey.='</ul>
            </li>';
        }else{
          if($rs['target']=='miniwin'){
            $rs['target']='_self';
            $rs['class']='onez-miniwin';
          }
          $this->$mkey.='<li><a href="'.$rs['href'].'" target="'.$rs['target'].'" class="'.$rs['class'].'">'.$rs['name'].'</a></li>';
        }
      }
    }
    $this->menu_top_left.='</ul>';
    $this->menu_top_right.='</ul>';
  }
  $this->menu_top_left.='<ul class="nav navbar-nav"><li><a href="'.onez('site')->href('/diy/menus.php').'" data-reload="1" class="onez-miniwin">自定义菜单</a></li></ul>';
}
*/
#菜单处理结束

if($this->menu){
  include(dirname(__FILE__).'/body_sidebar.php');
}else{
  include(dirname(__FILE__).'/body_nosidebar.php');
}
?>