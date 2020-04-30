<?php

/* ========================================================================
 * $Id: form.php 6567 2019-04-16 19:52:44Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_admin_widgets_form extends onezphp_admin_widgets{
  var $items=array();
  var $keys=array();
  function __construct(){
    
  }
  function submit(){
    $action=onez()->gp('action');
    if($action=='save'){
      $onez=array();
      foreach($this->items as $key=>$arr){
        if($arr['type']=='hidden' && !$arr['save']){
          continue;
        }
        if(strpos($arr['key'],'tmp-')!==false || strpos($arr['key'],'tmp_')!==false){
          continue;
        }
        $onez[$arr['key']]=onez()->gp($arr['key']);
        $file=$this->form_file($arr);
        if(!file_exists($file) && onez()->exists($arr['token'])){
          onez($arr['token'])->form_save($onez[$arr['key']],$arr);
        }
      }
      foreach($this->keys as $key){
        $onez[$key]=onez()->gp($key);
      }
      return $onez;
    }
    return false;
  }
  function form_file($arr){
    $file=dirname(__FILE__).'/form/'.$arr['type'].'.php';
    if(in_array($arr['type'],array('number','password','file'))){
      $file=dirname(__FILE__).'/form/text.php';
    }
    return $file;
  }
  function add($item){
    $item['token']=$item['type'];
    $item['type']=preg_replace('/[^0-9a-zA-Z_]+/i','',$item['type']);
    
    
    !$item['type'] && $arr['type']='text';
    if(!$item['key']){
      $item['key']='tmp_'.uniqid().'_'.count($this->items);
    }
    $this->items[$item['key']]=$item;
    return $this;
  }
  function addkey(){
    foreach(func_get_args() as $v){
      $this->keys[]=$v;
    }
  }
  function code($label_pos='top'){
    global $G;
    $this->html.='<style>.group-auto{display:none}</style>';
    $TYPE='code';
    $is_space=0;
    $values=$this->get('values');
    foreach($this->items as $key=>$arr){
      if($arr['type']=='space'){
        $is_space++;
      }
    }
    if($is_space){
      $md=intval(12/($is_space+1));
      $this->html.='<div class="row">';
      $this->html.='<div class="col-md-'.$md.' col-xs-'.$md.'">';
    }
    foreach($this->items as $key=>$arr){
      if($arr['type']=='space'){
        $this->html.='</div><div class="col-md-'.$md.' col-xs-'.$md.'">';
        continue;
      }elseif($arr['type']=='group'){
        $this->html.='</div></div><div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          '.$arr['label'].'
        </h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">';
        continue;
      }
      
      
      $value=$values[$arr['key']];
      if($arr['value']){
        $value=$arr['value'];
      }
      if(!$value){
        $value=$arr['default'];
      }
      if($arr['label'] && $arr['notempty']){
        $arr['label'].='<span class="text-red">*</span>';
      }
      
      if(trim($arr['group'])){
        $arr['group']='group-auto '.$arr['group'];
      }
      if($arr['cloud']){
        if(strpos($arr['cloud'],'?')===false){
          $arr['cloud'].='?';
        }else{
          $arr['cloud'].='&';
        }
        $arr['cloud'].='input='.$arr['key'];
        $cloudname=$arr['cloudname'];
        !$cloudname && $cloudname='选择';
        $arr['label'].=' <a href="'.$arr['cloud'].'" class="onez-miniwin btn btn-xs btn-success">'.$cloudname.'</a>';
        if(onez('admin')->times(1)){
          $G['footer-js'].=<<<ONEZ
$(function(){
  window.addEventListener('message', function(e){
    var data=e.data;
    if(typeof data.not_select!='undefined' && data.not_select){
      return;
    }
    for(var k in data){
      if($('#input-'+k).length>0){
        $('#input-'+k).val(data[k]);
        if(typeof OnezUploadResult=='function'){
          OnezUploadResult(k,'ok',data[k]);
        }
      }
    }
    closeWin();
  }, false);
});
ONEZ;
        }
      }
      $arr['value']=$value;
      if($arr['type']=='hidden'){
        $this->html.='<input type="hidden" id="input-'.$arr['key'].'" name="'.$arr['key'].'" value="'.$arr['value'].'" />';
        continue;
      }
      $file=$this->form_file($arr);
      if(!file_exists($file)){
        if(onez()->exists($arr['token'])){
          $r=onez($arr['token'])->form_code($arr);
          if($r){
            $this->html.=$r;
            continue;
          }
        }
        $file=dirname(__FILE__).'/form/text.php';
      }
      include($file);
      if($arr['add']){
        $this->html.=$arr['add'];
      }
    }
    if($is_space){
      $this->html.='</div>';
    }
    if($label_pos=='left'){
      $this->html=str_replace("\n",'',$this->html);
      $this->html=preg_replace('/<div\s*class="form\-group[^"]+"\>\s*\<label[^\>]+\>(.+?)\<\/label\>(.+?)\<\/div\>/is','<tr><th>$1</th><td>$2</td></tr>',$this->html);
      $this->html='<table class="table-form">'.$this->html.'</table>';
    }
    return $this->html;
  }
  function js(){
    $myjs='';
    foreach($this->items as $key=>$arr){
      if($arr['notempty']){
        $myjs.="if($('#input-$arr[key]').val().length<1){onez.alert(".var_export($arr['notempty'],1).");return false;}\n";
      }
    }
    $js.=<<<ONEZ
<script type="text/javascript">
$(function(){
  if($('#form-common').find('.group-auto').length>0){
    $('#form-common').find('.group-auto').hide();
    $('#form-common').find('.group-auto .form-control').each(function(){
      $(this).attr('data-name',$(this).attr('name'));
      $(this).attr('name','');
    });
    $('#form-common').find('select').bind('change',function(){
      var name=$(this).attr('name');
      if(name.length<1){
        return;
      }
      name=name.replace(/[^0-9a-zA-Z_]+/g,'');
      $('#form-common').find('.group-auto.'+name).hide();
      $('#form-common').find('.group-auto.'+name+' .form-control').each(function(){
        $(this).attr('name','');
      });
      $('#form-common').find('.group-auto.'+name+'-'+$(this).val()).show();
      $('#form-common').find('.group-auto.'+name+'-'+$(this).val()+' .form-control').each(function(){
        $(this).attr('name',$(this).attr('data-name'));
      });
    }).trigger('change');
  }
  
  $('#form-common').bind('submit',function(){
    try{
      $myjs
    }catch(e){
      
    }
    onez.formpost(this);
    return false;
  });
});
</script>
ONEZ;
    return $js;
  }
  function options($key=false){
    $options=array();
    $options['text']='单行文本';
    $options['textarea']='多行文本';
    $options['uicolor']='颜色选择';
    $options['select']='下拉选择';
    $options['checkbox']='开关';
    if($key){
      return $options[$key];
    }
    return $options;
  }
}