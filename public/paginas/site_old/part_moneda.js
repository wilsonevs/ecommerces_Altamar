Moneda = {
    submit:function(){
      var dr = $('#monedaform').cmGetRecord();
      rpc.call("MonedaData.enviar",dr)
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
      var dr = $('#monedaformsmall').cmGetRecord();
      dr.moneda = dr.monedasmall;
      rpc.call("MonedaData.enviar",dr)
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
  