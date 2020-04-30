<?php

/* ========================================================================
 * $Id: index.php 339 2020-04-27 14:53:24Z onez $
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
echo onez('ui')->css($this->tpl->url.'/css/app.css');
?>
<section class="content" id="content">

</section>
<?php 
onez('template')->footer();
echo onez('ui')->js($this->tpl->url.'/js/events.js');
onez('ui')->footer();
?>