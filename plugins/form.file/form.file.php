<?php

/* ========================================================================
 * $Id: form.file.php 14416 2020-04-22 17:41:39Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_form_file extends onezphp{
  var $exts=array();
  var $exts_default=array('jpg','gif','svg','png','mp3','mp4','ogg','tmp','txt','zip','rar','doc','ttf');
  var $exts_disabled=array('php','asp','jsp');
  function __construct(){
    $this->exts=$this->exts_default;
  }
  function form_code($arr){
    global $G;
    $html='';
    #只加载一次
    if($this->times(1)){
      $request='?_p='.$this->token.'&_m=savefile';
      $html.=<<<ONEZ
<script type="text/javascript">
window.createXHR=function (){ 
    if(typeof XMLHttpRequest != "undefined"){ 
        return new XMLHttpRequest(); 
    }else if(typeof ActiveXObject != "undefined"){ 
        //适合IE7之前的版本 
        if(typeof arguments.callee.activeXString != "string"){ 
            var versions = ["MSXML2.XMLHttp.6.0","MSXML2.XMLHttp.3.0","MSXML.XMLHttp"]; 
            for(var i=0,len=versions.length; i<len; i++){ 
                try{ 
                    var xhr = new ActiveXObject(versions[i]); 
                    arguments.callee.activeXString = versions[i]; 
                    return xhr; 
                }catch (ex){ 
                } 
            } 
        } 
        
        return new ActiveXObject(arguments.callee.activeXString); 
    }else{ 
        throw new Error("No XHR object available."); 
    }; 
};
window.onez_upload=function(obj,type,callback){
  $('#onez_file').remove();
  var accept='';
  if(type=='image'){
    accept='image/gif,image/jpeg,image/jpg,image/png,image/svg';
  }
  if(accept!=''){
    accept=' accept="'+accept+'"';
  }
  var file=$('<input type="file" id="onez_file"'+accept+' style="visibility: hidden;" />').appendTo('body');
  try{
    var onChange=function(){
      if(this.files.length>0){
        var xhr = window.createXHR();
        xhr.open("post",'$request&ftype='+type+'&action='+type, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.onreadystatechange = function(){
          if (xhr.readyState == 4){
            var flag = xhr.responseText;
            try{
              var json=JSON.parse(flag);
              if(typeof json.url=='string'){
                if(typeof callback=='function'){
                  callback(json);
                }
              }else{
                onez.alert(json.error);
              }
            }catch(e){
            }
          }
        };
        var fd = new FormData(); 
        fd.append("file", this.files[0]); 
        xhr.send(fd); 
      }
    };
    file.get(0).addEventListener("change",onChange);
  }catch(e){
  }
  $('#onez_file').trigger('click');
};

$(document).ready(function(){
  if (typeof(Worker)!="undefined"){
    $('object').each(function(){
      var input=$(this).attr('data-input');
      var type=$(this).attr('data-type');
      var flash=$(this).attr('data-flash');
      var tmpkey=$(this).attr('data-tmpkey');
      if(typeof flash!='undefined' && flash=='1'){
        return true;
      }
      if(typeof type=='undefined' || type==''){
        type='image';
      }
      var btn=$('<a href="javascript:;" style="color:#00c" data-input="'+input+'" data-type="'+type+'" data-tmpkey="'+tmpkey+'">点击开始上传</a>');
      $(this).replaceWith(btn);
      btn.bind('click',function(){
        var tmpkey=$(this).attr('data-tmpkey');
        var type=$(this).attr('data-type');
        window.onez_upload(this,type,function(data){
          var pos=data.url.indexOf('|');
          if(pos!=-1){
            var name=data.url.substr(pos+1);
            data.url=data.url.substr(0,pos);
          }
          $('[data-tmpkey="'+tmpkey+'"]').closest('.form-group').find('input:text').val(data.url);
          if(data.url.indexOf('.jpg')!=-1 || data.url.indexOf('.png')!=-1 || data.url.indexOf('.gif')!=-1 || data.url.indexOf('.jpeg')!=-1 || data.url.indexOf('.svg')!=-1){
            $('#preview-'+tmpkey).html('<img src="'+data.url+'" onload="if(this.width>300)this.width=300" style="max-width:300px;max-height:300px;" />');
          }
        });
      });
    });
  }
});
function OnezUploadResult(type,result,data){
  if(result=='ok'){
    var pos=data.indexOf('|');
    if(pos!=-1){
      var name=data.substr(pos+1);
      data=data.substr(0,pos);
      var nameObj=$('[data-tmpkey="'+type+'"]').closest('.form-group').find('input:text');
      var input_name;
      if(nameObj.length>0){
        input_name=nameObj.attr('name');
        $('[name="'+input_name+'_name"]').val(name);
      }
      $(document).trigger('onez-upload',{input:input_name,name:name});
    }
    if($('[data-tmpkey="'+type+'"]').length<1){
      type=$('[data-input="'+type+'"]').attr('data-tmpkey');
    }
    $('[data-tmpkey="'+type+'"]').closest('.form-group').find('input:text').val(data);
    if(data.indexOf('.jpg')!=-1 || data.indexOf('.png')!=-1 || data.indexOf('.gif')!=-1 || data.indexOf('.jpeg')!=-1 || data.indexOf('.svg')!=-1){
      $('#preview-'+type).html('<img src="'+data+'" onload="if(this.width>300)this.width=300" style="max-width:300px;max-height:300px;" />');
    }
  }else{
    alert(data);
  }
}
</script>
ONEZ;
    }
    $filetypes=array();
    $allowed_file_types=$arr['filetypes'];
    if($allowed_file_types){
      foreach($allowed_file_types as $v){
        $filetypes[]='*.'.$v;
      }
    }else{
      $filetypes[]='*.*';
    }
    $input=str_replace('#','',$arr['key']);
    $tmpKey='file'.uniqid().rand(100000000,999999999);
    if($arr['local'] && !$arr['filetype']){
      $arr['filetype']='tmp';
    }
    $F=array(
      'serverurl'=>$this->view('savefile&ftype='.$arr['filetype']),
      'type'=>$tmpKey,
      'filter_desc'=>'支持的格式('.implode(',',$filetypes).')',
      'filter_exts'=>implode(';',$filetypes),
    );
    $swfurl=$this->url.'/res/upload.swf?t='.filemtime(dirname(__FILE__)."/res/upload.swf");
    $swfurl.='&'.http_build_query($F).'&';
    $html.=<<<ONEZ
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="600" height="20" align="middle" data-input="$arr[key]" data-tmpkey="$tmpKey" data-type="$arr[filetype]" data-flash="$arr[flash]">
<param name="allowFullScreen" value="false" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="$swfurl" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<param name="wmode" value="transparent">
<embed src="$swfurl" quality="high" bgcolor="#ffffff" width="600" height="20" name="update" align="middle" allowScriptAccess="always" wmode="transparent" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
ONEZ;
    $myhtml='';
    if($value=$arr['value']){
      $o=explode('.', $value);
      $ext=strtolower($o[count($o)-1]);
      if(in_array($ext,array('gif','jpg','bmp','png','jpeg','svg'))){
        $myhtml='<img src="'.$value.'" onload="if(this.width>300)this.width=300" style="max-width:300px;max-height:300px;" />';
      }
    }
    //$html.='<input type="text" name="'.$arr['name'].'" id="'.$input.'" value="'.$value.'" class="control text">';
    $html.='<div class="preview" id="preview-'.$tmpKey.'">'.$myhtml.'</div>';
    
    $formkz=onez('bootstrap_form_kz')->init($arr);
    
    
    $hint='远程图片地址';
    !$arr['filetype'] && $arr['filetype']='image';
    if($arr['filetype']!='image'){
      $hint='远程文件地址';
    }
    
    $formkz->input->attr('id','input-'.$arr['key'])
                                        ->attr('name',$arr['key'])
                                        ->attr('type','text')
                                        ->attr('class','form-control')
                                        ->attr('placeholder',$hint)
                                        ->attr('style','margin-top:5px;')
                                        ->attr('value',$arr['value'])
                                      ;
                                      
    $input_flash=onez('html')->create('div');
    $input_flash->html=$html;
    $formkz->box->add($input_flash);
    
    $html=$formkz->code();
    $request='?_p='.$this->token.'&_m=savebase64';
    $html.=<<<ONEZ
<script type="text/javascript">
onez.imgReader=function(item,jq){
  var blob = item.getAsFile(),reader = new FileReader();
  reader.onload = function( e ){
    $.post('$request',{data:e.target.result.split(';base64,')[1]},function(data){
      if(typeof data.url=='undefined'){
        alert(data.message);
        return;
      }
      var tmpkey=$('[data-input="$arr[key]"]').attr('data-tmpkey');
      OnezUploadResult(tmpkey,'ok',data.url);
    },'json');
  };
  reader.readAsDataURL( blob );
};
$('#input-$arr[key]').bind('paste',function(e){
  var clipboardData = window.clipboardData; //for IE  
  if (!clipboardData) { // for chrome  
    clipboardData = e.originalEvent.clipboardData;  
  }
  var i = 0,
      items, item, types;
  if( clipboardData ){
      items = clipboardData.items;
      if( !items ){
          return;
      }
      item = items[0];
      // 保存在剪贴板中的数据类型
      types = clipboardData.types || [];
      for( ; i < types.length; i++ ){
          if( types[i] === 'Files' ){
              item = items[i];
              break;
          }
      }
      if( item && item.kind === 'file' && item.type.match(/^image\//i) ){
          // 读取该图片    
          if(window.navigator.userAgent.indexOf("MSIE")>=1){
            window.event.returnValue = false;
          }else{
            e.preventDefault();
          }
          e.returnValue=false;    
          onez.imgReader( item,$(this) );
      }
  }
});
</script>
ONEZ;
    
    return $html;
  }
  function form_save(&$value,$arr){
    if($arr['local']){
      $value=str_replace(ONEZ_CACHE_URL,ONEZ_CACHE_PATH,$value);
      if(strpos($value,'http')===0){
        $value=$this->toLocal($value,1);
      }
      $arr['value']=$value;
    }
  }
  function toLocal($url,$is_file=false){
    $data=onez()->post($url);
    list($filename)=explode('?',$url);
    $o=explode('.',$filename);
    $ext=$o[count($o)-1];
    if(!in_array($ext,array('png','jpg'))){
      $ext='tmp';
    }
    $file='/plugins/form.file/'.date('Y/m/d').'/'.uniqid().'.'.$ext;
    onez()->write(ONEZ_CACHE_PATH.$file,$data);
    if($is_file){
      return ONEZ_CACHE_PATH.$file;
    }
    return ONEZ_CACHE_URL.$file;
  }
  function savefile($token=''){
    // 指定允许其他域名访问  
    header('Access-Control-Allow-Origin:*');  
    // 响应类型  
    header('Access-Control-Allow-Methods:POST');  
    // 响应头设置  
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    
    $tmpfile=$_FILES['file']['tmp_name'];
    if(!$tmpfile || !file_exists($tmpfile)){
      onez()->output(array('message'=>'文件无效','code'=>0,'file'=>$_FILES));
    }
    $name=$_FILES['file']['name'];
    
    
    $type=onez()->gp('ftype');
    !$type && $type='image';
    $type=strtolower($type);
    
    $data=onez()->read($tmpfile);
    
    if($type=='image'){
      $file='/plugins/form.file/'.date('Y/m/d').'/'.uniqid().'.jpg';
      
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      
      $im=imagecreatefromstring($data);
      imagesavealpha($im,true);//
      
      $width=imagesx($im);
      $height=imagesy($im);
      
      $w=$width;
      $h=$height;
      if($w>800){
        $bl=800/$w;
        #$w=800;
        #$h*=$bl;
      }
      
      $image = imagecreatetruecolor($w, $h);
      $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127);  
      imagefill($image, 0, 0, $alpha);  
      imagecopyresampled($image, $im, 0, 0, 0, 0,$w, $h,$width, $height);
      imagesavealpha($image, true);
      imagepng($image,ONEZ_CACHE_PATH.$file); 
      imagedestroy($im);
      
      
      
      @unlink($temp_file);
    }elseif(!in_array(strtolower($type),$this->exts_disabled)){
      $o=explode('.',$name);
      $ext=$o[count($o)-1];
      in_array(strtolower($ext),$this->exts_disabled) && $ext='tmp';
      $file='/plugins/form.file/'.date('Y/m/d').'/'.uniqid().'.'.$ext;
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      @move_uploaded_file($tmpfile,ONEZ_CACHE_PATH.$file);
    }else{
      $file='/plugins/form.file/'.date('Y/m/d').'/'.uniqid().'.tmp';
      onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
      @move_uploaded_file($tmpfile,ONEZ_CACHE_PATH.$file);
    }
    
    $result=array();
    if(file_exists(ONEZ_CACHE_PATH.$file)){
      onez()->output(array(
        'message'=>'ok',
        'url'=>ONEZ_CACHE_URL.$file."|$name",
        'code'=>200,
      ));
    }else{
      onez()->output(array('message'=>'文件无效[02]','code'=>0));
    }
  }
  function savebase64(){
    // 指定允许其他域名访问  
    header('Access-Control-Allow-Origin:*');  
    // 响应类型  
    header('Access-Control-Allow-Methods:POST');  
    // 响应头设置  
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    
    // 指定允许其他域名访问  
    header('Access-Control-Allow-Origin:*');  
    // 响应类型  
    header('Access-Control-Allow-Methods:POST');  
    // 响应头设置  
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    
    $data=$_REQUEST['data'];
    $data=base64_decode($data);
  
    $file='/plugins/mdeditor/'.date('Y/m/d').'/'.uniqid().'.jpg';
    
    onez()->mkdirs(dirname(ONEZ_CACHE_PATH.$file));
    
    $im=imagecreatefromstring($data);
    imagesavealpha($im,true);//
    
    $width=imagesx($im);
    $height=imagesy($im);
    
    $w=$width;
    $h=$height;
    if($w>800){
      $bl=800/$w;
      $w=800;
      $h*=$bl;
    }
    
    $image = imagecreatetruecolor($w, $h);
    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127);  
    imagefill($image, 0, 0, $alpha);  
    imagecopyresampled($image, $im, 0, 0, 0, 0,$w, $h,$width, $height);
    imagesavealpha($image, true);
    imagepng($image,ONEZ_CACHE_PATH.$file); 
    imagedestroy($im);
    
    
    
    @unlink($temp_file);
    $result=array();
    if(file_exists(ONEZ_CACHE_PATH.$file)){
      onez()->output(array(
        'success'=>1,
        'url'=>ONEZ_CACHE_URL.$file,
        'code'=>200,
      ));
    }else{
      onez()->output(array('message'=>'文件无效[02]','code'=>0));
    }
  }
}