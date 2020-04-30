<?php

/* ========================================================================
 * $Id: money.php 936 2020-03-28 00:06:20Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
#<input :class="item.classname" :style="item.itemstyle" @click="event" :data-token="item.token" :data-action="item.action" :data-id="item.id" :value="item.value" :type="item.type" :password="item.password" :placeholder="item.placeholder" :placeholder-style="item.placeholderStyle" :placeholder-class="item.placeholderClass" :disabled="item.disabled" :maxlength="item.maxlength" :cursor-spacing="item.cursorSpacing" :focus="item.focus" :confirm-type="item.confirmType" :confirm-hold="item.confirmHold" :cursor="item.cursor" :selection-start="item.selectionStart" :selection-end="item.selectionEnd" :adjust-position="item.adjustPosition" @input="input" @focus="focus" @blur="blur" @confirm="confirm" />
if($MODE=='form'){
  $item['type']='onez-input';
  $item['itemType']='digit';
  $item['placeholder']=$item['hint'];unset($item['hint']);
  $items[]=$item;
}elseif($MODE=='post'){
  
}