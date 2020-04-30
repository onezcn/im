$(function(){
  $('.image-seticon').each(function(){
    var _this_=$(this);
    var data=$(this).data();
    var box=$('<div />').appendTo($(this));
    _this_.upload=function(files){
      _this_.addClass('uploading');
    	var fd = new FormData();
    	fd.append('Filedata', files[0]);
    	var xhr = new XMLHttpRequest();
    	xhr.addEventListener("load", function(e){
        _this_.removeClass('uploading');
        var s=e.target.responseText;
        try{
          eval('var o='+s+';');
          if(typeof o.url=='undefined'){
            if(typeof o.error!='undefined'){
              onez.alert(o.error);
            }
            return;
          }
          input.val(o.url);
          _this_.css({
            backgroundImage:'url('+o.url+')'
          }).addClass('image-seticon-has');
        }catch(e2){
        }
      }, false);
    	xhr.open("POST", data.server);
    	xhr.send(fd);
    };
    var input=$('<input type="hidden" name="'+data.name+'" value="'+data.value+'" />').appendTo($(this));
    _this_.bind('drop',function(event){
      _this_.removeClass('dragover');
      _this_.upload(event.dataTransfer.files);
    });
    _this_.bind('dragover',function(){
      _this_.addClass('dragover');
    });
    _this_.bind('dragleave',function(){
      _this_.removeClass('dragover');
    });
    $(this).css({
      width:data.size+'px',
      height:data.size+'px'
    });
    if(data.value!=''){
      $(this).css({
        backgroundImage:'url('+data.value+')'
      }).addClass('image-seticon-has');
    }
    $('<i />').bind('click',function(){
      if(confirm('您确定要删除这张图片吗？')){
        _this_.css({
          backgroundImage:'none'
        }).removeClass('image-seticon-has');
        input.val('');
      }
    }).appendTo($(this));
    var file=$('<input type="file" />').appendTo(box);
    file.get(0).onchange = function() {
      _this_.upload(this.files);
    };
  });
});