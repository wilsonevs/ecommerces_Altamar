Idioma = {
  submit:function(){
    var dr = $('#idioma').cmGetRecord();
    rpc.call("IdiomaData.enviar",dr)
    .then(function(res,ex){
      if(ex){
        $.cmDialogError(ex);
        return;
      }
      var url = window.location.href;
      window.location = url;
    });
  },

  submitsmall:function(){
    var dr = $('#idiomasmall').cmGetRecord();
    dr.idioma = dr.idiomasmall;
    rpc.call("IdiomaData.enviar",dr)
    .then(function(res,ex){
      if(ex){
        $.cmDialogError(ex);
        return;
      }
      var url = window.location.href;
      window.location = url;
    });
  }
}
