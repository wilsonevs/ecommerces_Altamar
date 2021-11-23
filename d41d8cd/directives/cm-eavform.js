(function() {
	'use strict';

	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;

	function controller($scope, cm, $rootScope, $document, $compile, $q) {

		$scope.$watch('eav', function(value) {
			//console.log('struct', value);
			if (!value) return;
			$scope.init($scope.eav);
		});

		var defaultFontsFormats=[
			"Andale Mono=andale mono,times",
			"Arial=arial,helvetica,sans-serif",
			"Arial Black=arial black,avant garde",
			"Book Antiqua=book antiqua,palatino",
			"Comic Sans MS=comic sans ms,sans-serif",
			"Courier New=courier new,courier",
			"Georgia=georgia,palatino",
			"Helvetica=helvetica",
			"Impact=impact,chicago",
			"Symbol=symbol",
			"Tahoma=tahoma,arial,helvetica,sans-serif",
			"Terminal=terminal,monaco",
			"Times New Roman=times new roman,times",
			"Trebuchet MS=trebuchet ms,geneva",
			"Verdana=verdana,geneva",
			"Webdings=webdings",
			"Wingdings=wingdings,zapf dingbats"
		].join(";");


		var defaultFontSizeFormats="8pt 10pt 12pt 14pt 18pt 24pt 36pt";

		var scriptLoader = new tinymce.dom.ScriptLoader();
		scriptLoader.add( cmCfg.appRoot+"/editor.js" );
		scriptLoader.loadQueue(function() {
			/*
			if( typeof editorCfg === "undefined" ){
				window.editorCfg={};
			}
			*/

			window.editorCfg = angular.extend({
				font_formats:defaultFontsFormats,
				fontsize_formats:defaultFontSizeFormats
			},window.editorCfg)


			$scope.tinymce_options = {
				language: 'es',
				paste_as_text: true,
				height: '300px',

				content_css: cmCfg.appRoot+"/editor.css",
				font_formats: editorCfg.font_formats,
				fontsize_formats: editorCfg.fontsize_formats,

				plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor colorpicker textpattern",
					"responsivefilemanager"
				],
				toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
				toolbar2: "responsivefilemanager | print preview media | forecolor backcolor emoticons | fontsizeselect | fontselect",
				image_advtab: true,
				extended_valid_elements: "a[*]",

				//habilita enlaces externos,ojo esto deja todos los archivos cargados por el admin con http://....
				remove_script_host : 0,

				//responsive file manager
				relative_urls: false,
				filemanager_title: "Manjedor de Archivos",
				external_filemanager_path: "lib/tinymce/responsive_filemanager/filemanager/",

				onChange: function(e) {
					// put logic here for keypress and cut/paste changes
				},
				inline: false,
				//plugins: 'advlist autolink link image lists charmap print preview',
				skin: 'lightgray',
				theme: 'modern'
			};

		});



		$scope.init = function(res) {
			console.log('res', res);
			res.hiddenFields = res.hiddenFields || [];
			res.readonlyFields = res.readonlyFields || [];

			$scope.st = res.st;


			var fld = null;
			var r = null;

			for (var k in res.st.attrs) {
				fld = res.st.attrs[k];
				fld.attr_value=null;

				fld.hidden = res.hiddenFields.indexOf(fld.attr_name) !== -1;
				fld.readonly = res.readonlyFields.indexOf(fld.attr_name) !== -1;


				/*
				if (fld.attr_type == 'selectbox' && fld.ds_multiple == 0) {
					if (angular.isDefined(res.item.attrs[fld.attr_name])) {
						fld.attr_value = {
							data: res.item.attrs[fld.attr_name].data[0]
						};
					}
					continue;
				}
				*/

				/*
				if (fld.attr_type == 'selectbox' && fld.ds_multiple == 1) {
					fld.attr_value = [];

					if (angular.isDefined(res.item.attrs[fld.attr_name])) {
						for (var k in res.item.attrs[fld.attr_name].data) {

							fld.attr_value.push({
								data: res.item.attrs[fld.attr_name].data[k],
								label: res.item.attrs[fld.attr_name].label[k]
							});
						}

					}
					continue;
				}
				*/


				if (fld.attr_type == 'tokenfield' || fld.attr_type=='selectbox') {
					fld.attr_value = [];

					if (angular.isDefined(res.item.attrs[fld.attr_name])) {
						for (var k in res.item.attrs[fld.attr_name].data) {

							fld.attr_value.push({
								data: res.item.attrs[fld.attr_name].data[k],
								label: res.item.attrs[fld.attr_name].label[k]
							});
						}

					}



					fld.acQuery = (function(fld) {
						return function(q, isInitializing) {

							return cm.rpc('Eav.acAttrDs', {
								type_id: res.st.type_id,
								attr_id: fld.attr_id,
								filter: q
							})

							.catch(function(res) {
								cm.error(res);
							});
						};

					})(fld);

					continue;
				}

				if (fld.attr_type == 'imagefield' || fld.attr_type == 'filefield') {
					if (angular.isDefined(res.item.attrs[fld.attr_name])) {
						fld.attr_value = res.item.attrs[fld.attr_name].extra;
					}
					continue;
				}

				if (angular.isDefined(res.item.attrs[fld.attr_name])) {
					fld.attr_value = res.item.attrs[fld.attr_name].data[0];
				}


			}

			//console.log('$scope.st',res.st.attrs[1]);

			$scope.st = res.st;
		}


		$scope.getRecord = function() {

			var dr = {};
			//dr.category_id = $scope.params.category_id;
			//dr.item_id = $scope.params.item_id || "";

			var fld = null;
			for (var k in $scope.st.attrs) {
				fld = $scope.st.attrs[k];

				if (fld.attr_value !== null && fld.attr_value !== undefined && fld.attr_value.data) {
					dr[fld.attr_name] = fld.attr_value.data;
					continue;
				}

				if (fld.attr_value === undefined) {
					fld.attr_value = '';
				}

				dr[fld.attr_name] = fld.attr_value;
			}

			return dr;
		}


		$scope.test = function() {
			alert("test");
		}

		$scope.api.test = $scope.test;
		$scope.api.getRecord = $scope.getRecord;
	}

	function link(scope, elem, attrs) {

		/*
		attrs.$observe('init', function(value) {
			console.log('init',value);
		});
		*/
		/*
		scope.$watch('struct', function(value) {
			console.log('struct',value);
		});
		*/
	}


	angular.module('cm.directives').directive('cmEavForm', function() {
		return {
			scope: {
				value: '=ngModel',
				api: '=',
				eav: '=',
				hiddenFields: '=?',
				readonlyFields: '=?'
			},
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),
			controller: controller,
			link: link
		};
	});


})();
