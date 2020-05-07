<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('IN_ONEZ', TRUE);
if(!defined('ONEZ_ROOT')){
  define('ONEZ_ROOT', dirname(dirname(__FILE__)));
}
define('ONEZ_VERSION', '1.0');
define('ONEZ_NODE_PATH', '/plugins');
define('ONEZ_MYNODE_PATH', '/myplugins');
define('ONEZ_AUTO_FETCH', 1);
ob_start();
if(version_compare(PHP_VERSION, '7.0.0') == -1) {
  if(function_exists(session_cache_limiter))session_cache_limiter('private, must-revalidate');
}

class onezphp{
  var $vars=array();
  var $parents=array();
  function __call($name,$arguments){
    if($name=='root'){
      return ONEZ_ROOT;
    }
    // 从“父类"中查找方法
    foreach ($this->parents as $p) {
      if(is_string($p)){
        if(!onez()->exists($p)){
          continue;
        }
        $p=onez($p);
      }
      if (is_callable(array($p, $name))) {
        return call_user_func_array(array($p, $name), $arguments);
      }
    }
    // 恢复默认行为
    #return call_user_func_array(array($this, $method), $args);
  }
  function add_parent(){
    foreach(func_get_args() as $arg){
      if(onez()->exists($arg)){
        $this->parents[]=onez($arg);
      }
    }
    return $this;
  }
  /**
  * 加载一个插件
  * 
  * @param string $token 插件标识
  * @param int $id 实例标识，用于重复生成类
  * 
  * @return class
  */
  function load($token,$id=0){
    global $G;
    !$token && onez()->showerror('1001','标识描述错误');
    $token=$this->getToken($token);
    $key="$token-$id";
    if($id!==-1){
      if(isset($G[$key])){
        return $G[$key];
      }
    }else{
      $G['num-'.$token]++;
      $key=$token.'-'.$G['num-'.$token];
    }
    $AUTOFETCH=1;
    $clsName="onezphp_$token";
    $clsName=str_replace('.','_',$clsName);
    
    if(!defined('ONEZ_AUTO_FETCH') && !onez()->exists($token)){
      $clsName='onezphp_onezphp';
      $classFile=__FILE__;
    }else{
      if(!class_exists($clsName)){
        $classFile=onez()->exists($token);
        if($classFile===false){
          if(!onez()->exists('fetch',0)){
            #内核文件
            $post=array(
              'action'=>'files',
              'files'=>'fetch',
              'charset'=>$G['charset'],
              'platform'=>$G['platform'],
              'version'=>$G['version'],
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://www.onezphp.com/api/usersite.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);
            $json=json_decode($content,1);
            if($json['error']){
              onez()->showerror(0,$json['error']);
            }
            foreach($json['files'] as $file=>$data){
              $data=base64_decode($data);
              onez()->mkdirs(dirname(ONEZ_ROOT.'/'.$file));
              file_put_contents(ONEZ_ROOT.'/'.$file,$data);
              if(!file_exists(ONEZ_ROOT.'/'.$file)){
                onez()->showerror(0,'请确认你的安装程序目录有写入权限. 多次安装失败, 请访问论坛获取解决方案！');
              }
            }
          }
          onez('fetch')->get($token);
          $classFile=onez()->exists($token);
        }
        if($classFile===false){
          onez()->showerror('1002','插件“'.$token.'”不存在');
        }
        include_once($classFile);
        if(!class_exists($clsName)){
          onez()->showerror('1003','插件“'.$token.'”类名有误');
        }
      }else{
        $classFile=onez()->exists($token);
      }
    }
    if(!isset($this->token)){
      $this->token='onez';
    }
    $onez=new $clsName($id);
    $onez->id=$id;
    $onez->token=$token;
    $onez->cname=str_replace('.','_',$token);
    $onez->key=$key;
    $onez->up=$this->token;
    $onez->cToken=$this->token.'-'.$token;
    $onez->path=dirname($classFile);
    
    $onez->url=onez()->homepage().substr($onez->path,strlen(onez()->root()));
    foreach($this->plugin_paths as $v){
      if($classFile==$v[0].'/'.$token.'/'.$token.'.php'){
        $onez->url=$v[1].'/'.$token;
        break;
      }
    }
    
    $onez->tags=array();
    $onez->config=array();
    if(!isset($G['nodes'])){
      $G['nodes']=array();
    }
    if(!isset($G['nodes'][$token])){
      $G['nodes'][$token]=0;
    }
    $G['nodes'][$token]++;
    if($onez->path && file_exists($onez->path.'/lib/config.php')){
      $onez->config=include($onez->path.'/lib/config.php');
    }
    $G[$key]=$onez;
    return $onez;
  }
  function classname(){
    return __CLASS__;
  }
  function g($key){
    global $G;
    return $G[$key];
  }
  function view($method){
    $url=onez()->homepage().'/lib/onezphp.php?_view=/'.$this->token.'/'.$method;
    if($this->id!=0){
      $url.='&_viewid='.$this->id;
    }
    return $url;
  }
  function www($method=false){
    global $G;
    if($method===false){
      $_onez=onez()->gp('_onez');
      if($_onez && onez()->exists($_onez)){
        $G['this']=onez($_onez);
        onez()->start($this->path,false);
        onez()->start(onez($_onez)->path);
      }else{
        onez()->start($this->path);
      }
    }else{
      $method=str_replace('?','&',$method);
      $_onez=onez()->gp('_onez');
      if($_onez && onez()->exists($_onez) && strpos($method,'_onez')===false){
        $method.='&_onez='.$_onez;
      }
      return $this->view('www&mod='.$method);
    }
  }
  function autoview($method,$show=0){
    if($show==1){
      $curMethod=onez()->gp('_method');
      parse_str($curMethod?$curMethod:$method,$info);
      $method=key($info);
      unset($info[$method]);
      if(method_exists($this,$method)){
        $this->myargs=array_keys($info);
        foreach($info as $k=>$v){
          $_REQUEST[$k]=$_GET[$k]=$v;
        }
        call_user_func_array(array($this,$method),$info);
      }
      return;
    }
    $get=$_GET;
    if($this->myargs){
      foreach($this->myargs as $k){
        unset($get[$k]);
      }
    }
    $get['_method']=$method;
    return $_SERVER['PHP_SELF'].'?'.http_build_query($get);
  }
  function set($key,$value){
    $this->vars[$key]=$value;
    return $this;
  }
  function get($key,$def=false){
    $value=$this->vars[$key];
    if($def!==false && !isset($this->vars[$key])){
      return $def;
    }
    return $value;
  }
  function getvar($key){
    global $G;
    if(isset($this->vars[$key])){//自有设置
      return $this->vars[$key];
    }
    if(function_exists('_option_get')){
      $option=_option_get($key);
      if($option){
        return $option;
      }
    }
    if($G['this'] && method_exists($G['this'],'option')){//站点设置
      $value=$G['this']->option($key);
    }
    if(!$value){//全站缓存
      $value=onez('cache')->option($key,0);
    }
    if(!$value){//有默认值
      if(method_exists($this,'buildinfo')){
        $info=$this->buildinfo();
        $value=$info[$key];
      }
    }
    if(!$value){//全局变量
      $value=$G[$key];
    }
    return $value;
  }
  function times($times=1){
    if($this->_times>=$times){
      return false;
    }
    $this->_times++;
    return true;
  }
  function href($href,$inplugin=0){
    if(strpos($href,'?')===false){
      $href.='?';
    }else{
      $href.='&';
    }
    if($this->token!='onezphp'){
      $mod=onez()->gp('mod');
      if(strpos($mod,'?')===false){
        $mod.='?';
      }else{
        $mod.='&';
      }
      return onez()->href($mod.'_onez='.$this->token.'|'.urlencode($href),$inplugin);
    }
    return onez()->href($href,$inplugin);
  }
  function data(){
    global $G;
    $key='site.'.$this->token;
    return onez('data',$key);
  }
  function myoption($key=false,$default=false){
    global $G;
    if($key!==false && isset($G['options.'.$key])){
      return $G['options.'.$key];
    }
    if(function_exists('_option_get')){
      $option=_option_get($key);
      if($option){
        return $option;
      }
    }
    $option=$this->get('option');
    if(!$option){
      $this->data()->set('noappid',1);
      $option=$this->data()->open('option')->one("key1='option'");
      $this->data()->set('noappid',0);
      !$option && $option=array();
      $this->set('option',$option);
    }
    if($key===false){
      return $option;
    }
    if(!$option[$key] && $default!==false){
      return $default;
    }
    return $option[$key];
  }
  function myoption_set($arr){
    $this->data()->set('noappid',1);
    $option=$this->myoption();
    if(!$option){
      $this->data()->open('option')->insert(array('key1'=>'option'));
    }
    foreach($arr as $k=>$v){
      $option[$k]=$v;
    }
    $this->set('option',$option);
    $option && $this->data()->open('option')->update($option,"key1='option'");
    $this->data()->set('noappid',0);
  }
  function me(){
    global $G;
    $me_token=$this->get('this');
    if($me_token){
      return onez($me_token);
    }
    $me_token=$this->get('me-token');
    if($me_token){
      return onez($me_token);
    }
    if($G['this']){
      return $G['this'];
    }
    return $this;
  }
  function action($action){
    return $action.'&myaction=event&ptoken='.$this->token;
  }
  function tmpl($action){
    global $A,$record,$G;
    $file=$this->path.'/tmpls/'.$action.'.php';
    if(file_exists($file)){
      include($file);
    }
  }
  function import(){
    list($file,$once)=func_get_args();
    if(file_exists($file)){
      if($once){
        return include_once($file);
      }else{
        return include($file);
      }
    }elseif(file_exists($this->path.$file)){
      if($once){
        return include_once($this->path.$file);
      }else{
        return include($this->path.$file);
      }
    }
    exit("file not exists!$file");
  }
}

class onezphp_onezphp extends onezphp{
  var $plugin_paths=array();
  function addpath($path,$url){
    $this->plugin_paths[]=array($path,$url);
    return $this;
  }
  function mypost($url,$fields='',$options=null){
    !$options && $options=array();
    if($fields){
      $opt = array(
        'http' => array(
          'method' => 'POST',
          'header' => 'content-type:application/x-www-form-urlencoded'.($options['headers']?(';'.implode(';',$options['headers'])):''),
          'content' => is_array($fields)?http_build_query($fields):$fields
        )
      );
      $context = stream_context_create($opt);
      $mydata = file_get_contents($url, false, $context);
    }else{
      if($options['headers']){
        $opt = array(
          'http' => array(
            'method' => 'GET',
            'header' => implode(';',$options['headers']),
          )
        );
        $context = stream_context_create($opt);
        $mydata = file_get_contents($url, false, $context);
      }else{
        $mydata=file_get_contents($url);
      }
    }
    return $mydata;
  }
  /**
  * 读取远程网址代码
  * 
  * @param string $url 请求的网址
  * @param mixed $fields 需要post的参数
  * @param array $options 附加选项
  * 
  * @return mixed 直接返回目标输出的内容
  */
  function post($url,$fields='',$options=null){
    global $G;
    if(!function_exists('curl_init')){
      return onez()->mypost($url,$fields,$options);
    }
    global $G;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if(strpos($url,'https://')!==false){
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    }
    if($options['useragent']){
      curl_setopt($ch, CURLOPT_USERAGENT, $options['useragent']);
    }else{
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.19) Gecko/2010031422 Firefox/3.0.19');
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout'] ? $options['timeout'] : 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $options['headers'] && curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
    if($options['showheader']){
      curl_setopt($ch,CURLOPT_HEADER,1);
    }else{
      curl_setopt($ch,CURLOPT_HEADER,0);
    }
    if($options['cookie']){
      if(file_exists($options['cookie'])){
        curl_setopt($ch, CURLOPT_COOKIEJAR, $options['cookie']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $options['cookie']);
      }else{
        curl_setopt($ch, CURLOPT_COOKIE, $options['cookie']);
      }
    }
    curl_setopt($ch, CURLOPT_REFERER,$options['baseurl'] ? $options['baseurl'] : $url);
    if($fields){
      curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
      curl_setopt($ch, CURLOPT_POST,1);
    }
    $output = curl_exec($ch);
    $G['error_post']=curl_error($ch);
    if($options['showheader']){
      $pos=strpos($output,"\r\n\r\n");
      $this->set('post.header',substr($output,0,$pos));
      $output=substr($output,$pos+4);
    }
    curl_close($ch);
    return $output;
  }
  /**
  * 规范token
  * @param undefined $token
  * 
  * @return
  */
  function getToken($token){
    $token=preg_replace('/[^0-9a-zA-Z_\.]+/i','_',$token);
    return $token;
  }
  /**
  * 判断插件是否存在
  * @param undefined $token
  * 
  * @return
  */
  function exists($token,$canRewrite=1){
    $token=$this->getToken($token);
    
    $PATH=onez()->root().ONEZ_MYNODE_PATH;
    foreach($this->plugin_paths as $v){
      $classFile=$v[0].'/'.$token.'/'.$token.'.php';
      if(file_exists($classFile)){
        return $classFile;
      }
    }
    $classFile=$PATH.'/'.$token.'/'.$token.'.php';
    if(file_exists($classFile)){
      return $classFile;
    }
    
    $PATH=onez()->root().ONEZ_NODE_PATH;
    $classFile=$PATH.'/'.$token.'/'.$token.'.php';
    if($canRewrite && function_exists('_plugin_rewrite')){
      _plugin_rewrite($token,$classFile);
    }
    
    if(!file_exists($classFile)){
      return false;
    }
    return $classFile;
  }
  /**
  * 读取本地文件数据
  * 
  * @param string $filename 文件名
  * @param string $method 默认rb
  * 
  * @return mixed 文件数据
  */
  function read($filename,$method="rb"){
    if(!file_exists($filename)){
      return;
    }
    if($handle=@fopen($filename,$method)){
      flock($handle,LOCK_SH);
      $size=filesize($filename);
      if($size>0){
        $filedata=fread($handle,$size);
      }
      fclose($handle);
    }
    return $filedata;
  }
  /**
  * 写入本地文件
  * 
  * @param string $filename 文件名
  * @param mixed $data 文件内容
  * @param string $method 写入方式,a+为追加
  * @param boolean $iflock
  * 
  * @return
  */
  function write($filename,$data,$method="rb+",$iflock=1){
    $this->mkdirs(dirname($filename));
    touch($filename);
    $handle=fopen($filename,$method);
    if($iflock){
      flock($handle,LOCK_EX);
    }
    fwrite($handle,$data);
    if($method=="rb+") ftruncate($handle,strlen($data));
    fclose($handle);
  }
  /**
  * 强行跳转网址
  * 
  * @param string $url 要跳转的网址
  * 
  * @return
  */
  function location($url){
    if(defined('LOCATION_CHECK')){
      if(strpos($url,'://')!==false){
        if(strpos($url,onez()->homepage())===false){
          exit('<h1>您将要访问：</h1><p>'.$url.'</p><p><a href="'.$url.'">继续访问</a></p>');
        }
      }
    }
    header("location:$url");
    exit();
  }
  /**
   * 创建多级目录
   * 
   * @param string $dir 要创建的完整路径
   * 
   * @return
   */
  function mkdirs($dir){
    if(!is_dir($dir)){
      $this->mkdirs(dirname($dir));
      mkdir($dir,0777);
    }
    return;
  }
  /**
  * 编码转换
  * 
  * @param string $from 当前编码
  * @param string $to 目标编码
  * @param string $string 字符串
  * 
  * @return string
  */
  function iconv($from,$to,$string){
    if(is_array($string)){
      foreach($string as $k=>$v){
        $string[$k]=$this->iconv($from,$to,$v);
      }
      return $string;
    }
    if(function_exists('mb_convert_encoding')){
      return mb_convert_encoding($string,$to,$from);
    }else{
      return iconv($from,$to,$string);
    }
  }
  
