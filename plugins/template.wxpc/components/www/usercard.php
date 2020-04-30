<?php

/* ========================================================================
 * $Id: usercard.php 2237 2020-04-29 22:18:53Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$json['html']=<<<ONEZ
<div class="usercard">
  <div class="usercard-box">
    <div class="usercard-info">
      <!-- 头像开始 -->
      <img :src="data.avatar" class="item-avatar" />
      <!-- 头像结束 -->
      <h3>{{data.nickname}} <span v-html="data.sex" class="sex"></span></h3>
      <p class="summary" v-html="data.summary"></p>
      <div class="line"></div>
      <div class="info" v-if="data.infos" v-for="item in data.infos">
        <span class="name">{{item.name}}</span>
        <span class="value">{{item.value}}</span>
      </div>
      <div class="btns" v-if="data.btns">
        <template v-for="item in data.btns">
          <button type="button" :class="item.classname" v-if="item.type=='button'" :data-token="item.token">{{item.name}}</button>
          <span class="mybtn" :class="item.icon" v-else :title="item.name" :data-token="item.token"></span>
        </template>
      </div>
    </div>
  </div>
</div>
ONEZ;
$json['less']=<<<ONEZ
.usercard{
  .usercard-box{
    .usercard-info{
      margin:30px;
      position: relative;
      h3{
        font-weight:12px;
        margin-bottom:10px;
      }
      .sex{
        svg{
          width:20px!important;
          height:20px!important;
          position: relative;
          top:3px;
        }
      }
      .summary{
        font-size:12px;
        color:#999;
      }
      .item-avatar{
        position: absolute;
        right:0;
        top:0;
        border-radius:2px;
        width:60px;
        height:60px;
        background:#999;
        cursor:pointer;
      }
      line-height:2;
      .line{
        border-bottom:1px solid #f4f4f4;
        margin:30px 0 10px 0;
      }
      .info{
        font-size:12px;
        .name{
          display:inline-block;
          width:60px;
          color:#999999;
          font-weight:normal;
          text-align:left;
        }
        .value{
          display:inline-block;
          color:#000;
        }
      }
      .btns{
        text-align:right;
        margin-top:20px;
        .mybtn{
          font-size:25px;
          margin:5px 0 0 20px;
          color:#999;
          cursor:pointer;
        }
      }
    }
  }
}
ONEZ;
