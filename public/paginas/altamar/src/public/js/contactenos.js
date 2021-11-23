Contacto = {
  submit:function(){
    var dr = $('#contacto').cmGetRecord();
    rpc.call("ContactenosData.enviar",dr)
    .then(function(res,ex){
      if(ex){
        $.cmDialogError(ex);
        return;
      }
      $.cmDialogInfo({
        message:res.message,
        callback:function(){
          var url = window.location.href;
          window.location = url;
        }
      });
    });
  }
}
