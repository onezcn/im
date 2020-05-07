<?php
error_reporting(E_ALL ^ E_NOTICE);
@set_time_limit(0);

ob_start();
define('ONEZ_ROOT', str_replace("\\",'/', dirname(__FILE__)));
define('APP_STORE_URL', 'http://www.onez.cn');
define('APP_STORE_API', 'http://www.onez.cn/api/usersite.v3.php');
define('ONEZ_PRODUCT_SUBJECT', '佳蓝即时通讯系统开源版');
define('ONEZ_PRODUCT_SUMMARY', '');
define('ONEZ_PRODUCT_SHORTNAME', '佳蓝IM');
define('ONEZ_PRODUCT_PTOKEN', 'wxpc');
define('ONEZ_VERSION', 'v3.0');
define('ONEZ_PKEY', '');
define('IS_ONEZ_DEBUG', '');
if($_GET['res']) {
	$res = $_GET['res'];
	$reses = tpl_resources();
	if(array_key_exists($res, $reses)) {
		if($res == 'css') {
			header('content-type:text/css');
		} else {
			header('content-type:image/png');
		}
		echo base64_decode($reses[$res]);
		exit();
	}
  
}

$actions = array('license', 'env', 'onez', 'db', 'finish');
$action = $_COOKIE['action'];
$action = in_array($action, $actions) ? $action : 'license';
$ispost = strtolower($_SERVER['REQUEST_METHOD']) == 'post';
if(file_exists(ONEZ_ROOT . '/cache/install.lock') && $action != 'finish') {
	header('location: ./index.php');
	exit;
}
header('content-type: text/html; charset=utf-8');
if($action == 'license') {
	if($ispost) {
		setcookie('action', 'env');
		header('location: ?refresh');
		exit;
	}
	tpl_install_license();
}
if($action == 'env') {
	if($ispost) {
		setcookie('action', $_POST['do'] == 'continue' ? 'db' : 'license');
		header('location: ?refresh');
		exit;
	}
	$ret = array();
	$ret['server']['os']['value'] = php_uname();
	if(PHP_SHLIB_SUFFIX == 'dll') {
		$ret['server']['os']['remark'] = '建议使用 Linux 系统以提升程序性能';
		$ret['server']['os']['class'] = 'warning';
	}
	$ret['server']['sapi']['value'] = $_SERVER['SERVER_SOFTWARE'];
	if(PHP_SAPI == 'isapi') {
		$ret['server']['sapi']['remark'] = '建议使用 Apache 或 Nginx 以提升程序性能';
		$ret['server']['sapi']['class'] = 'warning';
	}
	$ret['server']['php']['value'] = PHP_VERSION;
	$ret['server']['dir']['value'] = ONEZ_ROOT;
	if(function_exists('disk_free_space')) {
		$ret['server']['disk']['value'] = floor(disk_free_space(ONEZ_ROOT) / (1024*1024)).'M';
	} else {
		$ret['server']['disk']['value'] = 'unknow';
	}
	$ret['server']['upload']['value'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';

	$ret['php']['version']['value'] = PHP_VERSION;
	$ret['php']['version']['class'] = 'success';
	if(version_compare(PHP_VERSION, '5.4.0') == -1) {
		$ret['php']['version']['class'] = 'danger';
		$ret['php']['version']['failed'] = true;
		$ret['php']['version']['remark'] = 'PHP版本必须为 5.4.0 以上. ';
	}

	$ret['php']['fopen']['ok'] = @ini_get('allow_url_fopen') && function_exists('fsockopen');
	if($ret['php']['fopen']['ok']) {
		$ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
	} else {
		$ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
	}

	$ret['php']['curl']['ok'] = extension_loaded('curl') && function_exists('curl_init');
	if($ret['php']['curl']['ok']) {
		$ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['curl']['class'] = 'success';
	} else {
		$ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['curl']['class'] = 'danger';
		$ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 也不支持 allow_url_fopen, 系统无法正常运行.';
		$ret['php']['curl']['failed'] = true;
	}

	$ret['php']['ssl']['ok'] = extension_loaded('openssl');
	$ret['php']['ssl']['ok'] = 1;
	if($ret['php']['ssl']['ok']) {
		$ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['ssl']['class'] = 'success';
	} else {
		$ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['ssl']['class'] = 'danger';
		$ret['php']['ssl']['failed'] = true;
		$ret['php']['ssl']['remark'] = '没有启用OpenSSL, 将无法访问云端接口, 系统无法正常运行.';
	}

	$ret['php']['gd']['ok'] = extension_loaded('gd');
	if($ret['php']['gd']['ok']) {
		$ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['gd']['class'] = 'success';
	} else {
		$ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['gd']['class'] = 'danger';
		$ret['php']['gd']['failed'] = true;
		$ret['php']['gd']['remark'] = '没有启用GD, 将无法正常上传和压缩图片, 系统无法正常运行. ';
	}

	$ret['php']['dom']['ok'] = class_exists('DOMDocument');
	if($ret['php']['dom']['ok']) {
		$ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['dom']['class'] = 'success';
	} else {
		$ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['dom']['class'] = 'danger';
		$ret['php']['dom']['failed'] = true;
		$ret['php']['dom']['remark'] = '没有启用DOMDocument, 将无法正常安装使用模块, 系统无法正常运行. ';
	}


	$ret['php']['asp_tags']['ok'] = ini_get('asp_tags');
	if(empty($ret['php']['asp_tags']['ok']) || strtolower($ret['php']['asp_tags']['ok']) == 'off') {
		$ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['asp_tags']['class'] = 'success';
	} else {
		$ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['asp_tags']['class'] = 'danger';
		$ret['php']['asp_tags']['failed'] = true;
		$ret['php']['asp_tags']['remark'] = '请禁用可以使用ASP 风格的标志，配置php.ini中asp_tags = Off';
	}

	$ret['write']['root']['ok'] = local_writeable(ONEZ_ROOT . '/');
	if($ret['write']['root']['ok']) {
		$ret['write']['root']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['write']['root']['class'] = 'success';
	} else {
		$ret['write']['root']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['write']['root']['class'] = 'danger';
		$ret['write']['root']['failed'] = true;
		$ret['write']['root']['remark'] = '本地目录无法写入, 将无法使用自动更新功能, 系统无法正常运行.  ';
	}
	$ret['write']['cache']['ok'] = local_writeable(ONEZ_ROOT . '/cache');
	if($ret['write']['cache']['ok']) {
		$ret['write']['cache']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['write']['cache']['class'] = 'success';
	} else {
		$ret['write']['cache']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['write']['cache']['class'] = 'danger';
		$ret['write']['cache']['failed'] = true;
		$ret['write']['cache']['remark'] = 'cache目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
	}
	$ret['write']['plugins']['ok'] = local_writeable(ONEZ_ROOT . '/plugins');
	if($ret['write']['plugins']['ok']) {
		$ret['write']['plugins']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['write']['plugins']['class'] = 'success';
	} else {
		$ret['write']['plugins']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['write']['plugins']['class'] = 'danger';
		$ret['write']['plugins']['failed'] = true;
		$ret['write']['plugins']['remark'] = 'plugins目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
	}

	$ret['continue'] = true;
	foreach($ret['php'] as $opt) {
		if($opt['failed']) {
			$ret['continue'] = false;
			break;
		}
	}
	if($ret['write']['failed']) {
		$ret['continue'] = false;
	}
	tpl_install_env($ret);
}
if($action == 'onez') {
	setcookie('action', 'db');
	header('location: ?refresh');
  return;
  if(defined('ONEZ_PKEY') && ONEZ_PKEY!=''){
		setcookie('action', 'db');
		header('location: ?refresh');
		exit();
  }
	$userkey = $_GET['userkey'];
  if($userkey){
    setcookie('userkey', $userkey);
		setcookie('action', 'onez');
		header('location: ?refresh');
		exit();
  }
	$userkey = $_COOKIE['userkey'];
	if(!$ispost) {
    if($userkey){
      $ispost=1;
      $_POST['do']='continue';
      $_POST['onez']=array(
        'type'=>'check',
        'userkey'=>$userkey,
      );
    }
  }
	if($ispost) {
		if($_POST['do'] != 'continue') {
			setcookie('action', 'env');
			header('location: ?refresh');
			exit();
		}
    
    $onez=$_POST['onez'];
    $post=array(
      'action'=>'product',
      'data'=>base64_encode(json_encode($onez)),
    );
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, APP_STORE_API);
  	curl_setopt($ch, CURLOPT_POST, 1);
  	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	$content = curl_exec($ch);
  	curl_close($ch);
    $json=json_decode($content,1);
    if($json['error']){
      tpl_install_onez($json['error']);
    }
    if($json['extra']){
      mkdirs(dirname(ONEZ_ROOT.'/cache'));
      file_put_contents(ONEZ_ROOT.'/cache/install.extra',$json['extra']);
      if(!file_exists(ONEZ_ROOT.'/cache/install.extra')){
        tpl_install_db('请确认你的安装程序目录有写入权限. 多次安装失败, 请访问论坛获取解决方案！');
      }
    }
    if(isset($json['userkey'])){
      setcookie('userkey', $json['userkey']);
    }
    if($json['action']){
			setcookie('action', $json['action']);
			header('location: ?refresh');
			exit();
    }
    if($json['content']){
      tpl_install_onez('',base64_decode($json['content']));
    }
    
    
		setcookie('action', 'db');
		header('location: ?refresh');
		exit();
	}
  tpl_install_onez('');
}
if($action == 'db') {
  $G['hasdata']=0;
	if($ispost) {
		if($_POST['do'] != 'continue') {
			setcookie('action', 'onez');
			header('location: ?refresh');
			exit();
		}
    if(!file_exists(dirname(__FILE__).'/lib/onezphp.php')){
      $post=array(
        'action'=>'download',
      );
    }else{
      $post=array(
        'action'=>'download2',
      );
    }
    $post['userkey']=$_COOKIE['userkey'];
    if(file_exists(ONEZ_ROOT.'/cache/install.extra')){
      $post['extra']=file_get_contents(ONEZ_ROOT.'/cache/install.extra');
    }
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, APP_STORE_API);
  	curl_setopt($ch, CURLOPT_POST, 1);
  	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	$content = curl_exec($ch);
  	curl_close($ch);
    $json=json_decode($content,1);
    if($json['error']){
      tpl_install_db($json['error']);
    }
    if(!file_exists(dirname(__FILE__).'/lib/onezphp.php')){
      if(!$json['files']['lib/onezphp.php']){
        tpl_install_db('获取安装信息失败，可能是由于网络不稳定，请重试。');
      }
    }
    foreach($json['files'] as $file=>$data){
      $data=base64_decode($data);
      mkdirs(dirname(ONEZ_ROOT.'/'.$file));
      file_put_contents(ONEZ_ROOT.'/'.$file,$data);
      if(!file_exists(ONEZ_ROOT.'/'.$file)){
        tpl_install_db('请确认你的安装程序目录有写入权限. 多次安装失败, 请访问论坛获取解决方案！');
      }
    }
      
      
    include_once(dirname(__FILE__).'/lib/onezphp.php');
    $db=$_POST['db'];
    $user=$_POST['user'];
    if(version_compare(PHP_VERSION, '7.0.0') == -1) {
      $link=@mysql_connect($db['server'], $db['username'], $db['password']);
      if(!$link){
        tpl_install_db('无法连接数据库，请检查数据库账号和密码是否正确');
      }
    	if(!@mysql_select_db($db['name'], $link)){
        @mysql_query('CREATE DATABASE IF NOT EXISTS `'.$db['name'].'` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;', $link);
        $error=mysql_error();
        if(!@mysql_select_db($db['name'], $link)){
          tpl_install_db('数据库<code>'.$db['name'].'</code>不存在且无法创建，请检查['.$error.']');
        }
      }
  		$statement = @mysql_query("SHOW TABLES LIKE '{$db['prefix']}%';",$link);
  		if ($statement) {
        $rs=mysql_fetch_array($statement);
        if($rs){
          if($_POST['clear']){
            @mysql_query("DROP TABLE IF EXISTS {$db['prefix']}data;",$link);
            @mysql_query("DROP TABLE IF EXISTS {$db['prefix']}device;",$link);
            @mysql_query("DROP TABLE IF EXISTS {$db['prefix']}member;",$link);
            @mysql_query("DROP TABLE IF EXISTS {$db['prefix']}order;",$link);
          }else{
            $G['hasdata']=1;
            tpl_install_db('');
          }
        }
  		}
    }else{
      $link=mysqli_connect($db['server'], $db['username'], $db['password']);
      if(!$link){
        tpl_install_db('无法连接数据库，请检查数据库账号和密码是否正确');
      }
    	if(!@mysqli_select_db($link,$db['name'])){
        mysqli_query($link,'CREATE DATABASE IF NOT EXISTS `'.$db['name'].'` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;');
        $error=mysql_error();
        if(!mysqli_select_db($link,$db['name'])){
          tpl_install_db('数据库<code>'.$db['name'].'</code>不存在且无法创建，请检查['.$error.']');
        }
      }
  		$statement = mysqli_query($link,"SHOW TABLES LIKE '{$db['prefix']}%';");
  		if ($statement) {
        $rs=mysqli_fetch_array($statement);
        if($rs){
          if($_POST['clear']){
            mysqli_query($link,"DROP TABLE IF EXISTS {$db['prefix']}data;");
            mysqli_query($link,"DROP TABLE IF EXISTS {$db['prefix']}device;");
            mysqli_query($link,"DROP TABLE IF EXISTS {$db['prefix']}member;");
            mysqli_query($link,"DROP TABLE IF EXISTS {$db['prefix']}order;");
          }else{
            $G['hasdata']=1;
            tpl_install_db('');
          }
        }
  		}
    }
    $code=<<<ONEZ
<?php
!defined('IN_ONEZ') && exit('Access Denied');
return array(
  'dbhost'=>'$db[server]',
  'dbuser'=>'$db[username]',
  'dbpass'=>'$db[password]',
  'dbname'=>'$db[name]',
  'dbcharset'=>'utf8',
  'tablepre'=>'$db[prefix]',
  'pconnect'=>'1',
);
ONEZ;
    onez()->write(ONEZ_ROOT.'/config/db.default.php',$code);
    #安装数据库
    $sysFile=ONEZ_ROOT.'/cache/dbtables.php';
    if(file_exists($sysFile)){
      $dbtables=include($sysFile);
      if($dbtables){
        foreach($dbtables as $tablename=>$table){
          $sql=onez('db')->create_mysql($tablename,$table['idname'],$table['fields']);
          onez('db')->db()->query($sql);
        }
      }
    }
    #默认数据
    $sysFile=ONEZ_ROOT.'/cache/datas.default.php';
    if(file_exists($sysFile)){
      $value=onez()->read($sysFile);
      if($value){
        $value=substr($value,strpos($value,'?>')+2);
        $value=base64_decode($value);
        $value=unserialize($value);
        if($value){
          foreach($value as $table=>$datas){
            if(is_numeric($table)){
              $datas['namespace']='site.'.ONEZ_PRODUCT_PTOKEN;
              $datas['tablename']=$datas['tablename'];
              $datas['appid']='0';
              $datas['siteid']='0';
              onez('db')->open('data')->insert($datas);
            }else{
              foreach($datas as $rs){
                if(!empty($rs)){
                  $rs['namespace']='site.'.ONEZ_PRODUCT_PTOKEN;
                  $rs['appid']='0';
                  $rs['siteid']='0';
                  onez('db')->open($table)->insert($rs);
                }
              }
            }
          }
        }
      }
    }
    $G['this']=onez(ONEZ_PRODUCT_PTOKEN);
    #写入管理账号
    $rndkey=uniqid();
    $userid=onez('db')->open('member')->insert(array(
      'nickname'=>'超级管理员',
      'username'=>$user['username'],
      'grade'=>'admin',
      'rndkey'=>$rndkey,
      'password'=>hash_hmac('md5',$user['password'],$rndkey),
      'infotime'=>time(),
      'infoip'=>onez()->ip(),
    ));
    onez('cache')->cookie(str_replace('.','_',ONEZ_PRODUCT_PTOKEN),"$userid\t$user[username]\t".uniqid(),0);
    
    $pkey='';
    $json=array();
    if(file_exists(ONEZ_ROOT.'/cache/install.extra')){
      $extra=file_get_contents(ONEZ_ROOT.'/cache/install.extra');
      $json=json_decode(base64_decode($extra),1);
      if(!empty($json['pkey'])){
        $pkey=$json['pkey'];
      }
    }
    
    if(file_exists(ONEZ_ROOT.'/cache/install.extra')){
      @unlink(ONEZ_ROOT.'/cache/install.extra');
    }
		touch(ONEZ_ROOT . '/cache/install.lock');
		setcookie('action', 'finish');
		setcookie('userkey', '');
		header('location: ?refresh');
		exit();
	}
  tpl_install_db('');
}
if($action == 'finish') {
	setcookie('action', '', -10);
	tpl_install_finish();
}

