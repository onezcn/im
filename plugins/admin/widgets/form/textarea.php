<?php

/* ========================================================================
 * $Id: textarea.php 1410 2017-06-28 10:10:16Z onez $
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
  if($arr['after']){
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
    <div class="input-group">
          <textarea type="'.$arr['type'].'" class="form-control" rows="5" id="input-'.$arr['key'].'"'.($arr['height']?' style="height:'.$arr['height'].'px"':'').' name="'.$arr['key'].'" placeholder="'.$arr['hint'].'">'.$value.'</textarea>
          <span class="input-group-btn">'.$arr['after'].'</span>
    </div>
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }else{
    $this->html.='<div class="form-group '.$arr['group'].'">
          <label for="input-'.$arr['key'].'"'.($this->get('dir')=='horizontal'?' class="col-sm-2 control-label"':'').'>'.$arr['label'].'</label>
          '.($this->get('dir')=='horizontal'?'<div class="col-sm-10">':'').'
          <textarea type="'.$arr['type'].'" class="form-control" rows="5" id="input-'.$arr['key'].'"'.($arr['height']?' style="height:'.$arr['height'].'px"':'').' name="'.$arr['key'].'" placeholder="'.$arr['hint'].'">'.$value.'</textarea>
          '.($this->get('dir')=='horizontal'?'</div>':'').'
        </div>';
  }
}