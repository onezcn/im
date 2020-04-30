<?php

/* ========================================================================
 * $Id: image.php 6410 2017-11-05 13:27:29Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_image extends onezphp{
  var $tmpfile='';
  var $outfile='';
  function __construct($file){
    
  }
  function init(){
    return onez('image',-1);
  }
  function seturl($pic){
    $this->pic=$pic;
    return $this;
  }
  function load($token, $id = 0){//避免与主类冲突
    $pic=$token;
    $this->close();
    if(strpos($pic,'http')===0){
      $data=onez()->post($pic);
    }elseif(strlen($pic)<256 && is_file($pic) && file_exists($pic)){
      $data=onez()->read($pic);
    }else{
      $data=$pic;
    }
    $tmpkey=md5(uniqid());
    $this->tmpfile=ONEZ_CACHE_PATH.'/image/'.$tmpkey.'.tmp';
    $this->outfile=ONEZ_CACHE_PATH.'/image/'.$tmpkey.'.png';
    onez()->mkdirs(dirname($this->outfile));
    onez()->write($this->tmpfile,$data);
    register_shutdown_function(array(&$this, 'close'));
    
    
    $this->type_in=$this->pictype($this->tmpfile);
    $this->type_out='png';
    $this->src=ImageCreateFromString($data);
    $this->src_w = imagesx($this->src);
    $this->src_h = imagesy($this->src);
    
    return $this;
  }
  function close(){
    if($this->src){
      imagedestroy($this->src);
      $this->src=null;
    }
    if(file_exists($this->tmpfile)){
      @unlink($this->tmpfile);
    }
    if(file_exists($this->outfile)){
      @unlink($this->outfile);
    }
  }
  /**   
   * 取得图片类型   
   *   
   * @param    string     $file_path    文件路径   
   */    
  function pictype($file_path){
    $type_list = array("1"=>"gif","2"=>"jpg","3"=>"png","4"=>"swf","5" => "psd","6"=>"bmp","15"=>"wbmp");
    if(file_exists($file_path)){
      $img_info = @getimagesize($file_path);
      if(isset($type_list[$img_info[2]])){
        Return $type_list[$img_info[2]];
      }
    }else{
      die("文件不存在,不能取得文件类型!");
    } 
  } 
  /**
  * 旋转
  * @param undefined $r
  * 
  * @return
  */
  function rotate($r){
    $this->src = imagerotate($this->src, $r, 0);
    $this->src_w=imagesx($this->src);
    $this->src_h=imagesy($this->src);
    return $this;
  }
  function width(){
    return $this->src_w;
  }
  function height(){
    return $this->src_h;
  }
  function resize($width,$height){
    $image = imagecreatetruecolor($width, $height);
    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127);  
    imagefill($image, 0, 0, $alpha);  
    imagecopyresampled($image, $this->src, 0, 0, 0, 0,$width,$height,$this->src_w, $this->src_h);
    imagesavealpha($image, true);
    //imagepng($image,$this->outfile); 
    imagedestroy($this->src);
    
    $this->src=$image;
    $this->src_w=$width;
    $this->src_h=$height;
    return $this;
  }
  function circle(){
    $image = imagecreatetruecolor($this->src_w, $this->src_h);
    imagealphablending($image,false);
    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127);  
    $r=$this->src_w/2;  
    for($x=0;$x<$this->src_w;$x++){
      for($y=0;$y<$this->src_h;$y++){
        $c = imagecolorat($this->src,$x,$y);
        $_x = $x - $this->src_w/2;
        $_y = $y - $this->src_h/2;
        if((($_x*$_x) + ($_y*$_y)) < ($r*$r)){
          imagesetpixel($image,$x,$y,$c);
        }else{
          imagesetpixel($image,$x,$y,$alpha);
        }
      }
    }
    imagesavealpha($image, true);
    //imagepng($image,$this->outfile); 
    imagedestroy($this->src);
    
    $this->src=$image;
    return $this;
  }
  function data($type='png'){
    if(!file_exists($this->outfile)){
      if($type=='png'){
        imagepng($this->src,$this->outfile);
      }elseif($type=='jpg'){
        imagejpeg($this->src,$this->outfile);
      }else{
        imagepng($this->src,$this->outfile);
      } 
    }
    return onez()->read($this->outfile);
  }
  function icon($size=32){
    if(!$this->pic){
      return'';
    }
    return '<img src="'.$this->pic.'" width="'.$size.'" height="'.$size.'" class="viewpic" onclick="'.onez('event')->load('viewpic')->args().'" /> ';
  }
  function seticon(){
    global $G;
    $html='';
    if($this->times(1)){
      $html.= onez('ui')->css($this->url.'/css/style.css');
      $G['footer'].= onez('ui')->js($this->url.'/js/main.js');
    }
    $html.='<div class="image-seticon" data-size="'.$this->get('size').'" data-value="'.$this->get('value').'" data-name="'.$this->get('name').'" data-server="'.$this->view('upload').'"></div>';
    echo $html;
  }
  function upload(){
    $A=array();
    $tmpfile=$_FILES['Filedata']['tmp_name'];
    if($_FILES['Filedata']['error']){
      onez()->error('上传失败['.$_FILES['Filedata']['error'].']');
    }
    if(!$tmpfile || !file_exists($tmpfile)){
      onez()->error('文件无效');
    }
    
    $data=onez()->read($tmpfile);
    $file='/cache/plugins/image/'.date('Y/m/d').'/'.uniqid().'.png';
    
    onez()->mkdirs(dirname(ONEZ_ROOT.$file));
    
    $im=imagecreatefromstring($data);
    imagepng($im,ONEZ_ROOT.$file);
    imagedestroy($im);
    @unlink($temp_file);
    
    $result=array();
    if(file_exists(ONEZ_ROOT.$file)){
      $A['status']='ok';
      $A['url']=onez()->homepage().$file;
    }else{
      onez()->error('文件无效');
    }
    onez()->output($A);
  }
  function addpic($pic,$x=0,$y=0){ 
    imagecopyresampled($this->src, $pic->src, $x, $y, 0, 0,$pic->width(),$pic->height(),$pic->width(), $pic->height());
    return $this;
  }
  /**
   * 十六进制 转 RGB
   */
  function hex2rgb($hexColor) {
      $color = str_replace('#', '', $hexColor);
      if (strlen($color) > 3) {
          $rgb = array(
              'r' => hexdec(substr($color, 0, 2)),
              'g' => hexdec(substr($color, 2, 2)),
              'b' => hexdec(substr($color, 4, 2))
          );
      } else {
          $color = $hexColor;
          $r = substr($color, 0, 1) . substr($color, 0, 1);
          $g = substr($color, 1, 1) . substr($color, 1, 1);
          $b = substr($color, 2, 1) . substr($color, 2, 1);
          $rgb = array(
              'r' => hexdec($r),
              'g' => hexdec($g),
              'b' => hexdec($b)
          );
      }
      return $rgb;
  }
  function addtext($text,$x=0,$y=0,$color='#000000',$size=12,$file=false){
    if($file==false){
      $file=dirname(__FILE__).'/';
    }
    $rgb=$this->hex2rgb($color);
    $color = imagecolorallocate($this->src,$rgb['r'],$rgb['g'],$rgb['b']);
    imagettftext($this->src,$size,0,$x,$y,$color,$file,$text);
    return $this;
  }
}