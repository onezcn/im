<?php

/* ========================================================================
 * $Id: checkbox.php 1221 2019-05-28 15:43:58Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
if($TYPE=='code'){
  if(gettype($arr['options'])=='array'){
    if(!is_array($value)){
      $value=explode("\t",trim($value,"\t ,"));
    }
    $select='';
    $select.='<div class="form-group '.$arr['group'].'">';
      $select.='<label>'.$arr['label'].'</label>';
      $select.='<div class="row">';
      foreach($arr['options'] as $k=>$v){
        $select.='<div class="col-md-'.($arr['col']?$arr['col']:3).'">';
          $s=in_array($k,$value)?' checked':'';
          $select.='<input type="checkbox" id="input-'.$arr['key'].'-'.$k.'" name="'.$arr['key'].'[]" '.$s.' value="'.$k.'" style="position: relative;top: 2px;margin-right:2px"><span></span>';
          $select.='<label for="input-'.$arr['key'].'-'.$k.'" style="font-weight:normal">'.$v.'</label> &nbsp;';
        $select.='</div>';
      }
      $select.='</div>';
    $select.='</div>';
    $this->html.=$select;
  }else{
    $this->html.='<div class="form-group '.$arr['group'].'">
        <label>
          <input type="checkbox" id="input-'.$arr['key'].'" name="'.$arr['key'].'" '.($value?' checked':'').' value="1"><span></span>
          '.$arr['label'].'
        </label>
      </div>';
  }
  
}