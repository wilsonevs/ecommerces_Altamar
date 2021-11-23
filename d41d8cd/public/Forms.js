(function($){

	$.fn.getRecord=function(){
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


	$.fn.cmFormPost=function(url,method,callback){
		var p=this.getRecord();

		$.post(url,{method:method,params:p},null,"json")
		.done(function(data){
			callback(data.result,data.error);
		});
	};

	$.fn.cmButtonPost=function(){
		//todo
	},

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

			d.foundation('reveal', 'close');

			if(callback){
				callback();
			}

		});

		d.foundation('reveal', 'open');
	},


	$.fn.cmDialogInformation=function(message,title,callback){
		//var d=$('#dialog-information');
		var d=$(this);
		d.find(".box-message").html(message);
		if(title){
			d.find("#titulo-modal").html(title);
		}

		d.find(".button-ok").click(function(){

			d.foundation('reveal', 'close');

			if(callback){
				callback();
			}

		});

		d.foundation('reveal', 'open');
	},

	$.fn.cmDialogError=function(message,title,callback){
		//var d=$('#dialog-error');
		var d=$(this);
		d.find(".box-message").html(message);
		if(title){
			d.find("#titulo-modal").html(title);
		}

		d.find(".button-ok").click(function(){

			d.foundation('reveal', 'close');

			if(callback){
				callback();
			}

		});

		d.foundation('reveal', 'open');
	}



}(jQuery));
