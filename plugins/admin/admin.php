<?php

/* ========================================================================
 * $Id: admin.php 8581 2019-06-30 13:37:18Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets{
  var $vars=array();
  var $code;
  function code(){
    return $this->html;
  }
  function show(){
    if($this->code){
      echo $this->code;
    }else{
      echo $this->code();
    }
    return $this;
  }
  function add($html){
    $this->html.=$html;
    return $this;
  }
  function get($key,$def=false){
    $value=$this->vars[$key];
    if($def!==false && !isset($this->vars[$key])){
      return $def;
    }
    return $value;
  }
  function set($key,$value){
    $this->vars[$key]=$value;
    return $this;
  }
  function setByArray($vars){
    if(is_array($vars)){
      foreach($vars as $k=>$v){
        $this->set($k,$v);
      }
    }
    return $this;
  }
  function wrap($name,$opt,$methods=false){
    if(in_array($name,array('box'))){
      $name=preg_replace('/[^0-9a-zA-Z_]+/i','',$name);
      $file=dirname(__FILE__).'/widgets/'.$name.'.php';
      if(file_exists($file)){
        $wrap=onez('admin')->widget($name)->setByArray($this->vars)->setByArray($opt);
        if($methods){
          foreach($methods as $k=>$v){
            call_user_func_array(array($wrap,$k),$v);
          }
        }
        $this->code=$wrap->set('html',$this->code())->code();
        return $this;
      }
    }
    $A='<'.$name;
    foreach($opt as $k=>$v){
      $A.=' '.$k.'="'.$v.'"';
    }
    $A.='>';
    $B='</'.$name.'>';
    
    if($this->code){
      $this->code=$A.$this->code.$B;
    }else{
      $this->code=$A.$this->code().$B;
    }
    return $this;
  }
}
class onezphp_admin extends onezphp{
  var $style='skin-blue';
  var $boxed=1;
  var $title_mini='管理';
  var $title='管理中心';
  var $theme=null;
  function __construct(){
    
  }
  
  function theme($theme_token,$default='skin-blue'){
    if(!$theme_token){
      $this->style=$default;
      return;
    }
    if(strpos($theme_token,'skin-')!==false){
      $this->style=$theme_token;
      return;
    }
    if(!onez()->exists($theme_token)){
      $this->style=$default;
      return;
    }
    $this->theme=onez($theme_token);
    onez('call')->add($theme_token);
  }
  function header(){
    global $G;
    onez('call')->call('page_header');
    include(dirname(__FILE__).'/header.php');
    onez('call')->call('admin_header');
  }
  
  function footer($type='user'){
    global $G;
    onez('call')->call('admin_footer');
    include(dirname(__FILE__).'/footer.php');
  }
  function widget($name){
    global $G;
    $name=preg_replace('/[^0-9a-zA-Z_]+/i','',$name);
    $file=dirname(__FILE__).'/widgets/'.$name.'.php';
    if(file_exists($file)){
      include_once($file);
      $clsName="onezphp_admin_widgets_$name";
      if(class_exists($clsName)){
        return new $clsName();
      }
    }
  }
  //哪个菜单处理激活状态
  function menu_active(&$menu){
    $CUR_URL=CUR_URL;
    if(strpos($CUR_URL,'_onez')!==false){
      list(,$CUR_URL)=explode('_onez',$CUR_URL);
    }
    foreach($menu as $k=>&$v){
      if($v['href']==$CUR_URL || $v['url']==$CUR_URL){
        $v['current']=1;
        return 1;
      }
      if($v['children']){
        $r=$this->menu_active($v['children']);
        if($r){
          $v['current']=1;
          return 1;
        }
      }
    }
    foreach($menu as $k=>&$v){
      if(strpos($v['href'],$CUR_URL)!==false || strpos($v['url'],$CUR_URL)!==false){
        $v['current']=1;
        return 1;
      }
      if($v['children']){
        $r=$this->menu_active($v['children']);
        if($r){
          $v['current']=1;
          return 1;
        }
      }
    }
    return 0;
  }
  //显示菜单
  function showmenu($menu,$step=false){
    if(!empty($menu['tree'])){
      echo onez('admin.menu')->html($menu['tree']);
      return;
    }
    //分组
    if($step===false){
      $gMenu=array();
      foreach($menu as $k=>$v){
        if($v['group']){
          $gMenu[$v['group']][]=$v;
        }else{
          $gMenu['none'][]=$v;
        }
      }
      if($gMenu['common']){
        $first=$gMenu['common'];
        unset($gMenu['common']);
        $gMenu=array_merge(array('common'=>$first),$gMenu);
      }
      if($gMenu['setting']){
        if($gMenu['setting'][0]['href']){
          array_unshift($gMenu['setting'],array(
            'name' => '基本设置',
            'href' => '',
            'icon' => '',
          ));
        }
      }
      if($gMenu['setting']){
        $setting=$gMenu['setting'];
        unset($gMenu['setting']);
        $gMenu=array_merge(array('setting'=>$setting),$gMenu);
        
      }
      $menu=array();
      foreach($gMenu as $V){
        $menu=array_merge($menu,$V);
      }
    }
    if($step===false && defined('CUR_URL')){
      $this->menu_active($menu);
    }
    foreach($menu as $v){
      if($step===false){
        if(!$v['href'] && !$v['url']){
          $s=$v['icon']?'<i class="'.$v['icon'].'"></i> ':'';
          echo'<li class="header">'.$s.$v['name'].'</li>';
          continue;
        }
      }
      $target=$v['target'];
      !$target && $target='_self';
      $href=onez('admin')->href($v['href']);
      if($v['url']){
        $href=$v['url'];
      }
      if($v['children']){
        echo'<li class="treeview'.($v['current']?' active':'').'" '.$v['li'].'>';
        echo'<a href="#" '.$v['a'].'><i class="'.$v['icon'].'"></i> <span>'.$v['name'].'</span> <i class="fa fa-angle-left pull-right"></i></a>';
        echo'<ul class="treeview-menu">';
        $this->showmenu($v['children'],$step+1);
        echo'</ul>';
      }else{
        !$v['icon'] && $v['icon']='fa fa-circle-o';
        echo'<li'.($v['current']?' class="active"':'').' '.$v['li'].'>';
        echo'<a href="'.$href.'" target="'.$target.'" class="'.$v['classname'].'" '.$v['a'].'><i class="'.$v['icon'].'"></i> <span>'.$v['name'].'</span></a>';
        echo'</li>';
      }
      echo'</li>';
    }
  }
  //自动显示后台内页面
  function start(){
    $mod=onez()->gp('mod');
    if(!onez()->start()){
      onez('admin')->header();
      echo 'MOD"'.$mod.'"不存在';
      onez('admin')->footer();
      exit();
    }
  }
  function href($href, $inplugin = 0){
    return onez()->href($href);
  }
  function showfield($rs, $f){
    global $G;
    $key=$f['key1'];
    $value=trim($rs[$key]);
    if($f['showtype']=='time'){
      $value= date('Y-m-d H:i:s',$value);
    }elseif($f['showtype']=='thumb'){
      $value= '<img src="'.onez('m.plugin')->thumb($value).'" width="32" />';
    }elseif($f['showtype']=='category'){
      $T=$G['this']->data()->open('dev.category')->one("id='$f[category]'");
      $options=array();
      foreach(explode("\n",$T['items']) as $v){
        list($a,$b)=explode('=',$v);
        $options[$a]=$b;
      }
      $value=$options[$value];
    }elseif($f['showtype']=='table'){
      $T=$G['this']->data()->open($f['table'])->record("1");
      $options=array();
      foreach($T as $r){
        $options[$r[$f['table_value']]]=$r[$f['table_name']];
      }
      $value=$options[$value];
    }elseif($f['showtype']=='url'){
      $value= '<a href="'.$value.'" target="_blank">'.$value.'</a>';
    }elseif($f['showtype']=='qrcode'){
      $value= '<img src="http://qrcode.onez.cn/api.php?size=256&text='.urlencode($value).'">';
    }elseif($f['showtype']=='bool'){
      $value= $value?'<span class="btn btn-xs btn-success">是</span>':'<span class="btn btn-xs btn-danger">否</span>';
    }
    if($f['mytext']){
      $json=array();
      if(strpos($f['mytext'],'onez://')!==false){
        foreach(explode('/',trim(substr($f['mytext'],7),'/')) as $v){
          list($a,$b)=explode('=',$v);
          if($a && $b){
            $json[$a]=$b;
          }
        }
      }elseif(substr($f['mytext'],0,1)=='{' && substr($f['mytext'],-1)=='}'){
        $f['mytext'] = str_replace(array('&quot;','&#39;','    ','&nbsp;&nbsp;'),array('"',"'","\t",'  '),$f['mytext']);
        $json=json_decode($f['mytext'],1);
      }
      if($json && $json['type']){
        if($json['type']=='plugin'){
          (!$json['ptoken']||!$json['ptoken']=='this') && $json['ptoken']=$G['this']->token;
          if($json['ptoken']!='admin' && onez()->exists($json['ptoken'])){
            !$json['method'] && $json['method']='showfield';
            $method=$json['method'];
            if(method_exists(onez($json['ptoken']),$json['method'])){
              return onez($json['ptoken'])->$method($value);
            }
          }
        }
      }else{
        $value=preg_replace('/\$\{([a-zA-Z0-9_]+)\}/ie',"\$rs['$1']",$f['mytext']);
      }
    }
    return $value;
  }
}