function mkdirs($dir){
  if(!is_dir($dir)){
    mkdirs(dirname($dir));
    mkdir($dir,0777);
  }
  return ;
}
function local_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = fopen("$dir/test.txt", 'w')) {
			fclose($fp);
			unlink("$dir/test.txt");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}



function tpl_frame() {
	global $action, $actions;
	$action = $_COOKIE['action'];
	$step = array_search($action, $actions);
	$steps = array();
	for($i = 0; $i <= $step; $i++) {
		if($i == $step) {
			$steps[$i] = ' list-group-item-info';
		} else {
			$steps[$i] = ' list-group-item-success';
		}
	}
	$progress = $step * 20 + 20;
	$content = ob_get_contents();
	ob_clean();
  $title='安装系统 - '.ONEZ_PRODUCT_SUBJECT.' - '.ONEZ_PRODUCT_SUMMARY;
	$tpl = <<<EOF
<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>$title</title>
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<style>
			html,body{font-size:13px;font-family:"Microsoft YaHei UI", "微软雅黑", "宋体";}
			.pager li.previous a{margin-right:10px;}
			.header a{color:#FFF;}
			.header a:hover{color:#428bca;}
			.footer{padding:10px;}
			.footer a,.footer{color:#eee;font-size:14px;line-height:25px;}
		</style>
		<!--[if lt IE 9]>
		  <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
		<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</head>
	<body style="background-color:#28b0e4;">
		<div class="container">
			<div class="header" style="margin:15px auto;">
				<ul class="nav nav-pills pull-right" role="tablist">
					<li role="presentation" class="active"><a href="javascript:;">安装系统</a></li>
					<li role="presentation"><a href="http://www.onez.cn" target="_blank">官方网站</a></li>
					<li role="presentation"><a href="http://www.onez.cn/onez/YmJzfC9pbmRleC5waHA=/mod/bbs.html" target="_blank">论坛交流</a></li>
				</ul>
				<img src="?res=logo" height="60" />
			</div>
			<div class="row well" style="margin:auto 0;">
				<div class="col-xs-3">
					<div class="progress" title="安装进度">
						<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
							{$progress}%
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							安装步骤
						</div>
						<ul class="list-group">
							<a href="javascript:;" class="list-group-item{$steps[0]}"><span class="glyphicon glyphicon-copyright-mark"></span> &nbsp; 许可协议</a>
							<a href="javascript:;" class="list-group-item{$steps[1]}"><span class="glyphicon glyphicon-eye-open"></span> &nbsp; 环境监测</a>
							<a href="javascript:;" class="list-group-item{$steps[2]}"><span class="glyphicon glyphicon-globe"></span> &nbsp; 选择产品</a>
							<a href="javascript:;" class="list-group-item{$steps[3]}"><span class="glyphicon glyphicon-cog"></span> &nbsp; 参数配置</a>
							<a href="javascript:;" class="list-group-item{$steps[4]}"><span class="glyphicon glyphicon-ok"></span> &nbsp; 成功</a>
						</ul>
					</div>
				</div>
				<div class="col-xs-9">
					{$content}
				</div>
			</div>
			<div class="footer" style="margin:15px auto;">
				<div class="text-center">
					版权所有 <a href="http://www.onez.cn" target="_blank"><b>佳蓝科技</b></a> v3.0 &copy; 2020 <a href="http://www.onez.cn" target="_blank">www.onez.cn</a>
				</div>
			</div>
		</div>
	</body>
</html>
EOF;
	echo trim($tpl);
}

function tpl_install_license() {
  $ONEZ_PRODUCT_SUBJECT=ONEZ_PRODUCT_SUBJECT;
  $ONEZ_PRODUCT_SHORTNAME=ONEZ_PRODUCT_SHORTNAME;
	echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">阅读许可协议</div>
			<div class="panel-body" style="overflow-y:scroll;max-height:400px;line-height:20px;">
				<h3>版权所有 (c)2017，佳蓝科技保留所有权利。 </h3>
				<p>
					感谢您选择{$ONEZ_PRODUCT_SHORTNAME} - {$ONEZ_PRODUCT_SUBJECT}（以下简称{$ONEZ_PRODUCT_SHORTNAME}，{$ONEZ_PRODUCT_SHORTNAME}基于 PHP + MySQL的技术开发，全部源码开放。 <br />
					为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：
				</p>
				<p>
					<strong>一、本授权协议适用且仅适用于{$ONEZ_PRODUCT_SHORTNAME}任何版本，佳蓝科技官方对本授权协议的最终解释权。</strong>
				</p>
				<p>
					<strong>二、协议许可的权利 </strong>
					<ol>
						<li>您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用。</li>
						<li>您可以在协议规定的约束和限制范围内修改{$ONEZ_PRODUCT_SHORTNAME}源代码或界面风格以适应您的网站要求。</li>
						<li>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</li>
						<li>获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。</li>
					</ol>
				</p>
				<p>
					<strong>三、协议规定的约束和限制 </strong>
					<ol>
						<li>未获商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目的或实现盈利的网站）。</li>
						<li>未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</li>
						<li>未经官方许可，禁止在{$ONEZ_PRODUCT_SHORTNAME}的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
						<li>您承诺，应遵守所有中华人民共和国法律法规和国际上有关计算机、互联网和电子邮件的协议、规定、程序和惯例，其使用的所有功能应用于合法用途。如您将此功能用于侵犯国家、社会、他人的合法权益以及其他违法用途的，所产生的全部法律责任由您承担，与佳蓝科技无关；如佳蓝科技因此被追责的，您应承担由此给佳蓝科技造成的一切责任、损失及其他不利后果。以下为{$ONEZ_PRODUCT_SHORTNAME}禁止您进行的一些活动的示例，包括但不限于：</li>
						<li><pre>违反国家规定的政治宣传和/或新闻信息；
涉及国家秘密和/或安全的信息；
封建迷信和/或淫秽、色情、下流的信息或教唆犯罪的信息；
博彩有奖、赌博游戏；
“私服”、“外挂”等非法互联网出版活动；
违反国家民族和宗教政策的信息；妨碍互联网运行安全的信息；
侵害他人合法权益的信息和/或其他有损于社会秩序、社会治安、公共道德的信息或内容；
其他违反法律法规、部门规章或国家政策的内容</pre></li>
						<li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</li>
					</ol>
				</p>
				<p>
					<strong>四、有限担保和免责声明 </strong>
					<ol>
						<li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
						<li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
						<li>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装{$ONEZ_PRODUCT_SHORTNAME}，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</li>
						<li>如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</li>
					</ol>
				</p>
			</div>
		</div>
		<form class="form-inline" role="form" method="post">
			<ul class="pager">
				<li class="pull-left" style="display:block;padding:5px 10px 5px 0;">
					<div class="checkbox">
						<label>
							<input type="checkbox"> 我已经阅读并同意此协议
						</label>
					</div>
				</li>
				<li class="previous"><a href="javascript:;" onclick="if(jQuery(':checkbox:checked').length == 1){jQuery('form')[0].submit();}else{alert('您必须同意软件许可协议才能安装！')};">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
			</ul>
		</form>
EOF;
	tpl_frame();
}

function tpl_install_env($ret = array()) {
	if(empty($ret['continue'])) {
		$continue = '<li class="previous disabled"><a href="javascript:;">请先解决环境问题后继续</a></li>';
	} else {
		$continue = '<li class="previous"><a href="javascript:;" onclick="$(\'#do\').val(\'continue\');$(\'form\')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>';
	}
	echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">服务器信息</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">参数</th>
					<th>值</th>
					<th></th>
				</tr>
				<tr class="{$ret['server']['os']['class']}">
					<td>服务器操作系统</td>
					<td>{$ret['server']['os']['value']}</td>
					<td>{$ret['server']['os']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['sapi']['class']}">
					<td>Web服务器环境</td>
					<td>{$ret['server']['sapi']['value']}</td>
					<td>{$ret['server']['sapi']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['php']['class']}">
					<td>PHP版本</td>
					<td>{$ret['server']['php']['value']}</td>
					<td>{$ret['server']['php']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['dir']['class']}">
					<td>程序安装目录</td>
					<td>{$ret['server']['dir']['value']}</td>
					<td>{$ret['server']['dir']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['disk']['class']}">
					<td>磁盘空间</td>
					<td>{$ret['server']['disk']['value']}</td>
					<td>{$ret['server']['disk']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['upload']['class']}">
					<td>上传限制</td>
					<td>{$ret['server']['upload']['value']}</td>
					<td>{$ret['server']['upload']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">PHP环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。</div>
		<div class="panel panel-default">
			<div class="panel-heading">PHP环境要求</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">选项</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['php']['version']['class']}">
					<td>PHP版本</td>
					<td>5.4或者5.4以上</td>
					<td>{$ret['php']['version']['value']}</td>
					<td>{$ret['php']['version']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['curl']['class']}">
					<td>cURL</td>
					<td>支持</td>
					<td>{$ret['php']['curl']['value']}</td>
					<td>{$ret['php']['curl']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['ssl']['class']}">
					<td>openSSL</td>
					<td>支持</td>
					<td>{$ret['php']['ssl']['value']}</td>
					<td>{$ret['php']['ssl']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['gd']['class']}">
					<td>GD2</td>
					<td>支持</td>
					<td>{$ret['php']['gd']['value']}</td>
					<td>{$ret['php']['gd']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['asp_tags']['class']}">
					<td>asp_tags</td>
					<td>关闭</td>
					<td>{$ret['php']['asp_tags']['value']}</td>
					<td>{$ret['php']['asp_tags']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">系统要求{$ONEZ_PRODUCT_SHORTNAME}整个安装目录必须可写, 才能使用{$ONEZ_PRODUCT_SHORTNAME}所有功能。</div>
		<div class="panel panel-default">
			<div class="panel-heading">目录权限监测</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">目录</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['write']['root']['class']}">
					<td>/</td>
					<td>整目录可写</td>
					<td>{$ret['write']['root']['value']}</td>
					<td>{$ret['write']['root']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['cache']['class']}">
					<td>/cache</td>
					<td>cache目录可写</td>
					<td>{$ret['write']['cache']['value']}</td>
					<td>{$ret['write']['cache']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['plugins']['class']}">
					<td>/plugins</td>
					<td>plugins目录可写</td>
					<td>{$ret['write']['plugins']['value']}</td>
					<td>{$ret['write']['plugins']['remark']}</td>
				</tr>
			</table>
		</div>
		<form class="form-inline" role="form" method="post">
			<input type="hidden" name="do" id="do" />
			<ul class="pager">
				<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
				{$continue}
			</ul>
		</form>
EOF;
	tpl_frame();
}

function tpl_install_db($error = '') {
  global $G;
	if(!empty($error)) {
		$message = '<div class="alert alert-danger">发生错误: ' . $error . '</div>';
	}
	$insTypes = array();
	if(file_exists(ONEZ_ROOT . '/index.php') && is_dir(ONEZ_ROOT . '/app') && is_dir(ONEZ_ROOT . '/web')) {
		$insTypes['local'] = ' checked="checked"';
	} else {
		$insTypes['remote'] = ' checked="checked"';
	}
	if (!empty($_POST['type'])) {
		$insTypes = array();
		$insTypes[$_POST['type']] = ' checked="checked"';
	}
	$disabled = empty($insTypes['local']) ? ' disabled="disabled"' : '';
  $db=$_POST['db'];
  $user=$_POST['user'];
  $extra=$_POST['extra'];
  empty($db['server']) && $db['server']='localhost';
  empty($db['username']) && $db['username']='root';
  empty($db['prefix']) && $db['prefix']='onez_';
  empty($db['name']) && $db['name']=str_replace('.','_',ONEZ_PRODUCT_PTOKEN);
  
  if($G['hasdata']){
    $error=<<<ONEZ
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-8">
						<input type="checkbox" name="clear" value="1" id="input-clear" />
            <label for="input-clear" style="color:red">
              您的数据库不为空，请重新建立数据库或是清空该数据库或更改表前缀！
              
              如选中此项，将会自动删除已有数据，请慎重操作
            </label>
					</div>
				</div>
ONEZ;
  }
	echo <<<EOF
	{$message}
	<form class="form-horizontal" method="post" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">数据库选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库主机</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[server]" value="$db[server]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库用户</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[username]" value="$db[username]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[password]" value="$db[password]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">表前缀</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[prefix]" value="$db[prefix]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库名称</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[name]" value="$db[name]">
					</div>
				</div>
        $error
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">管理选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员账号</label>
					<div class="col-sm-4">
						<input class="form-control" type="username" name="user[username]" value="$user[username]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="user[password]" value="$user[password]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">确认密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="user[password]" value="$user[password]">
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="do" id="do" />
		<input type="hidden" name="extra" value="{$extra}" />
		<ul class="pager">
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('continue');$('form')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
			<li style="color:red;padding-top:5px;display:inline-block">(点击继续安装时会读取云端插件，可能会出现短时间的卡顿，请耐心等待！)</li>
		</ul>
	</form>
	<script>
		var lock = false;
		function check(obj) {
			if(lock) {
				return;
			}
			$('.form-control').parent().parent().removeClass('has-error');
			var error = false;
			$('.form-control').each(function(){
				if($(this).val() == '') {
					$(this).parent().parent().addClass('has-error');
					this.focus();
					error = true;
				}
			});
			if(error) {
				alert('请检查未填项');
				return false;
			}
			if($(':password').eq(0).val() != $(':password').eq(1).val()) {
				$(':password').parent().parent().addClass('has-error');
				alert('确认密码不正确.');
				return false;
			}
			lock = true;
			$(obj).parent().addClass('disabled');
			$(obj).html('正在执行安装');
			return true;
		}
	</script>
EOF;
	tpl_frame();
  exit();
}
function tpl_install_onez($error = '',$content='') {
	if(!empty($error)) {
		$message = '<div class="alert alert-danger">发生错误: ' . $error . '</div>';
	}
  if($content){
    echo $content;
  	tpl_frame();
    exit();
  }
  
  
  $onez=$_POST['onez'];
  $time=time();
	echo <<<EOF
	{$message}
  <div class="alert alert-info">登录成功后可以自动注册您的站点，如已购买过产品，可以选择后快速安装</div>
	<form class="form-horizontal" method="post" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">登录佳蓝账号</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">佳蓝账号</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="onez[username]" value="$onez[username]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">佳蓝密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="onez[password]" value="$onez[password]">
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="do" id="do" />
		<input type="hidden" name="onez[type]" value="login" />
		<ul class="pager">
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('continue');$('form')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
			<li class="previous"><a href="http://www.onez.cn/onez/YmJzfC91c2VyL3JlZy5waHA=/mod/bbs.html" target="_blank">免费注册佳蓝账号 <span class="glyphicon glyphicon-fire"></span></a></li>
		</ul>
	</form>
	<script>
		var lock = false;
		function check(obj) {
			if(lock) {
				return;
			}
			$('.form-control').parent().parent().removeClass('has-error');
			var error = false;
			$('.form-control').each(function(){
				if($(this).val() == '') {
					$(this).parent().parent().addClass('has-error');
					this.focus();
					error = true;
				}
			});
			if(error) {
				alert('请检查未填项');
				return false;
			}
			lock = true;
			$(obj).parent().addClass('disabled');
			$(obj).html('正在执行安装');
			return true;
		}
	</script>
EOF;
	tpl_frame();
  exit();
}

function tpl_install_finish() {
  global $G;
  include_once(dirname(__FILE__).'/lib/onezphp.php');
	$modules = get_store_module();
	$themes = get_store_theme();
  $_REQUEST['mod']='/admin/index.php';
  $regurl=onez('onez')->href('/onez/cloud/profile.php');
  if(!$modules && !$themes){
    $classname='hide';
  }
  $ONEZ_PRODUCT_SUBJECT=ONEZ_PRODUCT_SUBJECT;
	echo <<<EOF
	<div class="page-header"><h3>安装完成</h3></div>
	<div class="alert alert-success">
		恭喜您!已成功安装“{$ONEZ_PRODUCT_SUBJECT}”系统，您现在可以: <a target="_blank" class="btn btn-success" href="./index.php">访问网站首页</a>
	</div>
	<div class="form-group $classname">
		<h5><strong>应用商城</strong></h5>
		<span class="help-block">应用商城特意为您推荐了一批优秀模块、主题，赶紧来安装几个吧！</span>
		<table class="table table-bordered">
			<tbody>
				{$modules}
				{$themes}
			</tbody>
		</table>
	</div>
EOF;
	tpl_frame();
}


function get_store_module() {
	$response = onez()->post(APP_STORE_API, http_build_query(array('action' => 'module')));
  $response=json_decode($response,1);
  if(empty($response) || empty($response['content'])){
    return;
  }
	$response = $response['content'];

	$modules = '';
  if(!$response['message']){
    return '';
  }
	foreach ($response['message'] as $key => $module) {
		if ($key % 3 < 1) {
			$modules .= '</tr><tr>';
		}
		$module['detail_link'] = APP_STORE_URL . trim($module['detail_link'], '.');
		$modules .= '<td>';
		$modules .= '<div class="col-sm-4">';
		$modules .= '<a href="' . $module['detail_link'] . '" title="查看详情" target="_blank">';
		$modules .= '<img src="' . $module['logo']. '"' . ' width="50" height="50" ' . $module['title'] . '" /></a>';
		$modules .= '</div>';
		$modules .= '<div class="col-sm-8">';
		$modules .= '<p><a href="' . $module['detail_link'] .'" title="查看详情" target="_blank">' . $module['title'] . '</a></p>';
		$modules .= '<p>安装量：<span class="text-danger">' . $module['purchases'] . '</span></p>';
		$modules .= '</div>';
		$modules .= '</td>';
	}
	$modules = substr($modules, 5) . '</tr>';

	return $modules;
}

function get_store_theme() {
	$response = onez()->post(APP_STORE_API, http_build_query(array('action' => 'theme')));
  $response=json_decode($response,1);
  if(empty($response) || empty($response['content'])){
    return;
  }
	$response = $response['content'];

	$modules = '';
  if(!$response['message']){
    return '';
  }

	$themes = '<tr><td colspan="' . count($response['message']) . '">';
	$themes .= '<div class="form-group">';
	foreach ($response['message'] as $key => $theme) {
		$theme['detail_link'] = APP_STORE_URL . trim($theme['detail_link'], '.');
		$themes .= '<div class="col-sm-2" style="padding-left: 7px;margin-right: 25px;">';
		$themes .= '<a href="' . $theme['detail_link'] .'" title="查看详情" target="_blank" /><img src="' . $theme['logo']. '" /></a>';
		$themes .= '<p></p><p class="text-right">';
		$themes .= '<a href="' . $theme['detail_link']. '" title="查看详情" target="_blank">'  . $theme['title'] . '</a></p>';
		$themes .= '</div>';
	}
	$themes .= '</div>';

	return $themes;
}

function tpl_resources() {
	$res = array(
		'logo' => 'iVBORw0KGgoAAAANSUhEUgAAAN4AAABQCAYAAAB23csfAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzM2NTAxQkIxRDg1MTFFN0EzNTc5RkVDODU5OUQ5REYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzM2NTAxQkMxRDg1MTFFN0EzNTc5RkVDODU5OUQ5REYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozMzY1MDFCOTFEODUxMUU3QTM1NzlGRUM4NTk5RDlERiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozMzY1MDFCQTFEODUxMUU3QTM1NzlGRUM4NTk5RDlERiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PsAP2RkAABm3SURBVHja7F0HmBVF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',
	);
	return $res;
}