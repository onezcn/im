<?php

/* ========================================================================
 * $Id: file.php 902 2020-04-13 03:21:48Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
if($MODE=='form'){
  $_html='<div style="width:100px;height:100px;position: relative;border:1px solid #bbb;color:#999"><div style="width: 100px;height: 100px;line-height: 100px;text-align:center" id="input-'.$key.'-label">点击上传</div><input type="file" style="width: 100px;height: 100px;opacity: 0;position: absolute;left:0;top:0;cursor:pointer" class="form-control" id="input-'.$key.'" name="'.$key.'" placeholder="'.$item['hint'].'" autocomplete="off" value="'.$item['value'].'"></div>';
  $_html.=<<<ONEZ
<script type="text/javascript">
$('#input-$key').bind('change',function(e){
  if($(this).get(0).files.length>0){
    $('#input-$key-label').html('已选择').css({color:'green'}).tooltip($(this).get(0).files[0].name);
  }
});
</script>
ONEZ;
  return $_html;
}elseif($MODE=='show'){
  return $item['value'];
}elseif($MODE=='post'){
  
}