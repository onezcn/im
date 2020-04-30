<?php

/* ========================================================================
 * $Id: reset.php 8371 2020-04-26 19:22:32Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$db=onez('db')->open('member')->db;
#安装数据库
$sysFile=$G['this']->path.'/config/db.tables.php';
if(file_exists($sysFile)){
  $dbtables=include($sysFile);
  if($dbtables){
    foreach($dbtables as $tablename=>$table){
      if($tablename=='member'){
        #continue;
      }
      $sql=onez('db')->create_mysql($tablename,$table['idname'],$table['fields']);
      list($sql)=explode('mb4DEFAULT',$sql);
      onez('db')->db()->query($sql);
    }
  }
}

$tableConfigFile=$G['this']->path.'/config/db.config.php';
if(file_exists($tableConfigFile)){
  $tableConfig=include($tableConfigFile);
  $tableConfig=onez()->strcode($tableConfig,'DECODE','www.onez.cn');
  #$tableConfig=base64_decode($tableConfig);
  $tableConfig=json_decode($tableConfig,1);
  foreach($tableConfig as $table){
    !$table['idname'] && $table['idname']='id';
    if($table['token']=='member'){
      #continue;
      $table['idname']='userid';
    }
    if($table['is_category']){
      $table['fields']=array_merge(array(
        'upid'=>array(
          'name'=>'分类编号',
          'token'=>'upid',
          'notnull'=>'1',
          'type'=>'number',
          'maxlen'=>'11',
          'defaultValue'=>'0',
          'theindex'=>'index',
        ),
      ),$table['fields']);
    }
    foreach($table['fields'] as $r){
      if($r['theindex']=='id'){
        $table['idname']=$r['token'];
        break;
      }
    }
    if($table['is_step']){
      $table['fields']['step']=array(
        'name'=>'排序',
        'token'=>'step',
        'notnull'=>'1',
        'type'=>'number',
        'maxlen'=>'11',
        'defaultValue'=>'0',
        'theindex'=>'index',
      );
    }
    #确保表已存在
    $sql="CREATE TABLE IF NOT EXISTS `onez_$table[token]` (
  `$table[idname]` int(11) NOT NULL COMMENT '编号'
  ) COMMENT='$table[name]' ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $r=mysql_query($sql,$db->link);
    #mysql_query($sql,$db->link);
    #分析当前表
    $rescolumns = mysql_query("SHOW FULL COLUMNS FROM `onez_$table[token]`",$db->link) ;
    $rows=array();
    $hasId=0;
    while($row = mysql_fetch_array($rescolumns,MYSQL_ASSOC)){
      $rows[$row['Field']]=$row;
      #id
      if($row['Extra']=='auto_increment'){
        if($row['Field']==$table['idname']){
          
        }else{
          mysql_query("ALTER TABLE `onez_$table[token]` CHANGE `$row[Field]` `$table[idname]` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号';",$db->link);
          $row['Field']=$table['idname'];
          unset($rows[$row['Field']]);
          $rows[$table['idname']]=$row;
        }
        $hasId=1;
      }
    }
    #创建缺失字段
    foreach($table['fields'] as $r){
      if($r['encrypt']){
        $r['type']='long';
        $r['theindex']='common';
      }
      if($r['theindex']=='id'){
        continue;
      }elseif($r['theindex']=='userid'){
        $r['theindex']='index';
      }
      if(in_array($r['token'],array('addtime','updatetime','userid'))){
        $r['notnull']=1;
        $r['defaultValue']=0;
        $r['theindex']='index';
        $r['type']='number';
        $r['maxlen']='11';
      }
      $sql='';
      $notnull='';
      if($r['notnull']){
        $notnull=" NOT NULL DEFAULT '$r[defaultValue]'";
      }else{
        if($r['defaultValue']===''){
          $notnull=" DEFAULT '$r[defaultValue]'";
        }
      }
      switch($r['type']){
        case 'string':
          $sql="`$r[token]` varchar($r[maxlen])$notnull";
          break;
        case 'number':
          $sql="`$r[token]` int($r[maxlen])$notnull";
          break;
        case 'money':
          $sql="`$r[token]` float($r[maxlen])$notnull";
          break;
        case 'long':
          $sql="`$r[token]` longtext";
          break;
      }
      if(!$sql){
        continue;
      }
      if($r['token']==$table['idname']){
        continue;
      }
      if(!empty($rows[$r['token']])){
        $row=$rows[$r['token']];
        if(strpos($sql,$row['Type'])===false){
          if($row['Key']&&$r['type']=='long'){
            $row['Key']='';
            if($r['theindex']=='common'){
              $sql="ALTER TABLE `onez_$table[token]` DROP INDEX `$r[token]`;";
              mysql_query($sql,$db->link);
            }
          }
          $sql="ALTER TABLE `onez_$table[token]` CHANGE `$r[token]` $sql COMMENT '$r[name]'";
          mysql_query($sql,$db->link);
        }
        if($row['Key']=='UNI'){
          if($r['theindex']=='unique'){
            
          }elseif($r['theindex']=='index'){
            $sql="ALTER TABLE `onez_$table[token]` DROP INDEX `$r[token]`;";
            mysql_query($sql,$db->link);
            $sql="ALTER TABLE `onez_$table[token]` ADD INDEX(`$r[token]`);";
            mysql_query($sql,$db->link);
          }elseif($r['theindex']=='common'){
            $sql="ALTER TABLE `onez_$table[token]` DROP INDEX `$r[token]`;";
            mysql_query($sql,$db->link);
          }
        }elseif($row['Key']=='MUL'){
          if($r['theindex']=='unique'){
            $sql="ALTER TABLE `onez_$table[token]` DROP INDEX `$r[token]`;";
            mysql_query($sql,$db->link);
            $sql="ALTER TABLE `onez_$table[token]` ADD UNIQUE(`$r[token]`);";
            mysql_query($sql,$db->link);
          }elseif($r['theindex']=='index'){
          }elseif($r['theindex']=='common'){
            $sql="ALTER TABLE `onez_$table[token]` DROP INDEX `$r[token]`;";
            mysql_query($sql,$db->link);
          }
        }elseif(!$row['Key']){
          if($r['theindex']=='index'){
            $sql="ALTER TABLE `onez_$table[token]` ADD INDEX(`$r[token]`);";
            mysql_query($sql,$db->link);
          }elseif($r['theindex']=='unique'){
            $sql="ALTER TABLE `onez_$table[token]` ADD UNIQUE(`$r[token]`);";
            mysql_query($sql,$db->link);
          }
        }
        #print_r($rows[$r['token']]);exit();
        continue;
      }
      if($sql){
        $sql="ALTER TABLE `onez_$table[token]` ADD $sql COMMENT '$r[name]'";
        mysql_query($sql,$db->link);
        if($r['theindex']=='index'){
          $sql="ALTER TABLE `onez_$table[token]` ADD INDEX(`$r[token]`);";
          mysql_query($sql,$db->link);
        }elseif($r['theindex']=='unique'){
          $sql="ALTER TABLE `onez_$table[token]` ADD UNIQUE(`$r[token]`);";
          mysql_query($sql,$db->link);
        }
      }
      #print_r($r);exit();
    }
    #创建主键
    if(!$hasId){
      if(!empty($rows[$table['idname']])){#修改
        mysql_query("ALTER TABLE `onez_$table[token]` ADD PRIMARY KEY( `$table[idname]`);",$db->link);
        mysql_query("ALTER TABLE `onez_$table[token]` CHANGE `$table[idname]` `$table[idname]` INT(11) NOT NULL AUTO_INCREMENT;",$db->link);
      }else{#创建
        $sql="ALTER TABLE `onez_$table[token]` ADD `$table[idname]` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`$table[idname]`);";
        mysql_query($sql,$db->link);
      }
    }
	  $res = mysql_query("SHOW CREATE TABLE `onez_$table[token]`", $db->link);
		$row = mysql_fetch_row( $res );
    #调整字段顺序
    $start=0;
    $curFields=array();
    foreach(explode("\n",$row[1]) as $v){
      $v=trim($v);
      $v=trim($v,',');
      if($v[0]=='`'){
        $start=1;
      }elseif(strpos($v,'KEY ')!==false){
        break;
      }
      if($start){
        $i=strpos($v,' ');
        $curFields[trim(substr($v,0,$i),'`')]=trim(substr($v,$i));
      }
    }
    $newFields=array_keys($table['fields']);
    $alerts=array();
    for($i=0;$i<count($newFields);$i++){
      if($i==0){
        $after=$table['idname'];
      }else{
        $after=$newFields[$i-1];
      }
      $field=$newFields[$i];
      if($field==$table['idname']){
        continue;
      }
      if(!$curFields[$field]){
        continue;
      }
      $curAfter='';
      $curFieldKeys=array_keys($curFields);
      foreach($curFieldKeys as $k=>$v){
        if($v==$field){
          $curAfter=$k==0?$table['idname']:$curFieldKeys[$k-1];
        }
      }
      if($after==$curAfter){
        continue;
      }
      $alerts[]="ALTER TABLE `onez_$table[token]` CHANGE `$field` `$field` {$curFields[$field]} AFTER `$after`;";
    }
    foreach($alerts as $v){
      mysql_query($v,$db->link);
    }  }
}
onez()->write($G['this']->path.'/cache/table.init.lock','');
