<?php

/* ========================================================================
 * $Id: database_mysql.php 11047 2017-12-02 06:14:59Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */
 class database_mysql{
	var $version;
	var $link;
	var $info;
  function __construct($info){
  	$this->info=$info;
    $this->link=@mysqli_connect($info['dbhost'], $info['dbuser'], $info['dbpass']);
		if(!$this->link) {
      exit('连接MySQL数据库失败，请检查账号和密码');
			return false;
		} else {
			if(!@mysqli_select_db($this->link,$info['dbname'])){
        @mysqli_query($this->link,'CREATE DATABASE IF NOT EXISTS '.$info['dbname'].' DEFAULT CHARSET '.$info['dbcharset'].' COLLATE '.$info['dbcharset'].'_general_ci;');
        mysqli_select_db($this->link,$info['dbname']);
      };
			$info['dbcharset'] && @$this->query('set names '.$info['dbcharset']);
		}
    register_shutdown_function(array(&$this, 'close'));
  }
  
  function close(){
    mysqli_close($this->link);
  }
  
	function version() {
		if(empty($this->version)) {
			$this->version = mysqli_get_server_info($this->link);
		}
		return $this->version;
	}

	function error() {
		return (($this->link) ? mysqli_error($this->link) : mysqli_error());
	}

	function errno() {
		return (($this->link) ? mysqli_errno($this->link) : mysqli_errno());
	}
  
  function query($sql,$type='') {
    global $G;
    $time1=microtime(true);
    $sql=str_replace(' onez_onezonez_',' onezonez_',$sql);
    $sql=str_replace(' #_',' `'.$this->info['dbname'].'`.'.$this->info['tablepre'],$sql);
    $sql=str_replace(' `#_',' `'.$this->info['dbname'].'`.`'.$this->info['tablepre'],$sql);
    $sql=str_replace(' onez_',' `'.$this->info['dbname'].'`.'.$this->info['tablepre'],$sql);
    $sql=str_replace(' `onez_',' `'.$this->info['dbname'].'`.`'.$this->info['tablepre'],$sql);
    $sql=str_replace(' onezonez_',' `'.$this->info['dbname'].'`.onez_',$sql);
    $sql=preg_replace('/ `onez_(.+?)`\.`'.$this->info['tablepre'].'(.+?)`/is',' `onez_$1`',$sql);
    $G['sql_query'][]=$sql;
    
    $query=@mysqli_query($this->link,$sql);
    $time2=microtime(true);
    $G['sql_query'][]='['.($time2-$time1).']'.$sql;
    
		if(!$query){
      if(function_exists('query_callback')){
        return query_callback($sql,$this->errno(),$this->error());
      }
      //onez('php')->where($this->errno().'['.$this->error().']'.$sql);
      if(!defined('IS_ONEZ_DEBUG')){
        exit('数据库错误:'.$this->errno());
      }
      exit($this->errno().'['.$this->error().']'.$sql);
    }
    $G['sql_query_time']+=($time2-$time1);
		return $query;
  }
	function free_result($query) {
		return @mysqli_free_result($query);
	}
  
  function getFields($table) {
    $fields=array();
    $result=$this->query("SHOW FIELDS FROM onez_$table");
    while ($key = $this->fetch_array($result)) {
      $fields[]=$key['Field'];
    }
    return $fields;
  }
  function fetch_array($sql) {
    return mysqli_fetch_array($sql,MYSQLI_ASSOC);
  }
	function insert_id() {
		return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
  
  function rows($table,$vars="",$field='*'){
    if($vars){
      $vars = "where $vars";
    }
    $result=$this->query("select count($field) as id from onez_$table $vars");
    if(!$result)return 0;
    $rs=$this->fetch_array($result);
    return $rs['id'];
  }
  
  function checkKey($key){
    if(in_array($key,array('force','field','type','name'))){
      $key='`'.$key.'`';
    }
    return $key;
  }
  function insert($table,$arr) {
    $A=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="`$k`=$v";
    }
    $query=$this->query("insert into onez_$table set ".implode(',',$A));
    return $this->insert_id();
  }
  function replace($table,$arr) {
    $A=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="`$k`=$v";
    }
    $query=$this->query("replace into onez_$table set ".implode(',',$A));
    return $query;
  }
  function update($table,$arr,$vars) {
    if($vars){
      $vars=$this->xxx($vars);
      $vars = "where $vars";
    }
    $A=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="`$k`=$v";
    }
    $query=$this->query("update onez_$table set ".implode(',',$A)." $vars");
    return $query;
  }
  function delete($table,$vars) {
    if($vars){
      $vars=$this->xxx($vars);
      $vars = "where $vars";
    }
    $query=$this->query("delete from onez_$table $vars");
  }
  function select($table,$key,$vars=""){
    if($vars){
      $vars=$this->xxx($vars);
      $vars = "where $vars";
    }
    $result=$this->query("select $key from onez_$table $vars");
    if(!$result){
      return false;
    }else{
      $rs=$this->fetch_array($result);
			$this->free_result($result);
      return $rs[$key];
    }
  }
  function cache_get($table,$key,$vars=""){
    global $G;
    if(defined('ONEZ_DB_CACHE')){
      $hash=md5("$table\t$key\t".$this->xxx($vars));
      if(empty($G['ONEZ_DB_CACHE'][$hash])){
        $file=ONEZ_DB_CACHE.'/one/'.$hash.'.php';
        if(file_exists($file)){
          $G['ONEZ_DB_CACHE'][$hash]=include($file);
        }else{
          $G['ONEZ_DB_CACHE'][$hash]=$this->one($table,$key,$vars);
          onez()->write($file,"<?php\n!defined('IN_ONEZ') && exit('Access Denied');\nreturn ".var_export($G['ONEZ_DB_CACHE'][$hash],1).";",'a+');
        }
      }
      return $G['ONEZ_DB_CACHE'][$hash];
    }else{
      return $this->one($table,$key,$vars);
    }
  }
  function cache_remove($table,$key,$vars=""){
    if(defined('ONEZ_DB_CACHE')){
      $hash=md5("$table\t$key\t".$this->xxx($vars));
      if(!empty($G['ONEZ_DB_CACHE'][$hash])){
        unset($G['ONEZ_DB_CACHE'][$hash]);
      }
      $file=ONEZ_DB_CACHE.'/one/'.$hash.'.php';
      if(file_exists($file)){
        @unlink($file);
      }
    }else{
    }
  }
  function one($table,$key,$vars=""){
    if($vars){
      $vars=$this->xxx($vars);
      $vars = "where $vars";
    }
    $result=$this->query("select $key from onez_$table $vars limit 1");
    if(!$result){
      return array();
    }else{
      $rs=$this->fetch_array($result);
			$this->free_result($result);
      return $rs;
    }
  }
  function autoindex($table){
    $result=mysqli_query("SHOW TABLE STATUS LIKE 'onez_$table'");
    if(!$result){
      return false;
    }else{
      $rs=mysqli_fetch_array($result);
			$this->free_result($result);
      return $rs['Auto_increment'];
    }
  }
  function xxx($vars){
    global $G;
    if($G['userids']){
      if(strpos($vars,"userid='")!==false){
        if(preg_match('/userid=\'([0-9]+)\'/',$vars,$m)){
          $x=array();
          foreach(explode(',',$G['userids']) as $v){
            $v=trim($v);
            if($v){
              $x[]="userid='$v'";
            }
          }
          if($x){
            $vars='('.implode(' or ',$x).')';
          }
        }
      }
    }
    $vars=str_replace('\\','',$vars);
    return $vars;
  }
  function record($table,$key,$vars="",$limit=""){
    if($vars){
      $vars=$this->xxx($vars);
      $vars = "where $vars";
    }
    if($limit){
      $limit = "limit $limit";
    }
    $k=explode(",",$key);
    $record = Array();
    $result=$this->query("select $key from onez_$table $vars $limit");
    $j=0;
    if(!$result){
      return $record;
    }
    while($onez=$this->fetch_array($result)){
      $record[$j]=$onez;
      $j++;
    }
		$this->free_result($result);
    return $record;
  }
	function sql($table) {
	  $res = mysqli_query($this->link,"SHOW CREATE TABLE `onez_$table`");
		$row = mysqli_fetch_row( $res ); 
	  return $row[1];
	}
	function createtable($sql) {
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE IF NOT EXISTS\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
		return preg_replace("/^\s*(CREATE TABLE IF NOT EXISTS\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
			(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT charset=utf8" : " TYPE=$type");
	}
	function runquery($sql) {
		$A = $ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $Query) {
			$queries = explode("\n", trim($Query));
			foreach($queries as $query) {
				$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		foreach($ret as $query) {
			$query = trim($query);
			if($query) {
				if(substr($query, 0, 12) == 'CREATE TABLE') {
					$name = preg_replace("/CREATE TABLE IF NOT EXISTS ([a-z0-9_]+) .*/is", "\\1", $query);
					$A[]=$this->createtable($query);
				} else {
					$A[]=$query;
				}
			}
		}
    foreach($A as $line){
      $this->query($line);
    }
		return $A;
	}
  function page($sql,$totalput=false,$maxperpage=20){
    global $PHP_SELF;
    $thispage=max(intval(onez()->gp('page')),1);
    $sql=$this->xxx($sql);
    $result=$this->query($sql);
    if($totalput===false){
      $totalput=mysql_num_rows($result);
    }
    if (($totalput %$maxperpage)==0){
      $PageCount=intval($totalput /$maxperpage);
    }else{
      $PageCount=intval($totalput /$maxperpage+1);
    } 
    $PageCount<1 && $PageCount=1;
    $thispage>$PageCount && $thispage=$PageCount;
    $sql="$sql limit ".(($thispage-1)*$maxperpage).",$maxperpage";
    
    $result=$this->query($sql);
    $record = Array();
    while($onez=$this->fetch_array($result)){
      $record[]=$onez;
    }
    $ms="";unset($A,$B);
    unset($_GET['page']);
    $strs=http_build_query($_GET);
    $strs=$strs ? $PHP_SELF.'?'.$strs.'&page=*' : $PHP_SELF.'?page=*';
    if(function_exists('pageinfo')){
      return array($record,pageinfo($strs,$PageCount,$thispage));
    }
    if($strs && $PageCount>1){
      $buffer = null;
      $index = '首页';
      $pre = '上一页';
      $next = '下一页';
      $last = '末页';
  
      if ($PageCount<=7) { 
        $range = range(1,$PageCount);
      } else {
        $min = $thispage - 3;
        $max = $thispage + 3;
        if ($min < 1) {
          $max += (3-$min);
          $min = 1;
        }
        if ( $max > $PageCount ) {
          $min -= ( $max - $PageCount );
          $max = $PageCount;
        }
        $min = ($min>1) ? $min : 1;
        $range = range($min, $max);
      }
      
      if ($thispage > 1) {
        $buffer .= "<li><a href='".str_replace('*',1,$strs)."'>{$index}</a></li> <li><a href='".str_replace('*',$thispage-1,$strs)."' class='prev'>{$pre}</a></li>";
      }
      foreach($range AS $one) {
        if ( $one == $thispage ) {
          $buffer .= "<li><a class='current'>{$one}</a></li>";
        } else {
          $buffer .= "<li><a href='".str_replace('*',$one,$strs)."'>{$one}</a></li>";
        }
      }
      if ($thispage < $PageCount) {
        $buffer .= "<li><a href='".str_replace('*',$thispage+1,$strs)."' class='nxt'>{$next}</a></li> <li><a href='".str_replace('*',$PageCount,$strs)."'>{$last}</a></li>";
      }
      $page='<ul class="pagination">'.$buffer . '</ul>';
		}
    return array($record,$page,$totalput);
  }
}
?>