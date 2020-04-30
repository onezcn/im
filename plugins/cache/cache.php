<?php
!defined('IN_ONEZ') && exit('Access Denied');
/**
* 本地与服务器缓存
*/
class onezphp_cache extends onezphp{
  function __construct(){
    
  }
  /**
  * 读写当前访客的cookie
  * @param mixed $key
  * @param mixed $value
  * 
  * @return
  */
  function cookie($key,$value=false,$once=0){
    if($value===false){
      $key=onez()->getToken($key);
      $cookie=onez()->cookie($key);
      if(empty($cookie))return'';
      $value=onez()->strcode($cookie,'DECODE');
      $value=unserialize($value);
      return $value;
    }else{
      $value!='del' && $value!='remove' && $value=onez()->strcode(serialize($value),'ENCODE');
      onez()->cookie($key,$value,$once?0:86400*365*10);
    }
  }
  /**
  * 读取+写入缓存共用函数
  * @param mixed $key
  * @param mixed $value
  * 
  * @return
  */
  function info($key,$value=false){
    if($value===false){
      return $this->get($key);
    }else{
      $this->set($key,$value);
      return $this;
    }
  }
  /**
  * 写入缓存
  * @param mixed $key
  * @param mixed $value
  * 
  * @return
  */
  function set($key,$value){
    if(defined('ONEZ_CACHE_REDIS')){
      onez('redis')->set($key,$value);
      return $this;
    }
    onez()->write($this->file($key),"<?php\n!defined('IN_ONEZ') && exit('Access Denied');\n?>".serialize($value));
    if(!file_exists($this->file($key))){
      exit('没有读写权限: '.ONEZ_CACHE_PATH.'/appcaches');
    }
    return $this;
  }
  /**
  * 读取缓存
  * @param mixed $key
  * 
  * @return
  */
  function get($key,$def=false){
    if(defined('ONEZ_CACHE_REDIS')){
      return onez('redis')->get($key,$def);
    }
    $value=onez()->read($this->file($key));
    if($value){
      $value=substr($value,strpos($value,'?>')+2);
      return unserialize($value);
    }else{
      return array();
    }
  }
  /**
  * 键值对应的缓存文件地址
  * @param mixed $key
  * 
  * @return
  */
  function file($key){
    return ONEZ_CACHE_PATH.'/appcaches/'.$key.'.php';
  }
  /**
  * 按键值查找指定缓存目录下的文件
  * @param mixed $s
  * 
  * @return
  */
  function find($s){
    $glob=glob(ONEZ_CACHE_PATH.'/appcaches/'.$s);
    !$glob && $glob=array();
    return $glob;
  }
  function remove($key){
    if(file_exists($this->file($key))){
      @unlink($this->file($key));
    }
  }
  /**
  * 读取网站全局变量
  * @param mixed $key
  * 
  * @return
  */
  function option($key,$must=1){
    global $G;
    if(!$G['options']['is_init']){
      $G['options']=$this->get('options');
      $G['options']['is_init']=1;
    }
    $value=$G['options'][$key];
    if($must && !$value){
      if(onez()->exists('showmessage')){
        onez('showmessage')->error('请正确设置网站参数['.$key.'] By Super!');
      }else{
        exit('请正确设置网站参数['.$key.']');
      }
    }
    return $value;
  }
  /**
  * 追加网站全局变量
  * @param mixed $key
  * 
  * @return
  */
  function option_set($arr){
    global $G;
    if(!$arr || !is_array($arr)){
      return $this;
    }
    $G['options']=$this->get('options');
    if($G['options'] && !is_array($G['options'])){
      exit('options有误');
    }
    foreach($arr as $k=>$v){
      $G['options'][$k]=$v;
    }
    $this->set('options',$G['options']);
    return $this;
  }
}