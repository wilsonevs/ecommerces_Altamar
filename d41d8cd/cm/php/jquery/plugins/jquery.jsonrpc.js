function JSonRpc(url,op) {
    this.rpcUrl = url;
    this.options = {async:false};
    this.rawResponse =null;
    this.response = null;
    this.method='';

    if(typeof(op)!="undefined"){
    	if( op.sync==true ){
    		this.options.async = true;
    	}
    }

    return;
}

JSonRpc.prototype.callSync = function(method,params){
	this.setSyncMode();
	return this.execMethod(method,params);
}

JSonRpc.prototype.callAsyc = function(method,params,callback){
	this.setASyncMode();
	return this.execMethod(method,params,callback);
}

JSonRpc.prototype.execMethod = function(method,params,callback){
    if( typeof(params)=="undefined"){
        params={};
    }

    callback=typeof(callback)!="undefined"?callback:null;
    this.method = method;

    p = $.json.encode( {
        "method":method,
        "params":params
    } );

    //JConsole.log(this.options);

    if( this.options["async"]==true ){
        $.post(this.rpcUrl,p,callback,"json");
        return true;
    }
    else {
        this.rawResponse = $.ajax({
            url: this.rpcUrl,
            type: "POST",
            data: p,
            async: false
        }).responseText;


        if( this.rawResponse==''){
            this.response = {};
            this.response.error = {code:-1,message:'Empty response'};
            return false;
        }

        try {
            this.response = {};
            this.response = $.json.decode( this.rawResponse );
        }
        catch(e){
            this.response = { error:{code:-1,message:('RPC: Failed decoding response\n'+e.message+'\n'+this.rawResponse)} };
            return false;
        }

        if( this.response.error != null ){

            if( this.response.error.code == 1){
                $.JUtils.redirect(loginUrl+"?expired=true");
                //$.JUtils.critical(this.response.error.message);
            }
            return false;
        }

        return true;
    }
}

JSonRpc.prototype.setOption = function(k,v){
    this.options[k]=v;
    return;
}

JSonRpc.prototype.setSyncMode = function(){
    this.setOption("async",false);
    return;
}

JSonRpc.prototype.setAsyncMode = function(){
    this.setOption("async",true);
    return;
}

JSonRpc.prototype.rawResponse = function(){
    return this.rawResponse;
}

JSonRpc.prototype.isError = function(){


    //alert( $.json.encode( this.response ) );
    //alert( this.error() );
    
    if( this.error() == null){
        return false;
    }
    return true;
}

JSonRpc.prototype.error = function(){
    if( this.response==null){
        return {code:-1,message:"rpcClient::error: Invalid server response"};
    }
    return this.response.error;
}

JSonRpc.prototype.result = function(){
    if( this.response == null ){
        return {code:-1,message:"rpcClient::result: Invalid server response"};
    }
    return this.response.result;
}
