<?php

/* ========================================================================
 * $Id: factory.form.php 20964 2020-04-28 12:16:18Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#名称：佳蓝智能表单
#标识：factory.form

class onezphp_factory_form extends onezphp{
  var $items=array();
  var $tabs=array();
  var $keys=array();
  var $btns=array();
  var $type='mobile';
  var $form_path='form';
  var $formId='form-common';
  var $orientation='vertical';#vertical,horizontal
  function __construct(){
    if(defined('IS_M_API')){
      $this->mobile();
    }else{
      $this->pc();
    }
  }
  function widget(){
    return $this;
  }
  function mobile(){
    $this->type='mobile';
    $this->form_path='form';
    return $this;
  }
  function pc(){
    $this->type='pc';
    $this->form_path='form.pc';
    return $this;
  }
  function horizontal(){
    $this->orientation='horizontal';
    return $this;
  }
  function vertical(){
    $this->orientation='vertical';
    return $this;
  }
  function text2options($text){
    $options=array();
    foreach(explode("\n",$text) as $v){
      $v=trim($v);
      if($v){
        if(strpos($v,'=')!==false){
          list($a,$b)=explode('=',$v);
          $a=trim($a);
          $b=trim($b);
          $options[$a]=$b;
        }else{
          $v=trim($v);
          $options[$v]=$v;
        }
      }
    }
    return $options;
  }
  function formtypes($key=false){
    global $G;
    $options=array();
    $options['text']='单行字符串';
    $options['textarea']='多行文本';
    $options['switch']='开关';
    $options['select']='下拉选择';
    $options['number']='数字';
    $options['money']='金额';
    $options['image']='选择图片';
    $options['icon']='选择图标';
    $options['video']='选择视频';
    $options['audio']='选择音频';
    $options['fonticon']='字体图标';
    $options['editor']='编辑器';
    
    $glob=glob(dirname(__FILE__).'/form/*.php');
    if($glob){
      foreach($glob as $k=>$v){
        $token=substr(basename($v),0,-4);
        if(!isset($options[$token])){
          $options[$token]=$token;
        }
      }
    }
    unset($options['formtype']);
    $G['this']->formtypes($options);
    if($key!==false){
      return $options[$key];
    }
    return $options;
  }
  function form_file($arr){
    $file=dirname(__FILE__).'/'.$this->form_path.'/'.$arr['type'].'.php';
    return $file;
  }
  function hidden($key,$value){
    $file=dirname(__FILE__).'/'.$this->form_path.'/'.$arr['type'].'.php';
    return array(
      'type'=>'onez-input',
      'itemstyle'=>'display:none',
      'key'=>$key,
      'value'=>$value,
    );
  }
  function submit($key='action'){
    $submit=false;
    if($this->type=='pc'){
      $submit=(onez()->gp($key)=='save');
    }else{
      $submit=defined('IS_POST');
    }
    if($submit){
      $onez=array();
      $values=$this->get('values');
      foreach($this->items as $key=>$arr){
        if($arr['type']=='hidden' && !$arr['save']){
          $_REQUEST[$arr['key']]=$arr['value'];
          continue;
        }
        if(strpos($arr['key'],'tmp-')!==false || strpos($arr['key'],'tmp_')!==false){
          continue;
        }
        if($values[$arr['key']] && !isset($_REQUEST[$arr['key']])){
          #continue;
        }
        $onez[$arr['key']]=onez()->gp($arr['key']);
        
        $file=$this->form_file($arr);
        if(!file_exists($file) && $this->exists($arr['token'])){
          $r=$this->plugin($arr['token'])->form_save($onez[$arr['key']],$arr,$this);
          if($r=='delete'){
            unset($onez[$arr['key']]);
          }
          $file=dirname(__FILE__).'/'.$this->form_path.'/text.php';
        }
        $MODE='post';
        include($file);
      }
      foreach($this->keys as $key){
        $onez[$key]=onez()->gp($key);
      }
      return $onez;
    }
    return false;
  }
  function addBtn($btn){
    $this->btns[]=$btn;
    return $this;
  }
  function add($item,$tab=false){
    if($tab!==false){
      $item['tab']=$tab;
      if(!in_array($tab,$this->tabs)){
        $this->tabs[]=$tab;
      }
    }
    $item['token']=$item['type'];
    $item['type']=preg_replace('/[^0-9a-zA-Z_\.]+/i','',$item['type']);
    !$item['type'] && $arr['type']='text';
    if(!$item['key']){
      $item['key']='tmp_'.uniqid().'_'.count($this->items);
    }
    if(onez()->exists($item['token'])){
      $r=onez($item['token'])->form_on_add($item,$this);
      if($r){
        foreach($r as $v){
          $this->items[$v['key']]=$v;
        }
        return $this;
      }
    }
    $this->items[$item['key']]=$item;
    if($item['summary']){
      if($this->type=='pc'){
        $this->items['tmp_'.uniqid().'_'.count($this->items)]=array(
          'type'=>'html',
          'html'=>'<div class="form-summary">'.$item['summary'].'</div>',
        );
      }else{
        $this->items['tmp_'.uniqid().'_'.count($this->items)]=array(
          'type'=>'space',
          'subject'=>$item['summary'],
        );
      }
    }
    return $this;
  }
  function addkey(){
    foreach(func_get_args() as $v){
      $this->keys[]=$v;
    }
  }
  function plugin($ptoken){
    return $this;
  }
  function exists($ptoken){
    return onez()->exists($ptoken);
  }
  function code_tabs($code_option,$values,&$record){
    global $G;
    $code='';
    #选项卡
    if($this->tabs||$code_option['tab']){
      $basename=$code_option['basename'];
      !$basename && $basename='基础';
      $tabs=array();
      foreach($this->items as $key=>$arr){
        if(!$arr['tab']){
          $arr['tab']=$basename;
        }
        $tabs[$arr['tab']][$key]=$arr;
      }
      $code.='<div class="nav-tabs-custom">';
        $code.='<ul class="nav nav-tabs ui-sortable-handle">';
        $tabIndex=0;
        foreach($tabs as $name=>$items){
          $tabIndex++;
          $code.='<li'.($tabIndex==1?' class="active"':'').'><a href="#tab'.$tabIndex.'" data-toggle="tab" aria-expanded="false">'.$name.'</a></li>';
        }
        if($code_option['tabSubmit']){
          $btnname=$this->get('btnname');
          !$btnname && $btnname='确定';
          $code.='<li class="pull-right"><button type="submit" class="btn btn-primary">'.$btnname.'</button></li>';
        }
        $code.='</ul>';
        $code.='<div class="tab-content">';
        $tabIndex=0;
        foreach($tabs as $name=>$items){
          $tabIndex++;
          $code.='<div class="tab-pane'.($tabIndex==1?' active':'').'" id="tab'.$tabIndex.'">';
          $code.=$this->code_items($code_option,$items,$values,$record);
          $code.='</div>';
        }
        $code.='</div>';
      $code.='</div>';
    }else{
      $code.=$this->code_items($code_option,$this->items,$values,$record);
    }
    return $code;
  }
  function code_items($code_option,$CODE_ITEMS,$values,&$record){
    global $G;
    
    $code.='<div class="row">';
    foreach($CODE_ITEMS as $key=>$arr){
      if($arr['if']){
        list($a,$b)=explode('==',$arr['if']);
        if($values[$a]!=$b){
          continue;
        }
      }
      if($arr['token']=='space'){
        $record[]=array(
          'type'=>'space',
          'subject'=>(string)$arr['other'],
        );
        
        if($this->type=='pc' && $arr['other']){
          $code.='<div class="form-group'.$code_option['class_item'].'">';
            $code.='<div class="row">';
              #$code.='<div class="'.$code_option['class_left'].'"></div>';
              $code.='<div class="'.$code_option['class_right'].'">';
                $code.=$arr['other'];
              $code.='</div>';
            $code.='</div>';
          $code.='</div>';
        }
        continue;
      }
      
      
      $file=$this->form_file($arr);
      if(!file_exists($file)){
        if($this->exists($arr['token'])){
          $this->plugin($arr['token'])->form_code($arr,$this);
        }
        $file=dirname(__FILE__).'/'.$this->form_path.'/text.php';
      }
      $item=array();
      $item['id']=$key;
      $item['hint']=(string)$arr['hint'];
      $item['notempty']=(string)$arr['notempty'];
      $item['key']=$key;
      $value=$values[$arr['key']];
      if($arr['value']){
        $value=$arr['value'];
      }
      if(!$value && !isset($values[$arr['key']])){
        $value=$arr['default'];
      }
      $item['value']=$value?$value:'';
      
      $items=array();
      $itemBox=array('type'=>'form-item',);
      $MODE='form';
      if($code_option['show']){
        $MODE='show';
      }
      
      
      #兼容旧接口
      if($this->type=='pc' && strpos($arr['token'],'form.')!==false){
        $_arr=array_merge($arr,$item);
        $_arr['label']=' ';
        $r=onez($arr['token'])->form_code($_arr);
      }elseif($this->type=='pc' && $arr['ptoken']){
        $_arr=array_merge($arr,$item);
        $_arr['label']=' ';
        $r=onez($arr['ptoken'])->form_code($_arr);
      }else{
        $r=include($file);
      }
      $classname='';
      $attrs='';
      if($this->type=='pc'){
        if($arr['group']){
          $classname.=' group-auto';
          foreach(explode(' ',$arr['group']) as $g){
            $g=trim($g);
            if($g){
              list($a,$b)=explode('=',$g);
              $attrs.=' data-group-token="'.$a.'" data-'.$a.'="'.$b.'"';
            }
          }
        }
        if($arr['type']=='hidden'){
          $code.='<input type="hidden" id="input-'.$key.'" name="'.$key.'" value="'.$item['value'].'">';
          continue;
        }
        if(!$code_option['show'] && $arr['cloud']){
          if(strpos($arr['cloud'],'?')===false){
            $arr['cloud'].='?';
          }else{
            $arr['cloud'].='&';
          }
          $arr['cloud'].='input='.$arr['key'];
          $cloudname=$arr['cloudname'];
          !$cloudname && $cloudname='选择';
          $r.='<p><button type="button" data-cloud="'.$arr['cloud'].'" data-kw="'.$arr['kw'].'" class="btn btn-xs btn-success">'.$cloudname.'</button></p>';
          if(!$code_option['v2'] && onez('admin')->times(1)){
            $G['footer-js'].=<<<ONEZ
$(function(){
  window.addEventListener('message', function(e){
    var data=e.data;
    if(typeof data.not_select!='undefined' && data.not_select){
      return;
    }
    $(document).trigger('on-message',data);
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
        $code.='<div class="form-group'.$code_option['class_item'].$classname.'"'.$attrs.'>';
          $code.='<div class="row">';
          if($arr['label'] && $arr['notempty']){
            $arr['label']=' <span style="color:red">*</span> '.$arr['label'];
          }
            $arr['label'] && $code.='<label for="input-'.$key.'" class="control-label'.$code_option['class_left'].'">'.$arr['label'].'</label>';
            $class='';
            if($code_option['show']){
              $class.=' form-type-html';
            }
            $code.='<div class="'.$code_option['class_right'].$class.' form-type-'.str_replace('.','-',$arr['type']).' '.$classRight.'">';
            $code.=$r;
            $code.='</div>';
          $code.='</div>';
        $code.='</div>';
        continue;
      }
      if($itemBox['type']=='form-item' && (!$items || !is_array($items))){
        $record[]=array(
          'type'=>'html',
          'html'=>'<div style="color:red;line-height:2">[ERROR]'.json_encode($arr).'</div>',
        );
      }
      if(!$itemBox['label'] && $this->orientation=='vertical'){
        if($arr['label']){
          $record[]=array(
            'type'=>'space',
            'subject'=>$arr['label'],
          );
        }
      }else{
        $itemBox['label']=$arr['label'];
      }
      $itemBox['items']=$items;
      $record[]=$itemBox;
    }
    $code.='</div>';
    return $code;
  }
  function code2($code_option=array()){
    global $G;
    $code_option['v2']=1;
    $G['footer']=$G['footer-js']='';
    $this->formId='form-'.md5(serialize($_GET));
    $code='<form id="'.$this->formId.'" method="post">';
    $code.=$this->code($code_option);
    $code.=$this->js();
    $code.='<input type="hidden" name="_ajax_action" value="'.onez()->gp('action').'">';
    $code.='</form>';
    $js='';
    $code=str_replace('<script>','<script type="text/javascript">',$code);
    $code=preg_replace_callback('/\<script[^\>]+\>(.*?)\<\/script\>/is',function($mat)use(&$js){
      $js.=$mat[1];
      return '';
    },$code);
    $code=str_replace('input-','input-'.$this->formId.'-',$code);
    $code=str_replace('onez_file-','onez_file-'.$this->formId.'-',$code);
    
    $js.=$G['footer-js'];
    $js=str_replace('input-','input-'.$this->formId.'-',$js);
    $js=str_replace('onez_file-','onez_file-'.$this->formId.'-',$js);
    $item=array(
      'type'=>'form',
      'formId'=>$this->formId,
      'html'=>$code,
      'js'=>$js,
    );
    if($code_option['asTpl']){
      return '';
    }
    return $item;
  }
  function code($code_option=array()){
    global $G;
    
    $class_item=' col-md-12 col-xs-12';
    $class_left=' col-sm-2 col-xs-12';
    $class_right=' col-sm-10 col-xs-12';
    if($this->orientation=='vertical'){
      $class_item=' col-md-12 col-xs-12';
      $class_left=' col-sm-12 col-xs-12';
      $class_right=' col-sm-12 col-xs-12';
    }elseif($this->orientation=='horizontal'){
      $class_item=' col-md-12 col-xs-12';
      $class_left=' col-sm-2 col-xs-2';
      $class_right=' col-sm-10 col-xs-10';
    }
    !$code_option['class_item'] && $code_option['class_item']=$class_item;
    !$code_option['class_left'] && $code_option['class_left']=$class_left;
    !$code_option['class_right'] && $code_option['class_right']=$class_right;
    
    $values=$this->get('values');
    $action=onez()->gp('action');
    if($action=='update'){
      !$values && $values=array();
      $values=array_merge($values,$_POST);
    }
    $record=array();
    $code='';
    
    $code.=$this->code_tabs($code_option,$values,$record);
    if($code_option['submit']!==false){
      $btnname=$this->get('btnname');
      !$btnname && $btnname='确定';
      $record[]=array(
        'type'=>'button',
        'name'=>$btnname?$btnname:'确定',
        'token'=>'submit',
      );
    }
    if($this->type=='pc'){
      $code='<style>
@media (min-width: 768px) {
  .form-group label.form-left{
    font-weight:normal;
    text-align:right;
    padding-top: 7px;
  }
  .form-group .form-type-html{
    padding-top: 9px;
  }
}
.form-horizontal .tab-content .tab-pane>.row{
  margin-left:0px;
  padding:15px 0;
}
.form-horizontal .form-type-radio{
  position: relative;
  top: 5px;
}
.form-group .form-group>label{
  display:none;
}
</style>'.$code;
      if($code_option['submit']!==false){
        $code.='<div class="row">';
            $code.='<div class="form-left'.$code_option['class_left'].'"></div>';
            $code.='<div class="'.$code_option['class_right'].'">';
            $code.='<button type="submit" class="btn btn-primary">'.$btnname.'</button>';
            if($this->btns){
              foreach($this->btns as $btn){
                $code.=' <button type="button"';
                foreach($btn as $k=>$v){
                  if($k=='class'){
                    $v='btn '.$v;
                  }
                  $code.=' '.$k.'="'.$v.'"';
                }
                $code.='>';
                $code.=$btn['name'];
                $code.='</button>';
              }
            }
            $code.='</div>';
          $code.='</div>';
        #$code.='</div>';
      }
      $action=onez()->gp('action');
      if($action=='update'){
        $A=array();
        $A['code']=$code;
        $A['js']=$this->js();
        onez()->output($A);
      }
      $code='<div id="'.$this->formId.'-code-body" class="form-'.$this->orientation.'">'.$code.'</div>';
      return $code;
    }
    return $record;
  }
  function gp($key,$default=''){
    $value=onez()->gp($key);
    if(!$value){
      $values=$this->get('values');
      $value=$values[$key];
    }
    if(!$value){
      $value=$default;
    }
    return $value;
  }
  function value($key,$default=''){
    $arr=$this->items[$key];
    if(!$arr){
      return false;
    }
    $values=$this->get('values');
    $action=onez()->gp('action');
    if($action=='update'){
      !$values && $values=array();
      $values=array_merge($values,$_POST);
    }
    $value=$values[$arr['key']];
    if($arr['value']){
      $value=$arr['value'];
    }
    if(!isset($values[$arr['key']])){
      $value=$arr['default'];
    }
    return $value?$value:$default;
  }
  function js(){
    $formId=$this->formId;
    $myjs='';
    $cloud=1;
    $if=array();
    foreach($this->items as $key=>$arr){
      if($arr['notempty']){
        $myjs.="if($('#input-$arr[key]').val().length<1){onez.alert(".var_export($arr['notempty'],1).");return false;}\n";
      }
      if($arr['update']){
        $if[]="#input-$arr[key]";
      }
      if($arr['cloud']){
        $cloud++;
      }
    }
    if($myjs){
      $myjs='try{'.$myjs.'}catch(e){}';
    }
    $code='';
    if($if){
      $if=implode(',',$if);
      $code.=<<<ONEZ
  onez.watchFormCan=true;
  onez.watchForm=function(){
    if(!onez.watchFormCan){
      return;
    }
    onez.watchFormCan=false;
    setTimeout(function(){
      onez.watchFormCan=true;
    },100);
    $.post(window.location.href,$('#$formId').serialize().replace('action=save','action=update')+'&ajax=1',function(data){
      $('$if').unbind('change blur');
      $('#$formId').find('select').unbind('change');
      $('#$formId').unbind('submit');
      $('#$formId-js').remove();
      if(data.code){
        $('#$formId-code-body').html(data.code).find('a.onez-miniwin[href]').each(function(){
          var href=$(this).attr('href');
          if(href.indexOf('javascript:;')==-1){
            $(this).attr('data-href',href);
            $(this).attr('href','javascript:;');
            $(this).click(function(){
              var href=$(this).attr('data-href');
              var title=$(this).attr('data-title');
              if(typeof title=='undefined' || !title || title.length<1){
                title=$(this).text();
              }
              openWin(href,title,parseInt($(this).attr('data-width')+''),$(this).attr('data-reload'),parseInt($(this).attr('data-height')+''));
            });
          }
        });
        if(typeof onez.hash=='function'){
          var hash = onez.hash('$formId');
          hash && $('ul.nav a[href="#' + hash + '"]').tab('show');
        }else{
          var hash = window.location.hash;
          hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        }
      }
      if(data.js){
        $(data.js).attr('id','$formId-js').appendTo('body');
      }
      
    },'json');
  };
  $('$if').each(function(){
    var tagName=$(this).get(0).tagName.toLocaleLowerCase();
    var eventName='blur';
    if(tagName=='select'){
      eventName='change';
    }
    $(this).unbind(eventName).bind(eventName,onez.watchForm);
  });
ONEZ;
    }
    if($cloud){
      $code.=<<<ONEZ
  $('#$formId [data-cloud]').unbind('click').bind('click',function(){
    var data=$(this).data();
    var url=data.cloud;
    if(!data.kw){
      data.kw=$('input[type="text"]').attr('name');
    }
    url=url.replace('&kw=','&kw='+encodeURIComponent($('#input-'+data.kw).val()));
    openWin(url,$(this).text());
  });
ONEZ;
    }
    $js.=<<<ONEZ
<script type="text/javascript" id="$formId-js">
$(function(){
$code
  
  if(typeof onez.hash=='function'){
    var hash = onez.hash('$formId');
    hash && $('ul.nav a[href="#' + hash + '"]').tab('show');
    $('.nav-tabs a').click(function (e) {
      onez.hash('$formId',$(this).attr('href').substr(1));
    });
  }else{
    var hash = window.location.hash;
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');
    $('.nav-tabs a').click(function (e) {
      window.location.hash = this.hash;
    });
  }
  if($('#$formId').find('.group-auto').length>0){
    $('#$formId').find('.group-auto').hide();
    $('#$formId').find('[data-group-token]').each(function(){
      var token=$(this).attr('data-group-token');
      var sel=$('#input-'+token);
      if(!sel.hasClass('group-auto-ready')){
        sel.addClass('group-auto-ready');
        sel.unbind('change').bind('change',function(){
          var token=$(this).attr('name');
          var value=$(this).val();
          $('#$formId').find('[data-group-token="'+token+'"]').hide();
          $('#$formId').find('[data-'+token+'="'+value+'"]').show();
        }).trigger('change');
      }
    });
  }
  $('#$formId input[name="action"]').eq(1).remove();
  $('#$formId').unbind('submit').bind('submit',function(){
    $myjs
    onez.formpost(this);
    return false;
  });
});
</script>
ONEZ;
    return $js;
  }
  function output(&$A,&$record){
    $r=$this->code();
    if($r){
      $record=array_merge($record,$r);
    }
  }
}