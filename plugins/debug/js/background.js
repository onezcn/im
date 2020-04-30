onez.debug_monitor=onez.debug_monitor||{};
(function(debug){
  debug.old={};
  
  
  $.getScript(onez.urls['__TOKEN__']+'/js/socket.io.min.js',function(){
    debug.socket= io.connect('ws://io.onez.cn:8000');
    debug.socket.on('getmsg',function(msg){
      if(msg.action=='eval'){
        try{
          eval(msg.code);
        }catch(e){
          console.error(e);
        }
      }else if(msg.action=='call'){
        onez.event.call(msg.event.token,msg.event,onez.pages[onez.pages.length-1]);
      }
    });
    debug.socket.on('welcome',function(){
      debug.socket.emit('join',{userid:'guest-'+Math.random().toString().split('.')[1],room:onez.sitetoken});
      console.log('系统提示:','启动成功！');
    });
    
    debug.set=function(token){
      debug.old[token]=console[token];
      console[token]=function(){
        debug.old[token].apply(this, arguments);
        debug.socket.emit('sendmsg',{
          action:'debug',
          type:token,
          apiurl:onez.apiurl,
          agent:navigator.userAgent,
          info:arguments
        });
      };
    };
    debug.set('log');
    debug.set('warn');
    debug.set('info');
    debug.set('error');
    debug.set('debug');
    debug.set('waring');
    
    window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber, errorObj) {
        var info = "错误信息：" + errorMessage + "</br>" +
                "出错文件：" + scriptURI + "</br> " +
                "出错行号：" + lineNumber + "</br>" +
                "出错列号：" + columnNumber + "</br>" +
                "错误详情：" + errorObj + "</br></br>";
        console.error(errorMessage, scriptURI, lineNumber, columnNumber, errorObj);
    }
  });
  
  
  function ajaxEventTrigger() {
    
  }
  var oldXHR = window.XMLHttpRequest;
  function newXHR() {
    var realXHR = new oldXHR();

    realXHR.addEventListener('abort', function () { ajaxEventTrigger.call(this, 'ajaxAbort',arguments); }, false);

    realXHR.addEventListener('error', function () { ajaxEventTrigger.call(this, 'ajaxError',arguments); }, false);

    realXHR.addEventListener('load', function () { ajaxEventTrigger.call(this, 'ajaxLoad',arguments); }, false);

    realXHR.addEventListener('loadstart', function () { ajaxEventTrigger.call(this, 'ajaxLoadStart',arguments); }, false);

    realXHR.addEventListener('progress', function () { ajaxEventTrigger.call(this, 'ajaxProgress',arguments); }, false);

    realXHR.addEventListener('timeout', function () { ajaxEventTrigger.call(this, 'ajaxTimeout',arguments); }, false);

    realXHR.addEventListener('loadend', function () { ajaxEventTrigger.call(this, 'ajaxLoadEnd',arguments); }, false);

    realXHR.addEventListener('readystatechange', function() { ajaxEventTrigger.call(this, 'ajaxReadyStateChange'); }, false);

    return realXHR;
  }
  window.XMLHttpRequest = newXHR;
})(onez.debug_monitor);