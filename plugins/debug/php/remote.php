<?php

/* ========================================================================
 * $Id: remote.php 4191 2017-02-20 18:25:06Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$G['title']='远程调试信息';
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    <?php echo $G['title']?><small></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="<?php echo onez()->href('/')?>">
        <i class="fa fa-dashboard">
        </i>
        管理首页
      </a>
    </li>
    <li class="active">
      <?php echo $G['title']?>
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <div class="pull-left">
      <textarea class="form-control" id="mycode" style="margin-bottom: 5px;width:800px;height: 160px;font-size:16px;font-family: Fixedsys,'Courier New'"></textarea>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-info" onclick="_runcode()">运行代码</button>
      <button type="button" class="btn btn-success" onclick="_reload()">重新运行</button>
      <button type="button" class="btn btn-warning" onclick="_clear()">清空</button>
    </div>
    <div class="clearfix"></div>
  </div>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        <?php echo $G['title']?>
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body  table-responsive no-padding" style="height:600px;overflow-y: auto;overflow-x: hidden;">
      <table class="table table-striped">
        <thead>
          <tr>
            <th width="160">
              时间
            </th>
            <th width="60">
              类型
            </th>
            <th>
              详情
            </th>
          </tr>
        </thead>
        <tbody class="log-body">
        </tbody>
      </table>
    </div>
  </div>
</section>
<script type="text/javascript">
function _reload(){
  onez.debug_remote.event({token:'reload'});
}
function _runcode(){
  var mycode=$('#mycode').val();
  onez.debug_remote.run(mycode);
}
function _clear(){
  $('tbody.log-body').empty();
}
onez.debug_remote=onez.debug_remote||{};
(function(debug){
  debug.now=function() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
  };
  debug.event=function(obj) {
    debug.socket.emit('sendmsg',{action:'call',event:obj});
  };
  debug.run=function(mycode) {
    debug.socket.emit('sendmsg',{action:'eval',code:mycode});
  };
  $.getScript('<?php echo $this->url.'/js/socket.io.min.js'?>',function(){
    debug.socket= io.connect('ws://io.onez.cn:8000');
    debug.socket.on('getmsg',function(msg){
      if(msg.action=='debug'){
        var args=[];
        for(var k in msg.info){
          args.push(msg.info[k]);
        }
        var f=console[msg.type];
        var typename='';
        var color='#000000';
        if(msg.type=='log'){
          typename='日志';
          color='#000000';
        }else if(msg.type=='error'){
          typename='错误';
          color='#ff0000';
        }else if(msg.type=='warn'){
          typename='警告';
          color='orange';
        }
        var tr=$('<tr />');
        $('<td />').html(debug.now()).appendTo(tr);
        $('<td />').html(typename).appendTo(tr);
        $('<td />').html('<p style="font-size:10px;color:#666">'+msg.apiurl+'</p><p style="font-size:10px;color:#666">'+msg.agent+'</p><pre class="code lang">'+JSON.stringify(msg.info)+'</pre>').appendTo(tr);
        tr.find('td').css({color:color}).end().prependTo('tbody.log-body');
        console[msg.type](args);
      }
    });
    debug.socket.on('welcome',function(){
      debug.socket.emit('join',{userid:'guest-'+Math.random().toString().split('.')[1],room:'<?php echo $ptoken?>'});
    });
  });
})(onez.debug_remote);
</script>
<?php
onez('admin')->footer();
?>