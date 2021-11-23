<?php
require_once __DIR__.'/__init__.php';
require_once "{$cfg["appPath"]}/public/modelos/Pedido.php";

$ca = new Cm\DbQuery($db);

  // {
  //   "mc_gross": "5.00",
  //   "protection_eligibility": "Ineligible",
  //   "address_status": "confirmed",
  //   "item_number1": "1713",
  //   "payer_id": "EQEPAY7MJHBK4",
  //   "address_street": "calle 10 sur #53 e 25",
  //   "payment_date": "18:45:47 Oct 02, 2019 PDT",
  //   "payment_status": "Pending",
  //   "charset": "windows-1252",
  //   "address_zip": "05002",
  //   "first_name": "camilo",
  //   "address_country_code": "ES",
  //   "address_name": "camilo lopez",
  //   "notify_version": "3.9",
  //   "custom": "",
  //   "payer_status": "unverified",
  //   "address_country": "Spain",
  //   "num_cart_items": "1",
  //   "address_city": "medellin",
  //   "verify_sign": "A0V4NQmPkEYKSzUEakRJltcHqXSDAvh-iykHzLj9S9TWVXc.AgtcZh6P",
  //   "payer_email": "kam-702@hotmail.com",
  //   "txn_id": "5Y9476932T2532746",
  //   "payment_type": "instant",
  //   "last_name": "lopez",
  //   "item_name1": "Compra realizada en dannyberrios.app",
  //   "address_state": "MADRID",
  //   "receiver_email": "paypal@dannyberrios.com",
  //   "shipping_discount": "0.00",
  //   "quantity1": "1",
  //   "insurance_amount": "0.00",
  //   "pending_reason": "unilateral",
  //   "txn_type": "cart",
  //   "discount": "0.00",
  //   "mc_gross_1": "5.00",
  //   "mc_currency": "USD",
  //   "residence_country": "ES",
  //   "test_ipn": "1",
  //   "shipping_method": "Default",
  //   "transaction_subject": "",
  //   "payment_gross": "5.00",
  //   "ipn_track_id": "58e6afb0ada7e"
  // }


    $sql = "select id_cliente, id_carro from pedidos_e where id_pedido = {$_REQUEST['item_number1']}";
    $ca->prepare($sql);
    $ca->exec();

    if ($ca->size() == 0) {
      echo 'No se encontro un pedido';
      exit;
    }

    $p = new stdClass();

    if ($_REQUEST['payment_status'] == 'Completed' || $_REQUEST['payment_status'] == 'Pending') {
      $p->estado = 'pagado';
    } else {
      $p->estado = 'anulado';
    }

    $p->id_pedido = $_REQUEST['item_number1'];
    $p->notas = json_encode($_REQUEST);


    $db->transaction();
    $pedido = new PedidoMdl($p->id_pedido);
    $pedido->cambiarEstado($p);
    $db->commit();

    echo "1";


?>
