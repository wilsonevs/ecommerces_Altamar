<?php
require_once "{$cfg["modelsPath"]}/runtime/EavModel.php";
require_once __DIR__."/../../cm/php/fileformats/PHPExcel_1.8.0/Classes/PHPExcel.php";

$exports[]="Importacion";
class Importacion {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		return $res;
	}


	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		return $res;
	}


	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

        global $cfg;
        if (!isset($p->archivo->tmp_name)) {
            throw new Cm\Exception("Debe seleccionar un archivo para continuar.");
        }

        $zip = new ZipArchive;
        $lectura_archivo = $zip->open("{$cfg["appPath"]}/tmp/{$p->archivo->tmp_name}");
        if ($lectura_archivo === TRUE) {

            if(!file_exists("{$cfg["appPath"]}/tmp/productos")){
                mkdir("{$cfg["appPath"]}/tmp/productos");
            }

            $zip->extractTo("{$cfg["appPath"]}/tmp/productos");
            $zip->close();

            $archivo = "{$cfg["appPath"]}/tmp/productos/importacion/items.xlsx";

            //Leemos el excel
            $obj_php_excel = PHPExcel_IOFactory::load($archivo);
            $datos=array();


            foreach ($obj_php_excel->getWorksheetIterator() as $worksheet) {
                $highestrow = $worksheet->getHighestRow(); // e.g. 10
                $highest_column = $worksheet->getHighestColumn(); // e.g 'F'
                $highest_column_index = PHPExcel_Cell::columnIndexFromString($highest_column);
                $titulos = array();
                for ($col = 0; $col < $highest_column_index; ++ $col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, 1);
                    $titulos[$col] = $cell->getValue();
                }

                for ($fila = 2; $fila <= $highestrow; $fila++) {
                    $d_filas = array();
                    for ($col = 0; $col < $highest_column_index; ++ $col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $fila);

						//COLUMNA B PARA SACAR CLAVE
						if ($col == 1) {
							$d_filas["id"] = [$titulos[$col],$cell->getValue()];
						}

                        $d_filas[$titulos[$col]] = $cell->getValue();

                    }
                    $datos[] = $d_filas;
                }
            }


            //Realizamos la insercion
            Models\Runtime\EavModel::importacionExcel($datos);

        } else {
            throw new Cm\Exception("No se pudo leer el archivo, verifique que tenga la extensión válida");
        }

		$res->message = "Importación Exitosa.";
		return $res;
	}

}


?>
