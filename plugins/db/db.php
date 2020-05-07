<?php

/* ========================================================================
 * $Id: db.php 7933 2018-03-18 17:46:12Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_db_sql{
  var $db='table_default';
  var $tablename='table_default';
  function __construct($db,$tablename){
    $this->db=$db;
    $this->tablename=$tablename;
  }
  function page($xxx='',$totalput=false,$maxperpage=20){
    $tablename=$this->tablename;
    $sql="select * from onez_$tablename";
    $xxx && $sql.=" where $xxx";
    
    return $this->db->page($sql,$totalput,$maxperpage);
  }
  function page2($sql='',$totalput=false,$maxperpage=20){
    $tablename=$this->tablename;
    return $this->db->page($sql,$totalput,$maxperpage);
  }
  function insert($arr){
    return $this->db->insert($this->tablename,$arr);
  }
  function update($arr,$xxx){
    return $this->db->update($this->tablename,$arr,$xxx);
  }
  function one($xxx){
    return $this->db->one($this->tablename,'*',$xxx);
  }
  function delete($xxx){
    return $this->db->delete($this->tablename,$xxx);
  }
  function record($xxx){
    return $this->db->record($this->tablename,'*',$xxx);
  }
  function record2($tablename,$fields='*',$xxx=''){
    return $this->db->record($tablename,$fields,$xxx);
  }
  function rows($xxx){
    return $this->db->rows($this->tablename,$xxx);
  }
  function query($sql){
    return $this->db->query($sql);
  }
}
class onezphp_db extends onezphp{
  var $dbtype='mysql';
  var $_db;
  var $init_token;
  function __construct($init_token=''){
    global $G;
    if($init_token==''){
      return;
    }
    $this->init_token=$init_token;
  }
  function options(){
    $item=onez('cache')->get('options');
    if(!$item['dbhost']){
      $item['dbhost']='localhost';
      $item['dbuser']='root';
      $item['dbname']='onez_app';
      $item['tablepre']='onez_';
    }
    $options=array();
    $options['dbhost']=array('label'=>'数据库地址','type'=>'text','key'=>'dbhost','hint'=>'如:localhost','notempty'=>'数据库地址不能为空','value'=>$item['dbhost']);
    $options['dbuser']=array('label'=>'数据库账号','type'=>'text','key'=>'dbuser','hint'=>'如:root','notempty'=>'数据库账号不能为空','value'=>$item['dbuser']);
    $options['dbpass']=array('label'=>'数据库密码','type'=>'text','key'=>'dbpass','hint'=>'','notempty'=>'','value'=>$item['dbpass']);
    $options['dbname']=array('label'=>'数据库名称','type'=>'text','key'=>'dbname','hint'=>'如:onez_app，必须已创建完成','notempty'=>'数据库名称不能为空','value'=>$item['dbname']);
    $options['tablepre']=array('label'=>'表名前缀','type'=>'text','key'=>'tablepre','hint'=>'onez_','notempty'=>'','value'=>$item['tablepre']);
    
    $options_charset=array(
      'utf8'=>'UTF-8',
    );
    $options['dbcharset']=array('label'=>'数据库编码','type'=>'select','key'=>'dbcharset','options'=>$options_charset);
    return $options;
  }
  function db(){
    global $G;
    if($this->_db){
      return $this->_db;
    }
    if($this->dbtype=='mysql'){
      if($this->init_token){
        $init_token=$this->init_token;
        if(!$G['init_db.'.$init_token]){
          $G['init_db.'.$init_token]=1;
          !$items['db_databases'] && $items=onez('cache')->get('options');
          if($items['db_databases'][$init_token]){
            $items['db_databases'][$init_token]['pconnect']=1;
            if(!class_exists('database_mysql')){
              if(function_exists('mysqli_connect')) {
                include_once(dirname(__FILE__).'/lib/database_mysqli.php');
              }else{
                include_once(dirname(__FILE__).'/lib/database_mysql.php');
              }
            }
            $this->_db=new database_mysql($items['db_databases'][$init_token]);
          }
        }
      }
      if(!class_exists('database_mysql')){
        if(function_exists('mysqli_connect')) {
          include_once(dirname(__FILE__).'/lib/database_mysqli.php');
        }else{
          include_once(dirname(__FILE__).'/lib/database_mysql.php');
        }
      }
      $key=$init_token?$init_token:'default';
      if(file_exists(ONEZ_ROOT.'/config/db.'.$key.'.php')){
        $info=include(ONEZ_ROOT.'/config/db.'.$key.'.php');
      }else{
        $info=$this->get('info');
        !$info && $info=array(
          'dbhost'=>onez('cache')->option('dbhost',0),
          'dbuser'=>onez('cache')->option('dbuser',0),
          'dbpass'=>onez('cache')->option('dbpass',0),
          'dbname'=>onez('cache')->option('dbname',0),
          'dbcharset'=>onez('cache')->option('dbcharset',0),
          'tablepre'=>onez('cache')->option('tablepre',0),
          'pconnect'=>1,
        );
        if(!$info['dbhost'] || !$info['dbuser'] || !$info['dbname'] || !$info['dbcharset']){
          onez()->showerror(0,'请先安装网站');
        }
      }
      $this->_db=new database_mysql($info);
    }elseif($this->dbtype=='sqlite'){
      include_once(dirname(__FILE__).'/lib/database_sqlite.php');
      $info=array(
        'dbname'=>$G['dbfile'],
        'tablepre'=>'onez_',
      );
      $this->_db=new database_sqlite($info);
    }
    
    register_shutdown_function(array(&$this, 'close'));
    return $this->_db;
  }
  function close(){
    $this->_db=false;
  }
  function open($tableid){
    $tablename=$tableid;
    $table=new onezphp_db_sql($this->db(),$tablename);
    return $table;
  }
  function fieldtypes(){
    $fieldtypes=array();
    $fieldtypes['text']=array('name'=>'文本','len'=>'120');
    $fieldtypes['textarea']=array('name'=>'描述','len'=>'500');
    $fieldtypes['type']=array('name'=>'类型','len'=>'30');
    $fieldtypes['int']=array('name'=>'数字','len'=>'11');
    $fieldtypes['amt']=array('name'=>'金额','len'=>'11');
    $fieldtypes['time']=array('name'=>'时间','len'=>'11');
    $fieldtypes['addr']=array('name'=>'坐标','len'=>'11,6');
    $fieldtypes['long']=array('name'=>'超大文本','len'=>'');
    return $fieldtypes;
  }
  function create_field($rs) {
    !$rs['token'] && $rs['token']=$rs['fieldname'];
    if($rs['fieldname'] && strpos($rs['token'],'`')===false){
      include(dirname(__FILE__).'/lib/notwords.mysql.php');
      if(in_array($rs['token'],$words)){
        $rs['token']="`{$rs['token']}`";
      }
    }
    $sql='';
    $mylen=$rs['mylen'];
    $mylen2=(int)$rs['mylen'];
    $len=$fieldtypes[$rs['fieldtype']]['len'];
    switch($rs['fieldtype']){
      case 'text':
      case 'textarea':
      case 'type':
        $mylen2>0 && $len=$mylen2;
        $sql="$rs[token] varchar($len) DEFAULT NULL";
        break;
      case 'int':
        $mylen2>0 && $len=$mylen2;
        $sql="$rs[token] int($len) NOT NULL DEFAULT '0'";
        break;
      case 'amt':
        $sql="$rs[token] float(11,2) NOT NULL DEFAULT '0'";
        break;
      case 'time':
        $sql="$rs[token] int(11) NOT NULL DEFAULT '0'";
        break;
      case 'addr':
        $sql="$rs[token] float($len) NOT NULL DEFAULT '0'";
        break;
      case 'long':
        $sql="$rs[token] longtext";
        break;
    }
    return $sql;
  }
  function create_mysql($tablename,$idname,$fields) {
    $sqlcode=$myfields=$keys=array();
    $myfields[]='  '.$idname.' int(11) NOT NULL AUTO_INCREMENT';
    $fieldtypes=$this->fieldtypes();
    $hasFields=array();
    include(dirname(__FILE__).'/lib/notwords.mysql.php');
    foreach($fields as $rs){
      $rs['token']=$rs['fieldname'];
      if(in_array($rs['token'],$words)){
        $rs['token']="`{$rs['token']}`";
      }
      if(in_array($rs['token'],$hasFields)){
        continue;
      }
      $hasFields[]=$rs['token'];
      
      $sql=$this->create_field($rs);
      if(!$sql){
        continue;
      }
      $myfields[]='  '.$sql;
      if($rs['is_index']){
        $keys[]='  KEY '.$rs['token'].' ('.$rs['token'].')';
      }
    }
    $myfields[]='  PRIMARY KEY ('.$idname.')';
    foreach($keys as $k=>$v){
      $myfields[]=$v;
    }

    $sqlcode[]='CREATE TABLE IF NOT EXISTS onez_'.$tablename.' (';
    $sqlcode[]=implode(",\n",$myfields);
    $sqlcode[]=') ENGINE=MyISAM;';
    $result=implode("\n",$sqlcode);
    return $result;
  }
  function menus(){
    $menus=array();
    $menus[]=array (
      'name' => '多数据库管理',
      'url' => onez()->href('/databases.php?_onez=db'),
      'icon' => '',
    );
    return $menus;
  }
}