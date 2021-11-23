<?php
require_once __DIR__.'/__init__.php';
require_once "{$cfg["appPath"]}/public/modelos/Pedido.php";

// {
//   "response_code_pol": "1",
//   "phone": "",
//   "additional_value": "0.00",
//   "test": "1",
//   "transaction_date": "2019-04-01 12:00:05",
//   "cc_number": "************2331",
//   "cc_holder": "APPROVED",
//   "error_code_bank": "",
//   "billing_country": "CO",
//   "bank_referenced_name": "",
//   "description": "Compra realizada en localhost de prueba",
//   "administrative_fee_tax": "0.00",
//   "value": "2249900.00",
//   "administrative_fee": "0.00",
//   "payment_method_type": "2",
//   "office_phone": "",
//   "email_buyer": "dontcry-702@hotmail.com",
//   "response_message_pol": "APPROVED",
//   "error_message_bank": "",
//   "shipping_city": "",
//   "transaction_id": "31186554-452d-40f5-aa0a-2dbd1aff1b9f",
//   "sign": "591a9f50641c9f9ac15fb6bfeb775636",
//   "operation_date": "2019-04-01 12:00:05",
//   "tax": "0.00",
//   "transaction_bank_id": "00000000",
//   "payment_method": "10",
//   "billing_address": "",
//   "payment_method_name": "VISA",
//   "pseCycle": "null",
//   "pse_bank": "",
//   "state_pol": "4",
//   "date": "2019.04.01 12:00:05",
//   "nickname_buyer": "",
//   "reference_pol": "845483531",
//   "currency": "COP",
//   "risk": "0.09",
//   "shipping_address": "",
//   "bank_id": "10",
//   "payment_request_state": "A",
//   "customer_number": "",
//   "administrative_fee_base": "0.00",
//   "attempts": "1",
//   "merchant_id": "508029",
//   "exchange_rate": "1.00",
//   "shipping_country": "CO",
//   "installments_number": "1",
//   "franchise": "VISA",
//   "payment_method_id": "2",
//   "extra1": "",
//   "extra2": "",
//   "antifraudMerchantId": "",
//   "extra3": "",
//   "commision_pol_currency": "",
//   "nickname_seller": "",
//   "ip": "172.18.49.47",
//   "commision_pol": "0.00",
//   "airline_code": "",
//   "billing_city": "",
//   "pse_reference1": "",
//   "cus": "00000000",
//   "reference_sale": "18",
//   "authorization_code": "00000000",
//   "pse_reference3": "",
//   "pse_reference2": ""
// }

$p = new stdClass();

if ($_REQUEST['response_code_pol'] == 1 && $_REQUEST['state_pol'] == 4) {
  $p->estado = 'pagado';
}else {
  $p->estado = 'anulado';
}

$p->notas = $_REQUEST['description'];
$p->id_pedido = $_REQUEST['reference_sale'];


$db->transaction();
$pedido = new PedidoMdl($p->id_pedido);
$pedido->cambiarEstado($p);
$db->commit();

echo "1";

?>
