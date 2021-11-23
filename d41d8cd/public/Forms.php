<?php

class Forms {

	public static function ers(stdClass $p=null){
		//$si=Application::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$sql="
		select
			category_name,
			category_path
		from eav_categories
		where
			plat_id=:plat_id
			and category_id=:category_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$p->plat_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->exec();
		$res->category = $ca->fetch();


		$res->ds=new stdClass();

		$st = Models\Runtime\EavItems::struct( (object)["category_id"=>$p->category_id] );

		foreach($st->attrs as $attr){

			if( $attr->rel_type_id ){
				$ca->prepare($attr->rel_sql);
				$ca->exec();
				$res->ds->{$attr->attr_name} = $ca->fetchAll();
			}

		}

		return $res;
	}

	public static function auto(stdClass $p){
		$p->exclude = coalesce_array($p->exclude);

		$st = Models\Runtime\EavItems::struct((object)[
			"plat_id"=>$p->plat_id,
			"category_id"=>$p->category_id
		]);

		$ers=static::ers($p);
		//print_r($ers);exit;

		$html=[];
		//$html[]='<form>';

		foreach($st->attrs as $r){
			if( in_array($r->attr_name,$p->exclude) ) continue;

			$html[]='<div class="row">';
			$html[]='<div class="column large-24">';

			switch($r->attr_type){

				case "datefield":
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div><input type="text" name="'.$r->attr_name.'"/></div>';
					break;

				case "imagefield":
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div data-cmwidget="cm.ui.form.ImageField" data-name="'.$r->attr_name.'"></div>';
					break;


				case "filefield":
					$uploadName="upload_{$r->attr_name}";
					$iframeName="iframe_{$r->attr_name}";

					$uploadButton="{$r->attr_name}-button";
					$uploadButtonCallback="{$r->attr_name}Explore";
					$uploadReady="{$r->attr_name}UploadReady";
					$uploadInProgress="{$r->attr_name}UploadInProgress";

					$html[]="
					<script>
					function {$uploadButtonCallback}(field){
						var doc = $('iframe[name={$iframeName}]').contents().find('input[type=file]').click();

					}

					window.{$uploadInProgress} = function(){
						$('*[name={$uploadButton}]').val('Cargando Archivo, espere por favor...');
					}

					window.{$uploadReady} = function(data){
						$('*[name={$r->attr_name}]').val( data );
						$('*[name={$uploadButton}]').val('Archivo Cargado');
					}
					</script>
					";

					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div>';
					$html[]='<input type="hidden" name="'.$r->attr_name.'" value=""/>';
					//$html[]='<input type="file" name="upload_'.$r->attr_name.'" onchange="'.$uploadName.'_callback(this)"/>';
					$html[]='<input type="button" class="button tiny" name="'.$uploadButton.'" value="Cargar Archivo Adjunto" onclick="'.$uploadButtonCallback.'();"/>';
					$html[]='<br/><iframe name="'.$iframeName.'" src="/shared/upload.php?field='.$r->attr_name.'" style="display:none;"></iframe>';
					$html[]='</div>';
					break;


				case "richtext":
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div><textarea name="'.$r->attr_name.'"></textarea></div>';
					break;

				case "textarea":
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div><textarea name="'.$r->attr_name.'"></textarea></div>';
					break;

				case "selectbox":
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div><select name="'.$r->attr_name.'">';

					if( isset($ers->ds->{$r->attr_name}) ){
						foreach( $ers->ds->{$r->attr_name} as $option){
							$html[]='<option value="'.$option->data.'">'.$option->label.'</option>';
						}
					}

					$html[]='</select></div>';
					break;

				default:
					$html[]='<span>'.$r->attr_label.'</span>';
					$html[]='<div><input type="text" name="'.$r->attr_name.'"/></div>';
					break;
			}


			$html[]='</div>';
			$html[]='</div>';
		}

		//$html[]='</form>';
		return implode("\n",$html);
	}

