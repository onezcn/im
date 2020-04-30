<?php

/* ========================================================================
 * $Id: index.php 213 2020-04-24 12:12:03Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
onez('ui')->init();
onez('ui')->header();
onez('template')->header();
?>
<div id="app" class="app">

</div>
<?php 
onez('template')->footer();
onez('ui')->footer();
