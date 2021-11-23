jQuery.fn.extend({
	formRecord : function() {
		var $ui = $(this);

		var r = {};
		var oname = '';
		var oval = null;
		var otype = '';
		var otagName = '';

		jQuery.each($ui.find(":input").get(), function() {
			var $obj = $(this);

			try {
				var name = $obj.attr("name").replace("[]", "");
			} catch(e) {
				return;
			}

			if (jQuery.trim(name) == '') {
				return;
			}

			var val = $obj.val();

			tagName = $obj[0].tagName.toLowerCase();
			try {
				type =  $obj.attr("type").toLowerCase();
			}
			catch(e){
				type ='';
			}
			

			if ($obj.attr("name").indexOf("[]") > 0 && !jQuery.isArray(r[name])) {
				r[name] = new Array();
			}

			if (tagName == "select" && val == null && $obj.attr("multiple") == true) {
				r[name] = [];
				return;
			}

			if (jQuery.isArray(r[name]) && $obj.attr("multiple") != true) {

				if (type == "checkbox") {
					//if( $obj.attr("checked")==true){
					if ($obj.is(':checked')) {
						r[name].push(val);
						return;
					}
				} else {
					r[name].push(val);
					return;
				}
			} else {

				if ((type == "checkbox" || type == "radio" ) && !$obj.is(':checked')) {
					return;
				}

				r[name] = val;
				return;
			}
		});

		return r;
	}
});

