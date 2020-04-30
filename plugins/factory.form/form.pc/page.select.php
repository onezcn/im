<?php

/* ========================================================================
 * $Id: page.select.php 896 2020-04-17 07:52:24Z onez $
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
  list($text,$event)=explode('|',$item['value']);
  if(!$text){
    $text=$item['notip']?$item['notip']:'未选择';
  }
  return <<<ONEZ
<pre class="preview-$arr[key]" style="padding:10px 10px 5px 10px;margin-bottom:10px;">$text</pre>
<input type="hidden" id="input-$arr[key]" name="$arr[key]" value="$item[value]">
<script type="text/javascript">
$(document).bind('on-message',function(e,data){
  
  if(data['$arr[key]']){
    $('.preview-$arr[key]').html(data['$arr[key]'].split('|')[0]);
    $('#input-$arr[key]').val(data['$arr[key]']).trigger('blur');
  }
});
</script>
ONEZ;
}elseif($MODE=='show'){
  list($text,$event)=explode('|',$item['value']);
  if(!$text){
    $text=$item['notip']?$item['notip']:'未选择';
  }
  return $text;
}elseif($MODE=='post'){
  onez('form.file')->form_save($onez[$arr['key']],$arr);
}