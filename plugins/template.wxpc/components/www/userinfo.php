<?php

/* ========================================================================
 * $Id: userinfo.php 3759 2020-04-30 11:43:17Z onez $
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
<template v-if="data.type=='group'">
<div class="groupinfo">
  <div class="users" v-if="data.users">
    <template v-for="item in data.users">
      <div class="item-user" :data-token="item.token">
        <img :src="item.avatar" class="item-avatar" />
        <p class="item-name" v-html="item.name"></p>
      </div>
    </template>
  </div>
  <div class="btns">
    <div class="line"></div>
    <button type="button" class="btn" :data-token="data.sendToken">发消息</button>
  </div>
</div>
</template>
<template v-else-if="data.type=='user'">
<div class="userinfo">
  <div class="userinfo-box">
    <div class="userinfo-info">
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
      <div class="line line2"></div>
      <div class="btns" v-if="data.btns">
        <template v-for="item in data.btns">
          <button type="button" :class="item.classname" v-if="item.type=='button'" :data-token="item.token">{{item.name}}</button>
          <span class="mybtn" :class="item.icon" v-else :title="item.name" :data-token="item.token"></span>
        </template>
      </div>
    </div>
  </div>
</div>
</template>
ONEZ;
$json['less']=<<<ONEZ
.comp-userinfo{
  position: absolute;
  left:0;
  top:0;
  width:100%;
  height:100%;
}
.groupinfo{
  .users{
    
    position: absolute;
    left:30px;
    top:60px;
    bottom:90px;
    width:auto;
    overflow:hidden;
    overflow-y:auto;
  }
  .item-user{
    display:inline-block;
    text-align:center;
    .item-avatar{
      border-radius:2px;
      width:80px;
      height:80px;
      background:#fefefe;
      cursor:pointer;
    }
    .item-name{
      height:32px;
      line-height:32px;
      overflow:hidden;
    }
  }
  .line{
    border-bottom:1px solid #eee;
    height:10px;
    margin:30px 0 10px 0;
  }
  .btns{
    position: absolute;
    left:0;
    bottom:30px;
    width:100%;
    text-align:center;
    .btn{
      padding:6px 30px;
      
    }
  }
}
.userinfo{
  .userinfo-box{
    margin:50px 50px;
    padding-top:30px;
    .userinfo-info{
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
        width:80px;
        height:80px;
        background:#fefefe;
        cursor:pointer;
      }
      line-height:2;
      .line{
        border-bottom:1px solid #eee;
        height:10px;
        margin:30px 0 10px 0;
      }
      .line2{
        margin:0px 0 10px 0;
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
        text-align:center;
        margin-top:20px;
        .btn{
          font-size:16px;
          margin:5px 0 0 20px;
          color:#fff;
          cursor:pointer;
          padding:10px 40px;
        }
      }
    }
  }
}
ONEZ;
