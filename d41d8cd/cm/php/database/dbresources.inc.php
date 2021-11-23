<?php
namespace Cm;

class DbResources {

    public static function load(&$db, $rsTable, $relName, $relKeyValue, $relFields = '') {
        $ca = new DbQuery($db);
        $sql = "select
        rsid as id,
        rsname as name,
        rstype as type,
        rssize as size,
        relfield
        from {$rsTable}
        where relname=:relname and relkeyvalue=:relkeyvalue";

        if (is_string($relFields) && !empty($relFields)) {
            $tmp = explode(",", $relFields);
            $tmp = "'" . join("','", $tmp) . "'";
            $sql .= " and relfield in ( {$tmp} )";
        }

        if (is_array($relFields) && !empty($relFields)) {
            $tmp = "'" . join("','", $relFields) . "'";
            $sql .= " and relfield in ( {$tmp} )";
        }


        $ca->prepare($sql);
        $ca->bindValue(":relname", $relName, true);
        $ca->bindValue(":relkeyvalue", $relKeyValue, true);

		//throw new PublicException( $ca->preparedQuery() );

        $ca->exec();
        $result = new \stdClass();
        for ($i = 0; $i < $ca->size(); $i++) {
            $r = $ca->fetch();
            $relField = $r->relfield;
            unset($r->relfield);
            $result->{$relField} = $r;
        }

        return $result;
    }

    public static function save(&$db, $rsTable, $relName, $relKeyValue, $relField, $data,$privateTmpPath,$folderPath,$pathFunc) {
        //throw new PublicException(print_r(func_get_args(),1) );

        $ca = new DbQuery($db);
        $fields = "rsid,rsname,rstype,rssize,relname,relkeyvalue,relfield";

        //eliminar
        if (isset($data->id) && $data->id == 0 && !isset($data->tmp_name) ) {
            self::delete($db, $rsTable, $relName, $relKeyValue, $relField,$folderPath,$pathFunc);
            return "delete";
        }

        //existe y es igual
        if( isset($data->id) && $data->id!=0 && !isset($data->tmp_name) ){
            return "exists";
        }




        if (empty($data->id)) {

            $rs = self::load($db,$rsTable,$relName,$relKeyValue,$relField);
            if( !empty($rs) ){
                self::delete($db,$rsTable,$relName,$relKeyValue,$relField);
            }


            $ca->prepareInsert($rsTable, $fields);
            $rsId = $db->nextVal("{$rsTable}_rsid");
        }
        else {
            $ca->prepareUpdate($rsTable, $fields, "rsid=:rsid");
            $rsId = $data->id;
        }


        if (!empty($data->tmp_name)) {
            $folderId = $pathFunc($rsId);
            $folderPath.="/{$folderId}";
            $filePath = "{$folderPath}/{$rsId}";

            //throw new Exception($folderPath);

            if (!is_dir($folderPath)) {
                @$status = mkdir($folderPath);
                if (!$status) {
                    throw new \Exception("Failed creating resource directory {$folderPath}");
                }
            }

            $source = "{$privateTmpPath}/{$data->tmp_name}";
            @ $status = copy($source, $filePath);
            if (!$status) {
                throw new \Exception("Failed coping resource '{$source}'=>'{$filePath}'");
            }
        }


        $ca->bindValue(":rsid", $rsId, false);
        $ca->bindValue(":relname", $relName, true);
        $ca->bindValue(":relkeyvalue", $relKeyValue, true);
        $ca->bindValue(":relfield", $relField, true);
        $ca->bindValue(":rsname", $data->name, true);
        $ca->bindValue(":rstype", $data->type, true);
        $ca->bindValue(":rssize", $data->size, false);
        $ca->exec();
        return "insert";
    }

    public static function delete(&$db, $rsTable, $relName, $relKeyValue, $relFields = '') {
        $ca = new DbQuery($db);
        $sql = "delete from {$rsTable}
        where relname=:relname and relkeyvalue=:relkeyvalue";

        if (!empty($relFields)) {
            $tmp = explode(",", $relFields);
            $tmp = "'" . join("','", $tmp) . "'";
            $sql .= " and relfield in ( {$tmp} )";
        }

        $ca->prepare($sql);
        $ca->bindValue(":relname", $relName, true);
        $ca->bindValue(":relkeyvalue", $relKeyValue, true);
        $ca->exec();
        return true;
    }
}


?>
