<?php
!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_fetch extends onezphp{
  function __construct(){
    
  }
  function find($path,$offset=false){
    if($offset===false){
      $offset=strlen($path)+1;
    }
    $files=array();
    $glob=glob("$path/*");
    if($glob){
      foreach($glob as $v){
        if(is_dir($v)){
          $files=array_merge($files,$this->find($v,$offset));
        }else{
          $files[substr($v,$offset)]=md5_file($v);
        }
      }
    }
    return $files;
  }
  function check(){
    global $G;
    $myfiles=array();
    $glob=glob(ONEZ_ROOT.ONEZ_NODE_PATH.'/*');
    if($glob){
      foreach($glob as $v){
        $token=basename($v);
        $myfiles[$token]=$this->find($v);
      }
    }
    $post=array(
      'plugins'=>base64_encode(serialize($myfiles)),
      'goto'=>onez()->cururl(),
    );
    $mydata=onez()->post('http://www.onezphp.com/api/fetch.php?charset='.$G['charset'],http_build_query($post),array(
      'timeout'=>600,
      'headers'=>array(
        'Authorization: '.$this->auth(),
      )
    ));
    return json_decode($mydata,1);
  }
  function auth(){
    global $G;
    if(!$G['this']){
      return '';
    }
    $appid=$G['this']->option('onez_appid');
    $siteid=$G['this']->option('onez_siteid');
    $sitekey=$G['this']->option('onez_sitekey');
    if(!$appid){
      $appid=onez()->myoption('onez_appid');
      $siteid=onez()->myoption('onez_siteid');
      $sitekey=onez()->myoption('onez_sitekey');
    }
    
    $code=onez()->strcode("onezphp\t$appid\t".time(),'ENCODE',$sitekey);
    return "$siteid\t$code";
  }
  function show($message,$token=false){
    global $G;
    if(!$G['charset']){
      $G['charset']='utf-8';
    }
    ob_clean();
    $info='';
    $progress=100;
    $array=debug_backtrace();
    $array=array_reverse($array);
    $info.='<tr style="display:none" class="onez-plugin-fetch"><td>调用信息</td><td>';
    foreach($array as $line){
      $info.='<p class="info">';
      
      $ptoken='';
      if($line['object']){
        $ptoken=$line['object']->token;
      }
      $info.='<span>'.$ptoken.'</span>';
      $info.='<span>'.$line['function'].'</span>';
      $info.='<span>'.$line['line'].'</span>';
      $info.='<span>'.substr($line['file'],strlen(ONEZ_ROOT)).'</span>';
      
      $info.='</p>';
    }
    $info.='</td></tr>';
    $content=<<<ONEZ
		<div class="progress" title="正在更新">
			<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
				$message
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">系统提示</div>
			<table class="table table-striped">
				<tr>
					<th colspan="2">
            <p>更新时间取决于扩展大小和您的服务器网络情况，请耐心等待。如果长时间无反应，请刷新重试！</p>
            <p>自动下载的扩展存储在云引擎的<code>/plugins/</code>目录中，请勿修改此目录下的任何文件</p>
            <p>如需修改，请将扩展移至<code>/myplugins/</code>目录中，移动后此扩展不再触发更新！</p>
          </th>
				</tr>
				<tr>
					<td width="150">扩展标识</td>
					<td><span class="btn btn-xs btn-success">$token</span></td>
				</tr>
				<tr>
					<td>网站编码</td>
					<td>{$G['charset']}</td>
				</tr>
        $info
			</table>
		</div>
		<div id="tipbody"></div>
ONEZ;
    $adminurl=onez()->href('/index.php');
	  $tpl = <<<ONEZ
<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="{$G['charset']}">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>自动更新 - 佳蓝软件</title>
		<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<style>
			html,body{font-size:13px;font-family:"Microsoft YaHei UI", "微软雅黑", "宋体";}
			.pager li.previous a{margin-right:10px;}
			.header a{color:#FFF;}
			.header a:hover{color:#428bca;}
			.footer{padding:10px;}
			.footer a,.footer{color:#eee;font-size:14px;line-height:25px;}
      p.info{
        line-height:20px;
      }
      p.info span{
        display:inline-block;
        min-width:100px;
      }
		</style>
		<!--[if lt IE 9]>
		  <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body style="background-color:#28b0e4;">
		<div class="container">
			<div class="header" style="margin:15px auto;">
				<ul class="nav nav-pills pull-right" role="tablist">
					<li role="presentation" class="active"><a href="javascript:;">更新系统</a></li>
					<li role="presentation"><a href="http://www.onez.cn" target="_blank">官方网站</a></li>
					<li role="presentation"><a href="http://www.onez.cn/onez/YmJzfC9pbmRleC5waHA=/mod/bbs.html" target="_blank">论坛交流</a></li>
				</ul>
				<img src="//cdn.onez.cn/files/2017/0416/2017041603031745300002.png" height="60" />
			</div>
			<div class="row well" style="margin:auto 0;">
				<div class="col-xs-12">
					{$content}
				</div>
        
			</div>
			<div class="footer" style="margin:15px auto;">
				<div class="text-center">
					版权所有 <a href="http://www.onez.cn" target="_blank"><b>佳蓝科技</b></a> v3.0 &copy; 2020 <a href="http://www.onez.cn" target="_blank">www.onez.cn</a>
				</div>
			</div>
		</div>
		<script src="//cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(function(){
  $.post(window.location.href,{__action:'fetching'},function(data){
    $('.progress-bar').html('更新完毕');
    try{
      eval('var o='+data+';');
      if(typeof o.error!='undefined'){
        $('#tipbody').html('<div class="alert alert-danger">'+o.error+'</div><a class="btn btn-info" href="$adminurl">进入管理后台</a>');
        return;
      }
    }catch(e){
      location.reload();
    }
  });
});
</script>
	</body>
</html>
ONEZ;
	  echo trim($tpl);
    exit();
  }
  function get($token,$focus=0){
    global $G;
    $classFile=ONEZ_ROOT.ONEZ_NODE_PATH.'/'.$token.'/'.$token.'.php';
    $showmessage=0;
    $__action=onez()->gp('__action');
    if(!defined('ONEZPHP_FETCH_NOTIP')){
      if($__action!='fetching' && empty($_POST) && empty($_GET['_m']) && empty($_GET['_method']) && $_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest'){
        $showmessage=1;
        $this->show('正在从云端获取扩展...',$token);
      }
    }
    if($focus || !file_exists($classFile)){
      $post=array(
        'token'=>$token,
        'goto'=>onez()->cururl(),
      );
      set_time_limit(0);
      @ini_set('memory_limit','1024M');
      
      $auth='';
      if(!in_array($token,array('data','cache','db','dz.dzdb','dz.mydb'))){
        $auth=$this->auth();
      }
      $mydata=onez()->post('http://www.onezphp.com/api/fetch.php?fver=2.0&charset='.$G['charset'],http_build_query($post),array(
        'timeout'=>600,
        'headers'=>array(
          'Authorization: '.$auth,
        )
      ));
      $json=@json_decode($mydata,1);
      if(is_array($json)){
        if($json['location']){
          $mydata=onez()->post($json['location'],'',array(
            'timeout'=>600,
          ));
          if(!$mydata && $G['error_post']){
            onez()->error($G['error_post']);
          }
        }elseif($json['error']){
          onez()->error($json['error']);
        }
      }
      if(strpos($mydata,'onez')===0){
        $mydata=substr($mydata,4);
        $mydata=gzuncompress($mydata);
        $pos=0;
        $nFileCount = substr($mydata, $pos, 16) ;
        $pos += 16 ;

        $size = substr($mydata, $pos, 16) ;
        $pos += 16 ;

        $info = substr($mydata, $pos, $size-1) ;
        $pos += $size ;

        $info_array = explode("\n", $info) ;

        $c_file = 0 ;
        $c_dir = 0 ;
        
        $files=array();
        $isok=0;
        foreach($info_array as $str_row){
          list($filename, $attr) = explode("|", $str_row);
          if ( substr($attr,0,6)=="[/dir]"){
            continue;
          }
          if(substr($attr,0,5)=="[dir]"){
            //$files[]=array('dir',$filename);
          }else{
            $files[$filename]=substr($mydata, $pos, $attr);
            $pos += $attr ;
          }
        }
        $mainFile=$token.'.php';
        !$files[$mainFile] && onez()->error($token.'插件源有误');
        foreach($files as $filename=>$data){
          $file=ONEZ_ROOT.ONEZ_NODE_PATH.'/'.$token.'/'.$filename;
          if(strpos($filename,'.php')!==false){
            $data=str_replace('__DIR__','dirname(__FILE__)',$data);
          }
          onez()->write($file,$data);
        }
        if(!defined('ONEZPHP_FETCH_NOTIP') && $__action=='fetching'){
          onez()->outout(array('tip'=>'更新成功','status'=>'ok'));
        }
      }else{
        !$mydata && onez()->error('读取插件代码有误');
        onez()->error($mydata);
      }
    }
  }
}