  /**
  * 加解密字符串
  * 
  * @param string $string 字符串
  * @param string $action ENCODE加密,DECODE解密
  * @param string $rndKey 密钥
  * 
  * @return mixed
  */
  function strcode($string,$action='ENCODE',$rndKey='onez'){
    global $G;
    $G['rndKey'] && $rndKey=$G['rndKey'];
    $action != 'ENCODE' && $string = base64_decode($string);
    $code = '';
    $key  = substr(md5($rndKey),8,18);
    $keylen = strlen($key); $strlen = strlen($string);
    for ($i=0;$i<$strlen;$i++) {
      $k		= $i % $keylen;
      $code  .= $string[$i] ^ $key[$k];
    }
    return ($action!='DECODE' ? base64_encode($code) : $code);
  }
  /**
  * 读写cookie信息
  * 
  * @param string $var 键
  * @param string $value 值(null时为读取，其他为写入)
  * @param int $life
  * @param boolean $prefix
  * 
  * @return
  */
  function cookie($var, $value=null,$life=0,$prefix=1) {
    global $G,$_COOKIE;
    $time=time();
    if(!isset($G['cookiepre'])){
      $G['cookiepre']='onez_cn_';
    }
    if($value==null){
      if(isset($_COOKIE[$G['cookiepre'].$var])){
        return $_COOKIE[$G['cookiepre'].$var];
      }else{
        return '';
      }
    }elseif($value=='del'||$value=='remove'){
      $value='';
      $life=-20;
    }
    $cookiedomain=$G['cookiedomain'];
    $cookiepath='/';
    setcookie(($prefix ? $G['cookiepre'] : '').$var, $value,
      $life ? $time + $life : 0, $cookiepath,
      $cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
  }
  /**
  * 读取用户get或post的信息
  * 
  * @param string $keys 键
  * @param string $method 方法:G get,P post
  * @param boolean $cvtype 是否为数字
  * 
  * @return string
  */
  function gp($keys,$cvtype=1,$method=null){
    global $G;
    if($method=='G'){
      $value=$_GET[$keys];
    }elseif($method=='P'){
      $value=$_POST[$keys];
    }else{
      $value=$_REQUEST[$keys];
    }
    $G['gp_'.$keys]=$value;
    if (!empty($cvtype) || $cvtype==2) {
      $value = $this->charcv($value,$cvtype==2,true);
    }
    $value=='undefined' && $value='';
    return $value;
  }
  /**
  * 读取变量
  * 
  * @param mixed $mixed 字符串
  * @param boolean $isint 是否为数字
  * @param boolean $istrim 是否去除空格
  * 
  * @return
  */
  function charcv($mixed,$isint=false,$istrim=false) {
    if (is_array($mixed)) {
      foreach ($mixed as $key => $value) {
        $mixed[$key] = $this->charcv($value,$isint,$istrim);
      }
    } elseif ($isint) {
      $mixed = (int)$mixed;
    } elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
      $mixed = str_replace(array("\0","%00","\r"),'',$mixed);
      $mixed = preg_replace(
        array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'),
        array('','&amp;'),
        $mixed
      );
      $mixed = str_replace(array("%3C",'<'),'&lt;',$mixed);
      $mixed = str_replace(array("%3E",'>'),'&gt;',$mixed);
      $mixed = str_replace('&amp;','&',$mixed);
      $mixed = str_replace(array('"',"'","\t",'  '),array('&quot;','&#39;','    ','&nbsp;&nbsp;'),$mixed);
    }
    return $mixed;
  }
  function stripslashes($string, $force = 0) {
    if(is_array($string)) {
      foreach($string as $key => $val) {
        $string[$key] = $this->stripslashes($val, $force);
      }
    } else {
      $string = stripslashes($string);
    }
    return $string;
  }
  /**
  * 截取utf-8格式的部分字符串
  * 
  * @param string $str
  * @param int $start
  * @param int $length
  * @param string $charset
  * @param boolean $suffix
  * 
  * @return string
  */
  function substr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
      if(mb_strlen($str, $charset) <= $length) return $str;
      $slice = mb_substr($str, $start, $length, $charset);
    }else{
      $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
      $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
      $re['gbk&']     = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
      $re['big5']     = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
      preg_match_all($re[$charset], $str, $match);
      if(count($match[0]) <= $length) return $str;
      $slice = join("",array_slice($match[0], $start, $length));
    }
    if($suffix) return $slice."...";
    return $slice;
  }
  /**
  * 获取utf-8字符串的长度
  * 
  * @param string $string
  * 
  * @return string
  */
  function strlen($string = null) {
    preg_match_all("/[0-9]{1}/",$string,$arrNum);  
    preg_match_all("/[a-zA-Z]{1}/",$string,$arrAl);  
    preg_match_all("/./us",$string,$arrCh); 
    return count($arrNum[0]+$arrAl[0]+$arrCh[0]);
  }
  /**
  * 获取当前用户的IP地址
  * 
  * @return
  */
  function ip(){
    global $G;
    if($G['onlineip']){
      return $G['onlineip'];
    }
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
      $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
      $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
      $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
      $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    $onlineip = preg_replace("/^([\d\.]+).*/", "\\1", $onlineip);
    $G['onlineip']=$onlineip;
    return $onlineip;
  }
  /**
  * 自动获取当前程序根目录网址
  * 
  * @return
  */
  function homepage(){
    global $G;
    #分析当前网址
    if(!$G['homepage']){
      if(!$_SERVER['REQUEST_SCHEME']){
        $_SERVER['REQUEST_SCHEME']='http';
      }
      if($_SERVER['HTTPS']=='on'){
        $_SERVER['REQUEST_SCHEME']='https';
      }
      $homepage=$_SERVER['REQUEST_SCHEME'].'://';
      $homepage.=$_SERVER['HTTP_HOST'];
      if(strpos($homepage,':')===false){
        $_SERVER['SERVER_PORT']!='80' && $_SERVER['SERVER_PORT']!='443' && $homepage.=':'.$_SERVER['SERVER_PORT'];
      }
      $key=substr(onez()->root(),strlen($_SERVER['DOCUMENT_ROOT']));
      $key=str_replace('\\','/',$key);
      $homepage.=$key;
      $G['homepage']=$homepage;
    }
    return $G['homepage'];
  }
  /**
  * 自动获取当前网址
  * 
  * @return
  */
  function cururl($add=false,$del=false){
    $o=explode('/',onez()->homepage());
    list($filename,$args)=explode('?',$_SERVER['REQUEST_URI']);
    $get=array();
    parse_str($args,$get);
    if($add){
      foreach($add as $k=>$v){
        $get[$k]=$v;
      }
    }
    if($del){
      foreach($del as $k){
        unset($get[$k]);
      }
    }
    $url=$filename;
    if($get){
      $url.='?'.http_build_query($get);
    }
    return $url;
  }
  function thisurl(){
    $url=$_SERVER['PHP_SELF'];
    if($_GET){
      $url.='?'.http_build_query($_GET);
    }
    return $url;
  }
  function start($root=false,$ismod=true){
    global $G;
    if($G['mykeys']){
      foreach($G['mykeys'] as $key){
        if(!empty($_REQUEST['_my'.$key])){
          $_REQUEST[$key]=$_REQUEST['_my'.$key];
          unset($_REQUEST['_my'.$key]);
        }else{
          $_REQUEST[$key]=$_POST[$key];
        }
      }
    }
    if(isset($_REQUEST['_view'])){
      $view=onez()->gp('_view');
      $id=(int)onez()->gp('_viewid');
      list($token,$method)=explode('/',trim($view,'/'));
      if(method_exists(onez($token),$method)){
        ob_clean();
        call_user_func_array(array(onez($token,$id),$method),array());
        exit();
      }
    }
    $mod=onez()->gp('mod');
    (!$mod || $mod=='/') && $mod='index.php';
    $mod=preg_replace('/[\.\/]+\//i','/',$mod);
    $mod=trim($mod,'/');
    $_REQUEST['mod']=$_GET['mod']=$_POST['mod']=$mod;
    
    if(!$root){
      $root=getcwd();
    }
    
    $myFile=false;
    $www_path=$root.'/www';
    if(defined('ROOT_WWW')){
      $www_path=ROOT_WWW;
    }
    
    
    $modFile=$www_path.'/'.$mod;
    #分级调用init.php
    $path=dirname($modFile);
    $inits=array();
    $step=0;
    while($step<=3){
      $step++;
      if($path=='.' || $path=='' || $path=='/'){
        break;
      }
      $inits[]=$path;
      if(file_exists($path.'/lib/onezphp.php')){
        break;
      }
      if($path==dirname($path)){
        break;
      }
      $path=dirname($path);
    }
    $inits=array_reverse($inits);
    foreach($inits as $v){
      $initFile=$v.'/init.php';
      if(file_exists($initFile)){
        include_once($initFile);
      }
    }
    if(!$ismod || file_exists($modFile)){
      if($ismod){
        $_onez=onez()->gp('_onez');
        if($_onez){
          $_onez=str_replace(array('../','./'),'',$_onez);
          if(strpos($_onez,'.php')===false){
            $_onez='';
          }
          $_REQUEST['_onez']=$_GET['_onez']=$_POST['_onez']=$_onez;
          list($ptoken,$urlfile)=explode('|',$_onez);
          if($ptoken && onez()->exists($ptoken)){
            $urlfile=str_replace('?','&',$urlfile);
            $index=strpos($urlfile,'&');
            $args='';
            if(!$index===false){
              $args=trim(substr($urlfile,$index),'&');
              $urlfile=substr($urlfile,0,$index);
            }
            if($args){
              parse_str($args,$info);
              if($info){
                $_GET=array_merge($_GET,$info);
                $_REQUEST=array_merge($_REQUEST,$info);
              }
            }
            $myFile=onez($ptoken)->path.'/www/'.trim($urlfile,'/');
            if(file_exists($myFile)){
              onez($ptoken)->www_check($mod,$urlfile);
              include($myFile);
              exit();
            }
          }
        }
        include($myFile?$myFile:$modFile);
        exit();
      }
    }else{
      #扩展应用内的文件
      foreach(onez('store')->addons() as $addon){
        if(file_exists($addon->path.'/extra/admin/pages/'.$mod)){
          include($addon->path.'/extra/admin/pages/'.$mod);
          exit();
        }else{
          $mod2=basename($mod);
          if(file_exists($addon->path.'/extra/admin/pages/'.$mod2)){
            include($addon->path.'/extra/admin/pages/'.$mod2);
            exit();
          }
        }
      }
      if($G['this']){
        if(!file_exists($modFile) && is_callable(array($G['this'], 'mod_not_exists'))){
          $G['this']->mod_not_exists($myFile,$modFile,$www_path,$mod);
          if(file_exists($modFile)){
            include($modFile);
            exit();
          }
        }
      }
      echo 'MOD"'.$mod.'"不存在';
    }
    return false;
  }
  function href($href,$inplugin=0){
    global $G;
    parse_str('mod='.str_replace('?','&',$href),$info);
    if($extra){
      $info=array_merge($extra,$info);
    }
    if($G['mykeys']){
      foreach($G['mykeys'] as $key){
        if(!empty($info[$key])){
          $info['_my'.$key]=$info[$key];
          unset($info[$key]);
        }
      }
    }
    if($G['href_extra']){
      $info=array_merge($G['href_extra'],$info);
    }
    if(!$info['_onez']){
      $_onez=onez()->gp('_onez');
      if($inplugin && $_onez && onez()->exists($_onez)){
        $info['_onez']=$_onez;
      }
    }
    if(defined('ONEZ_IS_REWRITE') && $inplugin===0){
      ksort($info);
      $url='';
      if($info['_onez']){
        list($info['_onez'],$_onez)=explode('?',$info['_onez']);
        if($_onez){
          parse_str($_onez,$info2);
          $info2 && $info=array_merge($info,$info2);
        }
      }
      foreach($info as $k=>$v){
        if($k=='mod'){
          $url.='/'.$k;
          $v=substr(trim($v,'/'),0,-4);
          $v=str_replace('/','-',$v);
        }elseif($k=='_onez'){
          $url.='/onez';
          $v=base64_encode($v);
        }else{
          $url.='/'.$k;
          $v=urlencode(str_replace('/','--',$v));
        }
        !$v && $v='-';
        $url.='/'.$v;
      }
      !$url && $url='index';
      $url.='.html';
    }else{
      $url='?'.http_build_query($info);
    }
    return $url;
  }
  function output($A){
    ob_clean();
    echo json_encode($A);
    exit();
  }
  function ok($text,$url){
    ob_clean();
    $A=array(
      'status'=>'success',
      'message'=>$text?$text:'操作成功',
      'goto'=>$url,
    );
    echo json_encode($A);
    exit();
  }
  function error($text){
    ob_clean();
    $A=array(
      'error'=>$text,
    );
    echo json_encode($A);
    exit();
  }
  function showerror($no,$err){
    if($_POST['ajax']){
      onez()->error("[$no]$err");
    }
    if(onez()->exists('showmessage')){
      onez('showmessage')->error("[$no]$err");
    }
    exit("[$no]$err");
  }
  function root(){
    global $G;
    if(!$G['ONEZ_ROOT']){
      $G['ONEZ_ROOT']=$this->get('ONEZ_ROOT');
      if(!$G['ONEZ_ROOT']){
        $G['ONEZ_ROOT']=dirname(dirname(__FILE__));
      }
    }
    return $G['ONEZ_ROOT'];
  }
  function filename(&$name){
    $name=preg_replace('/[^a-zA-Z0-9_\/\.]+/i','',$name);
    return $name;
  }
  function debug(){
    foreach(func_get_args() as $v){
      print_r($v);
      print_r("\n");
    }
    $lines=array();
    foreach(debug_backtrace() as $v){
      $lines[]='#file: '.$v['file']."|#line: ".$v['line']."|#func: ".$v['function'];
    }
    foreach($lines as $line){
      echo "\n\n";
      foreach(explode('|',$line) as $v){
        echo "$v\n";
      }
    }
    #echo "\n".implode("\n",$lines);
    exit();
  }
}
function onez($token='onezphp',$id=0){
  global $G;
  if($token=='onezphp'){
    if(!$G['onezphp']){
      $G['onezphp']=new onezphp_onezphp;
      $G['onezphp']->token='onezphp';
    }
    return $G['onezphp'];
  }
  $fName='_select_'.$token;
  if(function_exists($fName)){
    $r=$fName($token);
    if($r){
      return $r;
    }
  }
  return onez()->load($token,$id);
}

