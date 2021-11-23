function CmJsonRpc(url){
	this.__url = url;

	this.call = function(method,params){

		this.__req = $.ajax({
			method: "POST",
			url: this.__url,
			data: JSON.stringify({
				method:method,
				params:params
			})
		});

		return this;
	}

	this.then = function(callback){

		this.__req
		.done(function(data,textStatus,jqXHR){

			if( data.error ){
				callback(null,data.error);
				return;
			}

			callback(data.result,null);
		}.bind(this))

		return this;
	}
}


window.rpc = {
	call:function(method,params){
		var tmp=new CmJsonRpc(window.rpcUrl);
		return tmp.call(method,params);
	}
};


(function($){

	$.fn.cm = $.fn.cm || {};

	$.fn.cmPopulate=function(options){
		var html = '';
		for(var i=0;i<options.length;i++){
			html+='<option value="'+options[i].data+'">'+options[i].label+'</option>';
		}
		this.html(html);
	}

	$.fn.cmGetValue=function(){
		return $(this).val();
	}

	$.fn.cmGetRecord=function(){
		var r={};
		var selector=[
			"input[name]",
			"textarea[name]",
			"select[name]"
		];

		this.find(selector.join(",")).each(function(){
			var name=$(this).attr("name");
			var type=$(this).attr("type");

			if( r[name]===undefined ){
				r[name]=null;
			}



			if( type=="radio" || type=="checkbox" ){
				if( !$(this).prop("checked") ){
					return;
				}
			}


			if( $(this).val()===null ){
				return;
			}

			if( r[name]!== null ){
				r[name] = [ r[name] ];
				r[name].push( $(this).val() );
				return;
			}

			r[name]=$(this).val();
		});

		return r;
	};

	/*
	$.fn.cmFormPost=function(url,method,callback){
		var p=this.getRecord();

		$.post(url,{method:method,params:p},null,"json")
		.done(function(data){
			callback(data.result,data.error);
		});
	};
	*/

	$.fn.cmDialog=function(message,title,callback){
		//var d=$('#dialog-information');
		var d=$(this);

		if(title){
			d.find("#titulo-modal").html(title);
		}

		if(message){
			d.find(".box-message").html(message);
		}

		d.find(".button-ok").click(function(){

			d.foundation('close');

			if(callback){
				callback();
			}

		});

		d.foundation('open');
	},


	$.cmDialogInfo=function(opts){
		opts = $.extend({
			selector:"#modal-info",
			title:"",
			message:"",
			callback:function(){}
		},opts);

		var dlg=$(opts.selector);

		dlg.find(".title").html(opts.title);
		dlg.find(".message").html(opts.message);


		dlg.find(".button-ok").click(function(){
			dlg.foundation('close');
			opts.callback();
		});

		dlg.foundation('open');
	},

	$.cmDialogError=function(opts){
		opts = $.extend({
			selector:"#modal-error",
			title:"",
			message:"",
			callback:function(){}
		},opts);

		var dlg=$(opts.selector);

		dlg.find(".title").html(opts.title);
		dlg.find(".message").html(opts.message);


		dlg.find(".button-ok").click(function(){
			dlg.foundation('close');
			opts.callback();
		});

		dlg.foundation('open');
	}

}(jQuery));
