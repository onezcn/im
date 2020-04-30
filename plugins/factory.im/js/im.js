var onez=onez||{};
onez.strcode=function(str,action,key){
  var arr=[],strlen=0;
  if(action == 'ENCODE'){
    str = Base64.encode(str);
    strlen = str.length;
  }else{
    str = Base64.decode(str);
    for (var i=0;i<str.length;i+=2) {
      arr.push(str.substr(i,2));
    }
    strlen = arr.length;
  }
  var code = '';
  var keylen = key.length;
  for (var i=0;i<strlen;i++) {
    var k		= i % keylen;
    if(action=='ENCODE'){
      var c=(str.charCodeAt(i) ^ key.charCodeAt(k)).toString(16);
      if(c.length<2){
        c='0'+c;
      }
      code+=c;
    }else{
      code  += String.fromCharCode(parseInt(arr[i],16) ^ key.charCodeAt(k));
    }
  }
  return (action=='DECODE' ? Base64.decode(code) : Base64.encode(code));
};
onez.IM=function(option){
  var im=this;
  im.websocket=function(loginData){
    var that=this;
    that.autoConnected=true;
    if(!im.option.server){
      throw('[factory.im]提示: 未设置im服务器');
    }
    var ws = new ReconnectingWebSocket(im.option.server,[],{
      debug:true,
      timeoutInterval:10000,
    });
    ws.onopen = function(evt) {
      $(document).trigger('onez-websocket-message',{
        action:'open'
      });
    };
     
    ws.onmessage = function(evt) {
      try{
        var json=JSON.parse(evt.data);
        console.log(json);
        if(typeof json.action!='undefined'){
          var action=json.action;
          if(action=='welcome'){
            im.tmpkey=json.tmpkey;
            console.log(loginData);
            var hash=onez.strcode(json.hash,'DECODE',im.tmpkey);
            if(hash=='www.onez.cn'){
              loginData.hash=onez.strcode('佳蓝','ENCODE',im.tmpkey);
            }
            loginData.action='login';
            ws.send(JSON.stringify(loginData));
          }else if(action=='login success'){
            im.connected=true;
            im.$emit('status',{status:'open'});
          }else{
            if(json.eMsg){
              json=JSON.parse(onez.strcode(json.eMsg,'DECODE',im.tmpkey));
            }
            if(json.action=='callback'){
              if(im.callList[json.callId]){
                im.callList[json.callId]=json;
              }
              return;
            }else if(json.action=='$emit'){
              im.$emit(json.event,json.data);
              return;
            }
            im.$emit('message',json);
            //console.log(json);
            //$(document).trigger('onez-websocket-message',json);
          }
        }
      }catch(e){
        console.log('catch',evt.data);
      }
    };
     
    ws.onclose = function(evt) {
      im.$emit('status',{status:'close'});
      im.connected=false;
      if(that.autoConnected){
        setTimeout(function(){
          that.connect();
        },1000);
      }
      $(document).trigger('onez-websocket-message',{
        action:'close',
        event:evt
      });
    };
    ws.onerror = function(evt) {
      im.$emit('status',{status:'close'});
      im.connected=false;
      if(that.autoConnected){
        setTimeout(function(){
          that.connect();
        },1000);
      }
      $(document).trigger('onez-websocket-message',{
        action:'error',
        event:evt
      });
    };
    this.quene=[];
    this.quene_busy=false;
    this.ws=ws;
    this.quene_run=function(){
      if(that.quene.length<1){
        that.quene_busy=false;
        return;
      }
      that.quene_busy=true;
      var msg=that.quene.shift();
      that.ws.send(msg);
      setTimeout(function(){
        that.quene_busy=false;
        that.quene_run();
      },100);
    };
    this.send=function(msg){
      that.quene.push(JSON.stringify(msg));
      if(!that.quene_busy){
        that.quene_run();
      }
    };
    this.close=function(){
      if(im.connected){
        ws.close();
      }
    };
    this.connect=function(){
      if(!im.connected){
        
      }
    };
  };
  //等待连接成功
  im.waitConnected=function(){
    return new Promise((resolve, reject) => {
      if(im.connected){
        resolve();
      }else{
        function _wait(){
          if(im.connected){
            resolve();
          }else{
            setTimeout(_wait,1000);
          }
        }
        _wait();
      }
    });
  };
  //云函数返回结果
  im.callBack=function(callId){
    return new Promise((resolve, reject) => {
      var sec=0;
      function _wait(){
        if(im.callList[callId]){
          if(im.callList[callId].status=='fail'){
            var result=im.callList[callId].result;
            delete im.callList[callId];
            resolve([false,result]);
            return;
          }else if(im.callList[callId].status=='success'){
            var result=im.callList[callId].result;
            delete im.callList[callId];
            resolve([true,result]);
            return;
          }
        }else{
          resolve([false,'函数基本信息丢失']);
          return;
        }
        sec++;
        if(sec>=10){
          if(im.callList[callId]){
            delete im.callList[callId];
          }
          resolve([false,'处理超时']);
          return;
        }
        setTimeout(_wait,1000);
      }
      _wait();
    });
  };
  im.playSound=function(url){
    var url=url||'msg';
    if(url=='msg'){
      url=im.option.resurl+'/sounds/msg.mp3';
    }
    $('#audio').remove();
    $('<audio src="'+url+'" autoplay="autoplay" id="audio" />').appendTo('body');
  };
  //云函数
  im.callList={};
  im.call=async function(method,req){
    await im.waitConnected();
    var result=[false,'未处理'];
    try{
      var callId='call'+Math.random();
      onez.websocket.send({
        action:'call',
        callId:callId,
        method:method,
        req:req,
      });
      im.callList[callId]={status:'wait'};
      result =await im.callBack(callId);
    }catch(e){
      result=[false,e];
    }
    return result;
  };
  im.autoClose=function(){
    onez.websocket.ws.close();
  };
  im.$call=function(eventName,req,success,fail){
    var req=req||{};
    if(typeof req=='function'){
      fail=success;
      success=req;
      req={};
    }
    if(req.success){
      success=req.success;
      delete req.success;
    }
    if(req.fail){
      fail=req.fail;
      delete req.fail;
    }
    im.call(eventName,req).then(function(r){
      if(r[0]){
        if(success){
          success(r[1]);
        }
      }else{
        if(fail){
          fail(r[1]);
        }
      }
    });
  };
  im.events={};
  im.$on=function(eventName,callback){
    var eventId=Math.random()+'';
    im.events[eventName]=im.events[eventName]||{};
    im.events[eventName][eventId]=callback;
    return eventId;
  };
  im.$off=function(eventName,eventId){
    if(im.events[eventName]){
      if(eventId){
        if(im.events[eventName][eventId]){
          delete im.events[eventName][eventId];
        }
      }else{
        delete im.events[eventName];
      }
    }
  };
  im.$emit=function(eventName,data){
    if(im.events[eventName]){
      for(var eventId in im.events[eventName]){
        im.events[eventName][eventId](data);
      }
    }
  };
  
  if(!option.userid){
    console.warn('[factory.im]提示: 请先登录');
    return;
  }
  im.option=option;
  onez.websocket=new im.websocket({
    udid:option.udid,
    userid:option.userid,
    siteId:option.siteId
  });
};
window.onunload = function() {
  onez.im.autoClose();
};
window.onbeforeunload = function () {
  onez.im.autoClose();
};