#强制编码
header('Content-Type:text/html;charset=utf-8');
@ini_set('date.timezone','Asia/Shanghai');
#全局配置文件
$conFile=onez()->root().'/config/global.php';
if(file_exists($conFile)){
  include($conFile);
}
!defined('ONEZ_CACHE_PATH') && define('ONEZ_CACHE_PATH',ONEZ_ROOT.'/cache');
!defined('ONEZ_CACHE_URL') && define('ONEZ_CACHE_URL',onez()->homepage().'/cache');
$G['baseurl']=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
if(isset($_REQUEST['_view'])){
  $view=onez()->gp('_view');
  if($view=='onezphp'){
    $url=onez()->gp('_url');
    unset($_REQUEST['_view']);
    unset($_GET['_view']);
    unset($_REQUEST['_url']);
    unset($_GET['_url']);
    
    $o=explode('/',$url);
    for($i=0;$i<count($o);$i+=2){
      if($o[$i]=='mod'){
        $o[$i+1]='/'.str_replace('-','/',$o[$i+1]).'.php';
      }elseif($o[$i]=='onez'){
        $o[$i]='_onez';
        $o[$i+1]=base64_decode($o[$i+1]);
      }else{
        $o[$i+1]=str_replace('--','/',$o[$i+1]);
      }
      $o[$i+1]=='-' && $o[$i+1]='';
      $_REQUEST[$o[$i]]=$o[$i+1];
    }
  }else{
    $view=preg_replace('/[^a-zA-Z0-9\/_\.]+/i','',$view);
    unset($_REQUEST['_view'],$_GET['_view'],$_POST['_view']);
    $id=(int)onez()->gp('_viewid');
    list($token,$method)=explode('/',trim($view,'/'));
    if($token && onez()->exists($token) && method_exists(onez($token),$method)){
      call_user_func_array(array(onez($token,$id),$method),array());
    }
  }
}
if($_FILES){
  foreach($_FILES as $key=>$file){
    if($file['tmp_name']){
      $data=file_get_contents($file['tmp_name']);
      if(strpos($data,'<?php')!==false || (strpos($data,'<?')!==false && (strpos($data,'eval(')!==false))){
        unset($file['tmp_name']);
        exit('Access Denied(FILES)');
      }
    }
  }
}

