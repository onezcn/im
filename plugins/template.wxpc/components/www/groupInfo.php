<?php

/* ========================================================================
 * $Id: groupInfo.php 1829 2020-04-30 20:04:57Z onez $
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
  <div class="users">
    <div class="item-user" :data-token="data.addToken">
      <div class="item-add">
        <i class="layui-icon layui-icon-addition"></i>
      </div>
      <p class="item-name">添加</p>
    </div>
    <template v-for="item in data.users">
      <div class="item-user" :data-token="item.token" :title="item.name">
        <img :src="item.avatar" class="item-avatar" />
        <p class="item-name" v-html="item.name"></p>
      </div>
    </template>
  </div>
  <div class="btns">
    <div class="line"></div>
    <button type="button" class="btn btn-quit" data-token="ajax" data-confirm="您确定要退出当前群组吗？" data-type="quit" data-groupid="data.groupid">删除并退出</button>
  </div>
</template>
ONEZ;
$json['less']=<<<ONEZ
.comp-groupInfo{
  .users{
    padding:15px;
  }
  .item-user{
    display:inline-block;
    text-align:center;
    .item-add{
      width:48px;
      height:48px;
      background:#fefefe;
      border:2px solid #ddd;
      cursor:pointer;
      .layui-icon{
        font-size: 32px;
        margin-top: 5px;
        color:#ddd;
        display: inline-block;
      }
    }
    .item-avatar{
      border-radius:2px;
      width:48px;
      height:48px;
      background:#fefefe;
      cursor:pointer;
    }
    .item-name{
      height:32px;
      line-height:32px;
      overflow:hidden;
      
      width:52px;
      white-space:nowrap;
      text-overflow:ellipsis;
      overflow:hidden;
      word-break:break-all;
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
ONEZ;
