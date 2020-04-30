<?php
!defined('IN_ONEZ') && exit('Access Denied');
class onezphp_data extends onezphp{
  var $data_table='data';
  var $sysFields=array('id','namespace','tablename','userid','appid','siteid','upid','token','addtime','updatetime','extra','keywords','step',
    'index1',
    'index2',
    'index3',
    'index4',
    'index5',
    'index6',
    'index7',
    'index8',
    'index9',
    'index10',
    'index11',
    'index12',
    'key1',
    'key2',
    'key3',
    'key4',
    'key5',
    'key6',
    'key7',
    'key8',
    'key9',
    'key10',
    'key11',
    'key12',
    'money1',
    'money2',
    'money3',
  );
  var $db;
  var $alias=array();
  function __construct($namespace){
    $this->set('namespace',$namespace);
  }
  function init($namespace,$tablename,$alias=array(),$force=0){
    $key=md5("$namespace\t$tablename");
    if($this->get('force')!=1 && file_exists(onez('cache')->file($key))){
      return $this;
    }
    
    
    $myalias=array();
    $k1=$k2=0;
    if($alias && is_array($alias)){
      $types=array(
        'text'=>array('name'=>'key','index'=>0),
        'number'=>array('name'=>'index','index'=>0),
        'money'=>array('name'=>'money','index'=>0),
      );
      foreach($types as $k=>$v){
        if($alias[$k]){
          foreach(explode(',',$alias[$k]) as $v2){
            $types[$k]['index']++;
            $myalias[$v2]=$types[$k]['name'].$types[$k]['index'];
          }
        }
      }
    }
    onez('cache')->set($key,$myalias);
    return $this;
  }
  function open($tablename,$data_alias=0){
    global $G;
    $namespace=$this->get('namespace');
    if(!$namespace){
      if(defined('ONEZ_DATA_NAMESPACE')){
        $namespace=ONEZ_DATA_NAMESPACE;
      }
    }
    !$namespace && $namespace='system';
    #!$namespace && exit("请设置命名空间: onez($this->data_table,'命名空间名称')");
    $alias=onez('cache')->get(md5("$namespace\t$tablename"));
    
    $this->set('namespace',$namespace);
    $this->set('tablename',$tablename);
    $this->set('alias',$alias);
    $this->data_table='data';
    if(defined('DATA_ALIAS')){
      $data_alias=DATA_ALIAS;
    }
    if(!$data_alias){
      $file=ONEZ_ROOT.'/config/data.alias/'.$namespace.'.php';
      if(file_exists($file)){
        $data_alias=$namespace;
      }
    }
    if(!$data_alias){
      if($G['data_alias']){
        $mydata_alias=onez('call')->call('data_alias',array($namespace,$tablename),$G['data_alias']);
        if($mydata_alias){
          $data_alias=$mydata_alias[0];
        }
      }
    }
    if($data_alias=='default'){
      $data_alias=0;
    }
    $this->db=onez('db')->db();
    $this->data_alias=$data_alias;
    if($data_alias!=='system'){
      if($data_alias){
        $file=ONEZ_ROOT.'/config/data.alias/'.$data_alias.'.php';
        if(file_exists($file)){
          onez('db',$data_alias)->set('info',include($file));
          $this->db=onez('db',$data_alias)->db();
          $this->data_table=$data_alias;
          return $this;
        }
      }elseif($G['this']->data_alias){
        $data_alias=$G['this']->data_alias;
        $file=ONEZ_ROOT.'/config/data.alias/user.php';
        if(file_exists($file)){
          onez('db',$data_alias)->set('info',include($file));
          $this->db=onez('db',$data_alias)->db();
          $this->data_table=$data_alias;
          $this->check_table_exists($data_alias);
          return $this;
        }
      }
    }else{
    }
    return $this;
  }
  function check_table_exists($tablename){
    global $G;
    if($this->is_check_table_exists[$tablename]){
      return;
    }
    $this->is_check_table_exists[$tablename]=1;
    $cacheFile=ONEZ_ROOT.'/cache/plugins/data/log/'.$tablename;
    if(!file_exists($cacheFile)){
      $cacheFile=ONEZ_CACHE_PATH.'/plugins/data/log/'.$tablename;
      if(!file_exists($cacheFile)){
        $sql=onez('db')->db()->sql('data');
        $sql=str_replace('CREATE TABLE `onez_data`','CREATE TABLE IF NOT EXISTS onez_'.$tablename.'',$sql);
        list($sql)=explode('AUTO_INCREMENT=',$sql);
        $sql.='DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
        $this->db->query($sql);
        onez()->write($cacheFile,1);
      }
    }
  }
  function appid($has=1){
    $this->appid_once=true;
    $this->appid_before=$this->get('noappid');
    if($has){
      $this->set('noappid',0);
    }else{
      $this->set('noappid',1);
    }
    return $this;
  }
  function noappid(){
    return $this->appid(0);
  }
  function xxx($arr,$type){
    $appid=(int)onez()->gp('appid');
    $siteid=(int)onez()->gp('siteid');
    $has_appid=1;
    if($this->get('noappid')){
      $has_appid=0;
      $appid=0;
      $siteid=0;
    }
    $namespace=$this->get('namespace');
    $tablename=$this->get('tablename');
    $alias=$this->get('alias');
    if($type=='insert'){
      $arr['namespace']=$namespace;
      $arr['tablename']=$tablename;
      !$arr['addtime'] && $arr['addtime']=time();
      !$arr['updatetime'] && $arr['updatetime']=time();
      if($has_appid){
        $arr['appid']=$appid;
        $arr['siteid']=$siteid;
      }
      foreach($alias as $k=>$v){
        if(isset($arr[$k])){
          $arr[$v]=$arr[$k];
          unset($arr[$k]);
        }
      }
    }elseif($type=='update'){
      !$arr['updatetime'] && $arr['updatetime']=time();
      foreach($alias as $k=>$v){
        if(isset($arr[$k])){
          $arr[$v]=$arr[$k];
          unset($arr[$k]);
        }
      }
      unset($arr['id']);
      unset($arr['namespace']);
    }elseif($type=='where'){
      if(is_array($arr)){
        if($has_appid){
          $arr['appid']=$appid;
          $arr['siteid']=$siteid;
        }
        $arr['namespace']=$namespace;
        $arr['tablename']=$tablename;
        foreach($alias as $k=>$v){
          if(isset($arr[$k])){
            $arr[$v]=$arr[$k];
            unset($arr[$k]);
          }
        }
        $xxx='';
        foreach($arr as $k=>$v){
          $xxx && $xxx.=' and ';
          $xxx.="`$k`=".var_export((string)$v,1);
        }
        $arr=$xxx;
      }else{
        foreach($alias as $k=>$v){
          $arr=str_replace('`'.$k.'`','`'.$v.'`',$arr);
        }
      }
      $myarr="tablename='$tablename' and namespace='$namespace'";
      if($has_appid){
      	$myarr.=" and appid='$appid' and siteid='$siteid'";
      }
      if($arr){
        if(strpos($arr,' order by ')!==false || strpos($arr,' limit ')!==false){
          $myarr.=' and '.preg_replace('/^(.+?)( order by | limit )/is','($1)$2',$arr);
        }else{
          $myarr.=' and ('.$arr.')';
        }
      }
      $arr=$myarr;
    }
    
    if($type=='insert'||$type=='update'){
      $S=$E=array();
      foreach($arr as $k=>$v){
        if(in_array($k,$this->sysFields)){
          $S[$k]=$v;
        }else{
          $E[$k]=$v;
        }
      }
      $S['extra']=serialize($E);
      $arr=$S;
    }
    return $arr;
  }
  function complete(){
    if($this->appid_once){
      $this->appid_once=false;
      $this->set('noappid',$this->appid_before);
      $this->appid_before=false;
    }
    return $this;
  }
  function parse($item){
    $extra=unserialize($item['extra']);
    if($extra && is_array($extra)){
      foreach($extra as $k=>$v){
        $item[$k]=$v;
      }
    }
    unset($item['extra']);
    
    $alias=$this->get('alias');
    if($alias){
      foreach($alias as $k=>$v){
        if(isset($item[$v])){
          $item[$k]=$item[$v];
          unset($item[$v]);
        }
      }
    }
    foreach(array('key','index','money') as $key){
      for($i=1;$i<=12;$i++){
        //unset($item[$key.$i]);
      }
    }
    return $item;
  }
  function page($xxx='',$totalput=false,$maxperpage=20){
    $namespace=$this->get('namespace');
    $tablename=$this->get('tablename');
    $sql="select * from onez_".$this->data_table." where 1";
    $xxx=$this->xxx($xxx,'where');
    $xxx && $sql.=" and $xxx";
    $record=$this->db->page($sql,$totalput,$maxperpage);
    foreach($record[0] as $k=>$v){
      $record[0][$k]=$this->parse($v);
    }
    $this->complete();
    return $record;
  }
  function insert($arr){
    $arr=$this->xxx($arr,'insert');
    $this->complete();
    return $this->db->insert($this->data_table,$arr);
  }
  function update($arr,$xxx){
    $xxx=$this->xxx($xxx,'where');
    
    $T=$this->record($xxx,false);
    foreach($T as $rs){
      $myarr=array_merge($rs,$arr);
      $myarr=$this->xxx($myarr,'update');
      $this->db->update($this->data_table,$myarr,"id='$rs[id]'");
    }
    $this->complete();
  }
  function one($xxx){
    $xxx=$this->xxx($xxx,'where');
    $one=$this->db->one($this->data_table,'*',$xxx);
    $one=$this->parse($one);
    $this->complete();
    return $one;
  }
  function delete($xxx){
    $xxx=$this->xxx($xxx,'where');
    $one=$this->db->one($this->data_table,'*',$xxx);
    
    if($one){
      $record=$this->db->record($this->data_table,'*',"upid='$one[id]'");
      foreach($record as $rs){
        $this->delete("id='$rs[id]'");
      }
    }
    $this->complete();
    return $this->db->delete($this->data_table,$xxx);
  }
  function record($xxx,$complete=true){
    $xxx=$this->xxx($xxx,'where');
    $record=$this->db->record($this->data_table,'*',$xxx);
    foreach($record as $k=>$v){
      $record[$k]=$this->parse($v);
    }
    if($complete){
      $this->complete();
    }
    return $record;
  }
  function rows($xxx){
    $xxx=$this->xxx($xxx,'where');
    $this->complete();
    return $this->db->rows($this->data_table,$xxx);
  }
  function query($sql){
    $sql=$this->xxx($sql,'sql');
    $this->complete();
    return $this->db->query($sql);
  }
  function proxy(){
    $arguments=func_get_args();
    $name=$arguments[0];
    $arguments[0]=$this->data_table;
    $tablename=$this->get('tablename');
    if($name=='select' || $name=='record' || $name=='one'){
      $arguments[2]=$this->xxx($arguments[2],'where');
    }elseif($name=='rows' || $name=='delete'){
      $arguments[1]=$this->xxx($arguments[1],'where');
    }elseif($name=='insert'){
      $arguments[1]=$this->xxx($arguments[1],'insert');
    }elseif($name=='update'){
      $arguments[2]=$this->xxx($arguments[2],'update');
    }
    return call_user_func_array(array($this->db, $name), $arguments);
  }
  function copy($token_src,$token_to,$upid_src=0,$upid_to=0){
    if($token_src==$token_to){
      onez()->error('源与目标不能相同');
    }
    $xxx='';
    $T=onez('db')->db()->record($this->data_table,'*',"namespace='site.$token_src' and (upid='$upid_src' or key12 like '$upid_src-%')");
    foreach($T as $rs){
      $oldid=$rs['id'];
      unset($rs['id']);
      $rs['namespace']="site.$token_to";
      $rs['upid']=$upid_to;
      $rs['key12']=preg_replace("/^$rs[id]\-(.+?)$/i","$upid_to-$1",$rs['key12']);
      $newid=onez('db')->db()->insert($rs);
      $this->copy($token_src,$token_to,$oldid,$newid);
    }
  }
  function copytable($token_src,$token_to,$table_src,$table_to,$upid_src=0,$upid_to=0){
    $xxx='';
    $T=onez('db')->db()->record($this->data_table,'*',"namespace='site.$token_src' and tablename='$table_src' and (upid='$upid_src' or key12 like '$upid_src-%')");
    foreach($T as $rs){
      $oldid=$rs['id'];
      unset($rs['id']);
      $rs['namespace']="site.$token_to";
      $rs['tablename']=$table_to;
      $rs['upid']=$upid_to;
      $rs['key12']=preg_replace("/^$rs[id]\-(.+?)$/i","$upid_to-$1",$rs['key12']);
      $newid=onez('db')->db()->insert($rs);
      $this->copytable($token_src,$token_to,$table_src,$table_to,$oldid,$newid);
    }
  }
}