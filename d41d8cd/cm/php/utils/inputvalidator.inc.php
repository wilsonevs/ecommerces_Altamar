<?php
namespace Cm;
require_once dirname(__FILE__) . '/ji18n.inc.php';

class InputValidator {

    const CM_FILTER_VALIDATE_STRING = "CM_FILTER_VALIDATE_STRING";
    const CM_FILTER_VALIDATE_OPTION = "CM_FILTER_VALIDATE_OPTION";
    const CM_FILTER_VALIDATE_PASSWORD = "CM_FILTER_VALIDATE_PASSWORD";
    const CM_FILTER_SANITIZE_STRING_XSS = "CM_FILTER_SANITIZE_STRING_XSS";
    const CM_FILTER_VALIDATE_DATE = "CM_FILTER_VALIDATE_DATE";

    const outputString ="string";
    const outputArray = "array";

    public static function xss_clean($data) {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

    public static function filterInputGet($name, $required, $filter, $default=false, $defaultOnNullOrFalse=true) {

        if (!isset($_GET[$name]) && $required === true) {
            throw new Exception("Invalid input var {$name}");
        }

        if (!isset($_GET[$name])) {
            return $default;
        }

        //$value = filter_input(INPUT_GET, $name, $filter);
        $value = filter_var($_GET[$name],$filter);

        if ($required && ( is_null($value) || $value === false )) {
            throw new Exception("Invalid input var {$name}");
        }

        if ($defaultOnNullOrFalse && ( is_null($value) || $value === false )) {
            return $default;
        }

        return $value;
    }

    public static function sanitizeInputGet($name,$required,$filter,$default){

    }


    public static function sanitizeVar($var,$op){
        if( $op["filter"]==self::CM_FILTER_SANITIZE_STRING_XSS){
            return self::xss_clean($var);
        }

        throw new \Exception("Invalid sanitize filter");
    }

    public static function validateVar($var, $op) {
        $errorMsg = empty($op["error_msg"])?"":$op["error_msg"];
        $op['min_length'] = isset($op['min_length']) ? $op['min_length'] : -1;
        $op['max_length'] = isset($op['max_length']) ? $op['max_length'] : -1;

        //$op['label'] = isset($op['label']) ? $v['label'] : str_replace("_", " ", ucfirst($op['name']));

        $options = array('flags' => FILTER_NULL_ON_FAILURE, 'options' => array());

        if ($op['filter'] === FILTER_VALIDATE_REGEXP) {
            $options['options']['regexp'] = $op['regexp'];
        }

        if( $op["filter"] === FILTER_VALIDATE_INT ){
            $filterOptions = array("min_range","max_range");
            foreach($filterOptions as $k){
                if( isset($op[$k])){
                    $options["options"][$k] = $op[$k];
                }
            }
        }


        if ($op['filter'] === self::CM_FILTER_VALIDATE_STRING) {
            if ( ($op['min_length'] != -1 && strlen($var) < $op['min_length']) || ($op['max_length'] != -1 && strlen($var) > $op['max_length']) ) {
                $errorMsg = empty($errorMsg)?'Longitud de cadena inválida en el campo %1$s, debe escribir mínimo %2$d máximo %3$d caracteres':$errorMsg;
                return _tr($errorMsg, $op['label'], $op['min_length'], $op['max_length']);
            }

            return true;
        }

        if( $op['filter'] ==self::CM_FILTER_VALIDATE_DATE ){
            $tmp = explode("-",$var);
            if( !checkdate((integer)$tmp[1], (integer)$tmp[2],(integer) $tmp[0])){
                $errorMsg = empty($errorMsg)?'%1$s inválida':$errorMsg;
                return _tr($errorMsg,$op["label"]);
            }
            return true;
        }


        if ($op["filter"] == self::CM_FILTER_VALIDATE_OPTION) {
            if (!in_array($var, $op["options"])) {
                $errorMsg = empty($errorMsg)?'Debe seleccionar una de las opciones del menú desplegable para el campo %1$s':$errorMsg;
                return _tr($errorMsg, $op["label"]);
            }
            return true;
        }

        if ($op["filter"] == self::CM_FILTER_VALIDATE_PASSWORD) {
            $op["min_numbers"] = isset($op["min_numbers"]) ? $op["min_numbers"] : 0;
            $op["min_uppercase"] = isset($op["min_uppercase"]) ? $op["min_uppercase"] : 0;
            $op["min_lowercase"] = isset($op["min_lowercase"]) ? $op["min_lowercase"] : 0;
            //$op["min_symbols"] = isset($op["min_symbols"]) ? $op["min_symbols"] : 0;

            $message = array();

            if (strlen($var) < $op["min_length"]) {
                $message[] = _tr('una longitud mínima de %1$d caracteres', $op["min_length"]);
            }

            if (preg_match("/[0-9]{{$op["min_numbers"]}}/", $var) == 0) {
                $message[] = _tr('como mínimo %1$d caracter numérico', $op["min_numbers"]);
            }

            if (preg_match("/[A-Z]{{$op["min_uppercase"]}}/", $var) == 0) {
                $message[] = _tr('como mínimo %1$d letras en mayúsculas', $op["min_uppercase"]);
            }

            if (preg_match("/[a-z]{{$op["min_lowercase"]}}/", $var) == 0) {
                $message[] = _tr('como mínimo %1$d letras en minúsculas', $op["min_lowercase"]);
            }


            if (count($message) > 0) {
                $errorMsg = empty($errorMsg)?'El campo %1$s requiere: %2$s':$errorMsg;
                return _tr($errorMsg, $op["label"], implode(",", $message));
            }

            return true;
        }


        $r = filter_var($var, $op['filter'], $options);


        if (is_null($r)) {
            switch ($op["filter"]) {
                case FILTER_VALIDATE_REGEXP:
                    $errorMsg = empty($errorMsg)?'Valor inválido en el campo %1$s':$errorMsg;
                    return _tr($errorMsg, $op['label']);
                    break;

                case FILTER_VALIDATE_INT:
                    $errorMsg = empty($errorMsg)?'Valor inválido en el campo %1$s, debe ser de tipo numérico sin decimales':$errorMsg;
                    return _tr($errorMsg, $op["label"]);
                    break;

                case FILTER_VALIDATE_FLOAT:
                    $errorMsg = empty($errorMsg)?'Valor inválido en el campo %1$s, debe ser de tipo numérico':$errorMsg;
                    return _tr($errorMsg, $op["label"]);
                    break;

                case FILTER_VALIDATE_EMAIL:
                    $errorMsg = empty($errorMsg)?'Valor inválido en el campo %1$s, debe ser un correo electrónico valido':$errorMsg;
                    return _tr($errorMsg, $op["label"]);
                    break;

                default:
                    $errorMsg = empty($errorMsg)?'Valor inválido en el campo %1$s':$errorMsg;
                    return _tr($errorMsg, $op['label']);
                    break;
            }
        }

        return true;
    }


    /**
     *
     * @param <type> $p
     * @param <type> $iv
     * @param array $op
     * @return <type>
     */
    public static function validateVars(&$p, $iv, $op=array()) {
        $op["output"] = isset($op['output']) ? $op['output'] : 'string';

        $output = array();

        foreach ($iv as $v) {
            $v['label'] = isset($v['label']) ? $v['label'] : str_replace("_", " ", ucfirst($v['name']));


            if ( !isset($p[$v['name']]) ) {

                $message = _tr('El campo %1$s no esta definido', $v['label']);
                if( $op["output"] == self::outputString ){
                    $output[] = $message;
                }
                else {
                    $output[] = array('name' => $v['name'], 'message' => $message);
                }
                continue;
            }


            $result = self::validateVar($p[$v['name']], $v);

            if ($result !== true) {
                if ($op['output'] == 'string') {
                    $output[] = $result;
                } else {
                    $output[] = array('name' => $v['name'], 'message' => $result);
                }
            }
        }


        if ($op['output'] == 'string') {
            return implode("<br/>", $output);
        } else {
            return $output;
        }
    }



    public static function isEmail($email){
        /*
        $pattern="/[a-zA-Z0-9\\._-]+@[a-zA-Z0-9_-\\.]+(\\.[a-zA-Z0-9]{2,4}){1,2}/";
        $matches = array();
        preg_match($pattern, $email, $matches);

        if( count($matches) == 0 ){
            return false;
        }

        if( $matches[0] != $email  ){
            return false;
        }

        return true;
        */

        $result = filter_var($email,FILTER_VALIDATE_EMAIL);
        if( $result === false ){
            return false;
        }

        return true;
    }

}

class InputValidatorException extends \Exception {

    private $m_validation;

    function __construct($message,$validation=array()) {
        $this->message = $message;
        $this->m_validation = $validation;
    }

    public function getValidation() {
        return $this->m_validation;
    }

}

?>
