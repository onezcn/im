<?php

/* ========================================================================
 * $Id: menu.inc.php 2981 2017-10-11 17:14:46Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
!$Menu && $Menu=array();
$Menu[]=array(
  'name'=>'系统首页',
  'href'=>'/onez/index.php',
);
//if(strpos(ONEZ_ROOT,'/git/onezblue')===false){
  
  $Menu[]=array(
    'name'=>'云服务',
    'href'=>'',
  );
  $Menu[]=array(
    'name'=>'一键更新',
    'href'=>'/onez/cloud/upgrade.php',
    'icon'=>'fa fa-cloud-download',
  );
  $Menu[]=array(
    'name'=>'注册站点',
    'href'=>'/onez/cloud/profile.php',
    'icon'=>'fa fa-globe',
  );
  0 && $Menu[]=array(
    'name'=>'云短信',
    'href'=>'/onez/cloud/sms.php',
    'icon'=>'fa fa-envelope-o',
  );
  0 && $Menu[]=array(
    'name'=>'云服务诊断',
    'href'=>'/onez/cloud/diagnose.php',
    'icon'=>'fa fa-user-md',
  );

  if($G['platform']!='discuz'){
    $Menu[]=array(
      'name'=>'快速安装',
      'href'=>'/onez/cloud/quick.php',
      'icon'=>'fa fa-bolt',
    );
    0 && $Menu[]=array(
      'name'=>'应用商城',
      'url'=>'http://www.onezphp.com/?mod=/market/shop.php&siteid='.onez()->myoption('onez_siteid'),
      'icon'=>'fa fa-print',
      'classname'=>'onez-miniwin',
    );
    0 && $Menu[]=array(
      'name'=>'充值',
      'url'=>'http://www.onezphp.com/admin.php'.onez()->href('/member/wallet/charge.php'),
      'icon'=>'fa fa-credit-card',
      'classname'=>'onez-miniwin',
    );
  }
//}
$Menu[]=array(
  'name'=>'扩展',
  'href'=>'',
);
$Menu[]=array(
  'name'=>'应用中心',
  'href'=>'/onez/extension/store.php',
  'icon'=>'fa fa-cubes',
);
$Menu[]=array(
  'name'=>'自定义应用',
  'href'=>'/onez/extension/store.my.php',
  'icon'=>'fa fa-support',
);
$Menu[]=array(
  'name'=>'系统插件扩展',
  'href'=>'/onez/extension/plugins.php',
  'icon'=>'fa fa-trophy',
);
$Menu[]=array(
  'name'=>'全局设置',
  'href'=>'/onez/extension/store.setting.php',
  'icon'=>'fa fa-inbox',
);
if(strpos(ONEZ_ROOT,'/git/onezblue')!==false){
$Menu[]=array(
  'name'=>'联盟',
  'href'=>'',
);
$Menu[]=array(
  'name'=>'产品寄售',
  'href'=>'/onez/union/sell.php',
  'icon'=>'fa fa-credit-card',
);
}
/*
$Menu[]=array(
  'name'=>'系统管理',
  'href'=>'',
);
0 && $Menu[]=array(
  'name'=>'更新缓存',
  'href'=>'/onez/system/updatecache.php',
  'icon'=>'fa fa-refresh',
);
$Menu[]=array(
  'name'=>'站点设置',
  'href'=>'/onez/system/site.php',
  'icon'=>'fa fa-inbox',
);
0 && $Menu[]=array(
  'name'=>'附件设置',
  'href'=>'/onez/system/attachment.php',
  'icon'=>'fa fa-folder-open',
);
0 && $Menu[]=array(
  'name'=>'其他设置',
  'href'=>'/onez/system/common.php',
  'icon'=>'fa fa-gear',
);
$Menu[]=array(
  'name'=>'查看日志',
  'href'=>'/onez/system/logs.php',
  'icon'=>'fa fa-book',
);
$Menu[]=array(
  'name'=>'系统工具',
  'href'=>'',
);
$Menu[]=array(
  'name'=>'数据库',
  'href'=>'/onez/system/database.php',
  'icon'=>'fa fa-database',
);
$Menu[]=array(
  'name'=>'木马查杀',
  'href'=>'/onez/system/scan.php',
  'icon'=>'fa fa-legal',
);
*/
return $Menu;
