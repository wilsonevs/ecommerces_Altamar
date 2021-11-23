<?php
namespace Task\Mail;

use Cm;
use stdClass;
use Models;

require_once __DIR__."/__init__.php";

$mail=new NotificacionRecuperarContra();
$mail->exec();

class NotificacionRecuperarContra
{
    public function exec()
    {
        global $cfg;
        global $db;
        // global $ca;
        global $mail_root;

        $item_id = filter_input(INPUT_GET,"item_id",FILTER_VALIDATE_INT,FILTER_NULL_ON_FAILURE);
        if( empty($item_id) ){
        	echo "Item Invalido.";
        	exit;
        }


        $d = new stdClass();

        // Categoria donde se encuentran los destinatarios de contacto y registro
        $destinos_correo = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 12"
        ]);

        $destinos_correo = explode("\n" , $destinos_correo->attrs->correos->data[0]);


        //Categoria de los usuarios que solicitaron recuperar clave
        $d = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 60 and items.item_id=:item_id",
        	"params"=>[
        		":item_id"=>$item_id
        	]
        ]);

        // Consultar el formulario de registro
        $cuenta = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 39 and {correo_electronico}=:correo_electronico",
        	"params"=>[
        		":correo_electronico"=>$d->attrs->correo_electronico->data[0]
        	]
        ]);


        //Generador de contraseña
        $d->contrasena_nueva = $this->generarPass();
        $d->contrasena = password_hash($d->contrasena_nueva, PASSWORD_DEFAULT);

        $tmp = Models\Runtime\EavModel::load($cuenta->item_id);
        $tmp->contrasena = $d->contrasena;


        $html = Cm\MailInliner::inline(__FILE__, $d, (object)["root"=>$mail_root]);

        Cm\Mail::send((object)[
            "smtp"=>$cfg["smtp"],
            "from"=>$cfg["smtp"]["from"],
            "from_name"=>$cfg["smtp"]["from_name"],
            "to"=>"{$d->attrs->correo_electronico->data[0]}",
            "subject"=>"Recuperación de contraseña",
            "html"=>$html
        ]);

        $tmp2 = Models\Runtime\EavModel::load($item_id);
        $tmp2->enviado=1;

        $db->transaction();
        Models\Runtime\EavModel::save($tmp);
        Models\Runtime\EavModel::save($tmp2);
        $db->commit();

        echo "1";
        exit;
    }

    public function generarPass() {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $password = "";
        //Reconstruimos la contraseña segun la longitud que se quiera
        for($i=0;$i<10;$i++) {
            //obtenemos un caracter aleatorio escogido de la cadena de caracteres
            $password .= substr($str,rand(0,62),1);
        }

        return $password;
    }
}
