<?php
namespace Task\Mail;

use Cm;
use stdClass;
use Models;

require_once __DIR__."/__init__.php";
require_once __DIR__."/../../public/modelos/Pedido.php";

$mail=new NotificacionPedido();
$mail->exec();

class NotificacionPedido
{
    public function exec()
    {
        global $cfg;
        global $db;
        global $ca;
        global $mail_root;

        $ca=new Cm\DbQuery($db);

        $id_pedido = filter_input(INPUT_GET,"id_pedido",FILTER_VALIDATE_INT,FILTER_NULL_ON_FAILURE);
        if( empty($id_pedido) ){
        	echo "Pedido invalido";
        	exit;
        }

        $d = new stdClass();

        $ped = \PedidoMdl::get($id_pedido);
        $d->enc = $ped->enc();
        $d->det = $ped->det();

        // Categoria donde se encuentran los destinatarios del pedido
        $destinos_correo = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 71"
        ]);

        $destinos_correo = explode("\n" , $destinos_correo->attrs->correos->data[0]);
        $destinos_correo[] = $d->enc->com_correo_electronico;

        // plantilla para el correo de compra.
        // $plantilla = Models\Runtime\EavModel::item( (object)[
        // 	"filter"=>"items.category_id = 89"
        // ]);

        //Cargar las formas de pago.
        $d->forma_pago = Models\Runtime\EavModel::item( (object)[
        	"filter"=>"items.category_id = 61 and items.item_id=:item_id",
        	"params"=>[
        		":item_id"=>$d->enc->id_forma_pago
        	]
        ]);

        $d->enc->total = number_format($d->enc->total);
        $d->enc->subtotal = number_format($d->enc->subtotal);
        $d->enc->total_transporte = number_format($d->enc->total_transporte);
        $d->enc->total_iva = number_format($d->enc->total_iva);
        $d->enc->total_descuento = number_format($d->enc->total_descuento);
        // $d->enc->total_usd = number_format($d->enc->total_usd);

        foreach ($d->det as $k=>$v) {
        	$d->det[$k]->subtotal = number_format($d->det[$k]->subtotal);
        	$d->det[$k]->precio = number_format($d->det[$k]->precio);
        }

        $d->host = "http://www.{$cfg["appHost"]}";

        $html = Cm\MailInliner::inline(__FILE__, $d, (object)["root"=>$mail_root]);

        Cm\Mail::send((object)[
            "smtp"=>$cfg["smtp"],
            "from"=>$cfg["smtp"]["from"],
            "from_name"=>$cfg["smtp"]["from_name"],
            //"to"=>$asesor->email,
            "to"=>"{$d->enc->com_correo_electronico}",
            "bcc"=>$destinos_correo,
            "subject"=>"Información de su pedido en {$cfg["appHost"]}",
            "html"=>$html
        ]);


        $db->transaction();

        $ca->prepareTable("pedidos_e");
        $ca->bindValue(":notificado",1);
        $ca->bindWhere(":id_pedido",$id_pedido);
        $ca->execUpdate();

        $db->commit();

        echo "1";
    }
}