	/*
	function hoja_vidaExplore(field){
		var doc = $('iframe[name=iframe_hoja_vida]').contents().find('input[type=file]').click();

	}

	window.hoja_vidaUploadInProgress = function(){
		$('*[name=hoja_vida-button]').val('Cargando Archivo, espere por favor...');
	}

	window.hoja_vidaUploadReady = function(data){
		$('*[name=hoja_vida]').val( data );
		$('*[name=hoja_vida-button]').val('Archivo Cargado');
	}
	*/

	public static function fileUploader($fieldName,$uploadLabel){
		$uploadName="upload_{$fieldName}";
		$iframeName="iframe_{$fieldName}";

		$uploadButton="{$fieldName}-button";
		$uploadButtonCallback="{$fieldName}Explore";
		$uploadReady="{$fieldName}UploadReady";
		$uploadInProgress="{$fieldName}UploadInProgress";

		$html[]="
		<script>
		function {$uploadButtonCallback}(field){
			var input = $('iframe[name={$iframeName}]').contents().find('input[type=file]');
			input.change(function(){
				window.{$uploadInProgress}();
			});

			input.click();
			//var doc = $('iframe[name={$iframeName}]').contents().find('input[type=file]').click();
		}

		window.{$uploadInProgress} = function(){
			$('*[name={$uploadButton}]').val('Cargando Archivo, espere por favor...');
		}

		window.{$uploadReady} = function(data){
			$('*[name={$fieldName}]').val( data );
			$('*[name={$uploadButton}]').val('Archivo Cargado');
		}
		</script>
		";

		$html[]='<div>';
		$html[]='<input type="hidden" name="'.$fieldName.'" value=""/>';
		//$html[]='<input type="file" name="upload_'.$fieldName.'" onchange="'.$uploadName.'_callback(this)"/>';
		$html[]='<input type="button" class="button tiny" name="'.$uploadButton.'" value="'.$uploadLabel.'" onclick="'.$uploadButtonCallback.'();"/>';
		$html[]='<br/><iframe name="'.$iframeName.'" src="/shared/upload.php?field='.$fieldName.'" style="display:none;"></iframe>';
		$html[]='</div>';

		return implode("",$html);
	}


	public static function fileUploaderMultiple($fieldName,$addLabel){

		$uploadName="upload_{$fieldName}";
		$iframeName="iframe_{$fieldName}";

		$uploadButton="{$fieldName}-button";
		$uploadButtonCallback="{$fieldName}Explore";
		$uploadReady="{$fieldName}UploadReady";
		$uploadInProgress="{$fieldName}UploadInProgress";

		$html[]="
		<script>
		function {$uploadButtonCallback}(field){
			var doc = $('iframe[name={$iframeName}]').contents().find('input[type=file]').click();

		}

		window.{$uploadInProgress} = function(){
			$('*[name={$uploadButton}]').val('Cargando Archivo, espere por favor...');
		}

		window.{$uploadReady} = function(data){
			$('*[name={$fieldName}]').val( data );
			$('*[name={$uploadButton}]').val('Archivo Cargado');
		}
		</script>
		";

		$html[]='<div>';
		$html[]='<input type="hidden" name="'.$fieldName.'" value=""/>';
		//$html[]='<input type="file" name="upload_'.$fieldName.'" onchange="'.$uploadName.'_callback(this)"/>';
		$html[]='<input type="button" class="button tiny" name="'.$uploadButton.'" value="Cargar Archivo Adjunto" onclick="'.$uploadButtonCallback.'();"/>';
		$html[]='<br/><iframe name="'.$iframeName.'" src="/shared/upload.php?field='.$fieldName.'" style="display:none;"></iframe>';
		$html[]='</div>';

		return implode("",$html);
	}
}


//$html = Forms::auto( (object)["plat_id"=>1,"category_id"=>10] );
?>
