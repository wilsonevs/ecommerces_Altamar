<?php
namespace Task\Mail;

use Cm;
use stdClass;
use Models;

require_once __DIR__."/__init__.php";

$mail=new NotificacionSuscribe();
$mail->exec();

class NotificacionSuscribe
{
    public function exec()
    {
        global $cfg;
        global $db;
        // global $ca;
        global $mail_root;

        // $ca=new Cm\DbQuery($db);

        $item_id = filter_input(INPUT_GET,"item_id",FILTER_VALIDATE_INT,FILTER_NULL_ON_FAILURE);
        if( empty($item_id) ){
        	echo "Contacto invalido";
        	exit;
        }

        $d = new stdClass();

        // Categoria donde se encuentran los destinatarios de contacto y registro
        $destinos_correo = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 12"
        ]);

        $destinos_correo = explode("\n" , $destinos_correo->attrs->correos->data[0]);

        // plantilla para el correo de compra.
        // $plantilla = Models\Runtime\EavModel::item( (object)[
        // 	"filter"=>"items.category_id = 89"
        // ]);

        $d = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 67 and items.item_id=:item_id",
        	"params"=>[
        		":item_id"=>$item_id
        	]
        ]);

        $d->host = "http://www.{$cfg["appHost"]}";

        $html = Cm\MailInliner::inline(__FILE__, $d, (object)["root"=>$mail_root]);

        Cm\Mail::send((object)[
            "smtp"=>$cfg["smtp"],
            "from"=>$cfg["smtp"]["from"],
            "from_name"=>$cfg["smtp"]["from_name"],
            //"to"=>$asesor->email,
            // "bcc"=>$destinos_correo,
            "to"=>$destinos_correo,
            "subject"=>"Notificación de suscrito en {$cfg["appHost"]}",
            "html"=>$html
        ]);


        $tmp = Models\Runtime\EavModel::load($item_id);
        $tmp->enviado=1;

        $db->transaction();
        Models\Runtime\EavModel::save($tmp);
        $db->commit();

        echo "1";
        exit;
    }
}
