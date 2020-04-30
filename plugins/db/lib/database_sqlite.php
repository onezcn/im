<?php

/* ========================================================================
 * $Id: database_sqlite.php 8041 2018-03-18 17:47:53Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

class OnezSqlite3 extends SQLite3{
  function __construct($dbname)
  {
     $this->open($dbname);
  }
}
class database_sqlite{
	var $version;
	var $link;
	var $info;
  function database_sqlite($info){
    if(!file_exists($info['dbname'])){
      onez()->mkdirs(dirname($info['dbname']));
    }
  	$this->info=$info;
		$this->link=new OnezSqlite3($info['dbname']);
    
		if(!$this->link) {
      exit($this->link->lastErrorMsg());
			return false;
		} else {
		}
    register_shutdown_function(array(&$this, 'close'));
  }
  
  function close(){
    $this->link->close();
  }
  
	function version() {
		if(empty($this->version)) {
			$this->version = $this->link->version();
		}
		return $this->version;
	}

	function error() {
		return $this->link->lastErrorMsg();
	}

	function errno() {
		return $this->link->lastErrorCode();
	}
  
  function query($sql,$type='') {
    $sql=str_replace(' #_',' '.$this->info['tablepre'],$sql);
    $sql=str_replace(' [#_',' ['.$this->info['tablepre'],$sql);
    $sql=str_replace(' onez_',' '.$this->info['tablepre'],$sql);
    $sql=str_replace(' [onez_',' ['.$this->info['tablepre'],$sql);
    
    $query = $this->link->query($sql);
		if(!$query){
      if(function_exists('query_callback')){
        return query_callback($sql,$this->errno(),$this->error());
      }
      //$this->link->query('ALTER TABLE `onez_users` ADD `nickname` varchar( 255 ) NOT NULL Default "";');
      //$this->link->query('ALTER TABLE `onez_users` ADD `siteuid` integer( 11 ) NOT NULL Default 0;');
      //$this->link->query('ALTER TABLE `onez_users` ADD `rank` integer( 11 ) NOT NULL Default 0;');
      exit("\n$sql\n".$this->errno().'['.$this->error().']');
    }
		return $query;
  }
	function free_result($query) {
		
	}
  
  function getFields($table) {
    $fields=array();
    $result=$this->query("SHOW FIELDS FROM onez_$table");
    while ($key = $this->fetch_array($result)) {
      $fields[]=$key['Field'];
    }
    return $fields;
  }
  function fetch_array($result) {
    return $result->fetchArray(SQLITE3_ASSOC);
  }
	function insert_id() {
		return $this->link->lastInsertRowID();
	}
	function result() {
    if($result){
      return $result->fetchArray(SQLITE3_ASSOC);
    }
	}
  
  function rows($table,$vars="",$field='*'){
    if($vars){
      $vars = "where $vars";
    }
    $result=$this->query("select count($field) as id from onez_$table $vars");
    if(!$result)return 0;
    $rs=$result->fetchArray(SQLITE3_ASSOC);
    return $rs['id'];
  }
  
  function checkKey($key){
    if(in_array($key,array('force','field','type','name'))){
      $key='['.$key.']';
    }
    return $key;
  }
  function insert($table,$arr) {
    $A=$B=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="[$k]";
			$B[]=$v;
    }
    $query=$this->query("insert into onez_$table (".implode(',',$A).")values(".implode(',',$B).")");
    return $this->insert_id();
  }
  function replace($table,$arr) {
    $A=$B=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="[$k]";
			$B[]=$v;
    }
    $query=$this->query("replace into onez_$table (".implode(',',$A).")values(".implode(',',$B).")");
    return $query;
  }
  function update($table,$arr,$vars) {
    if($vars){
      $vars = "where $vars";
    }
    $A=array();
    foreach($arr as $k=>$v){
      $v=var_export((string)$v,true);
			$A[]="[$k]=$v";
    }
    $query=$this->query("update onez_$table set ".implode(',',$A)." $vars");
    $this->link->changes();
    return $query;
  }
  function delete($table,$vars) {
    if($vars){
      $vars = "where $vars";
    }
    $query=$this->query("delete from onez_$table $vars");
    $this->link->changes();
  }
  function select($table,$key,$vars=""){
    if($vars){
      $vars = "where $vars";
    }
    $result=$this->query("select $key from onez_$table $vars");
    if(!$result){
      return false;
    }else{
      $rs=$result->fetchArray(SQLITE3_ASSOC);
			$this->free_result($result);
      return $rs[$key];
    }
  }
  function one($table,$key,$vars=""){
    if($vars){
      $vars = "where $vars";
    }
    $result=$this->query("select $key from onez_$table $vars limit 1");
    if(!$result){
      return array();
    }else{
      $rs=$result->fetchArray(SQLITE3_ASSOC);
			$this->free_result($result);
      return $rs;
    }
  }
  function autoindex($table){
    
  }
  function record($table,$key,$vars="",$limit=""){
    if($vars){
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
    while($onez=$result->fetchArray(SQLITE3_ASSOC)){
      $record[$j]=$onez;
      $j++;
    }
		$this->free_result($result);
    return $record;
  }
	function sql($table) {
    
	}
	function createtable($sql) {
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE IF NOT EXISTS\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
		return preg_replace("/^\s*(CREATE TABLE IF NOT EXISTS\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql);
	}
	function runquery($sql) {
		$A = $ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $queryA) {
			$queries = explode("\n", trim($queryA));
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
  function page($sql,$totalput,$maxperpage=20){
    global $PHP_SELF;
    $thispage=max(intval(onez()->gp('page')),1);
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
    while($onez=$result->fetchArray(SQLITE3_ASSOC)){
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
        $buffer .= "<a href='".str_replace('*',1,$strs)."'>{$index}</a> <a href='".str_replace('*',$thispage-1,$strs)."' class='prev'>{$pre}</a>";
      }
      foreach($range AS $one) {
        if ( $one == $thispage ) {
          $buffer .= "<a class='current'>{$one}</a>";
        } else {
          $buffer .= "<a href='".str_replace('*',$one,$strs)."'>{$one}</a>";
        }
      }
      if ($thispage < $PageCount) {
        $buffer .= "<a href='".str_replace('*',$thispage+1,$strs)."' class='nxt'>{$next}</a> <a href='".str_replace('*',$PageCount,$strs)."'>{$last}</a>";
      }
      $page='<div class="page-list pg">'.$buffer . '</div>';
		}
    return array($record,$page);
  }
}
?>