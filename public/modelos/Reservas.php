<?php

class ReservasMdl
{
    public static function cotizar(stdClass $p)
    {
        $res = new stdClass();

        //FECHAS CONVERSION
        $fecha_ini = str_replace("/", "-", $p->fecha_ini);
        $fecha_ini = new DateTime($fecha_ini);
        $fecha_fin = str_replace("/", "-", $p->fecha_fin);
        $fecha_fin = new DateTime($fecha_fin);
        // FIN CONVERSION
        $res->noches = 0;
        //VALIDACION NOCHES
        if (empty($p->fecha_ini)) {
            $res->noches = 0;
        } elseif (empty($p->fecha_fin)) {
            $res->noches = 0;
        } elseif ($fecha_ini > $fecha_fin) {
            $res->noches = 0;
        } else {
            $res->noches = (integer)$fecha_fin->diff($fecha_ini)->format("%a");
        }
        //FIN VALIDACION NOCHES

        //CONSULTA A ITEM DEL ADMIN
        $filter=[];
        $filter[]="items.category_id=:category_id";
        $filter[]="and";
        $filter[]="items.item_id = :item_id";

        $cabana = Models\Runtime\EavModel::item((object)[
            "filter"=>$filter,
            "params"=>[
                ":category_id"=>25,
                ":item_id"=>$p->item_id
            ]
        ]);
        // FIN CONSULTA A ITEM DEL ADMIN

        //ES EL PORCENTAJE DE LA TARIFA QUE LE VAMOS A COBRAR
        $tarifa_porcentaje = 1;

        // // CONSULTA ALIMENTACION ALMUERZO
        // if ($p->almuerzo==1) {
        //     $filter=[];
        //     $filter[]="items.item_id = :item_id and";
        //     $filter[]="items.category_id = :category_id";
        //     $alimentacion = Models\Runtime\EavModel::item((object)[
        //         "filter"=>$filter,
        //         "params"=>[
        //             ":item_id"=>148,
        //             ":category_id"=>38
        //         ]
        //     ]);
        //
        //     $pago_almuerzo = $alimentacion->attrs->valor->data[0] * $res->noches * (integer)$p->ocupantes;
        //
        //     //OCUPACION MAXIMA
        //     if ((integer)$cabana->attrs->maximo_ocupantes->data[0] == (integer)$p->ocupantes) {
        //         $tarifa_porcentaje = 1 - $alimentacion->attrs->descuento->data[0] / 100;
        //         $pago_almuerzo = $pago_almuerzo * $tarifa_porcentaje;
        //     }
        //     //FIN OCUPACION MAXIMA
        //
        // }else{
        //     $pago_almuerzo = 0;
        // }
        //
        // // FIN CONSULTA ALIMENTACION ALMUERZO

        $pago_almuerzo = 0;


        // CONSULTA ALIMENTACION CENA
        if ($p->cena == 1) {
            $filter=[];
            $filter[]="items.item_id = :item_id and";
            $filter[]="items.category_id = :category_id";
            $alimentacion = Models\Runtime\EavModel::item((object)[
                "filter"=>$filter,
                "params"=>[
                    ":item_id"=>149,
                    ":category_id"=>38
                ]
            ]);

            $pago_cena = $alimentacion->attrs->valor->data[0] * $res->noches * (integer)$p->ocupantes;

            //OCUPACION MAXIMA
            if ((integer)$cabana->attrs->maximo_ocupantes->data[0] == (integer)$p->ocupantes) {
                $tarifa_porcentaje = 1 - $alimentacion->attrs->descuento->data[0] / 100;
                $pago_cena = $pago_cena * $tarifa_porcentaje;
            }
            //FIN OCUPACION MAXIMA


        }else {
            $pago_cena = 0;
        }

        $pago_alimentos = $pago_almuerzo + $pago_cena;


        // FIN CONSULTA ALIMENTACION CENA

        //PLAN NOVIOS
        if (isset($p->servicios_0) && $p->servicios_0 != 0) {
            $filter=[];
            $filter[]="items.category_id=:category_id";
            $filter[]="and";
            $filter[]="items.item_id = :item_id";
            $servicios_adicionales = Models\Runtime\EavModel::item((object)[
                "filter"=>$filter,
                "params"=>[
                    ":category_id"=>39,
                    ":item_id"=>$p->servicios_0
                ]
            ]);

            $pago_plan_novios = $servicios_adicionales->attrs->precio->data[0];
        } else{
            $pago_plan_novios = 0;
        }
        //FIN PLAN NOVIOS


        //PAGO ALOJAMIENTO
        $precio_noche =
        (float)$cabana->attrs->precio_minimo->data[0] +
        ((float)$cabana->attrs->precio_adulto_adicional->data[0] *
        ((integer)$p->ocupantes - (integer)$cabana->attrs->minimo_ocupantes->data[0]));

        $pago_alojamiento = 0;
        $segunda_noche = false;
        for ($i=0; $i < $res->noches; $i++) {
                $fecha_liq = $fecha_ini;
                $dia_semana = date("N", strtotime($fecha_liq->format('Y-m-d H:i:s')));

                $fecha_liq->modify("+{$i}+1 day");
                if ($segunda_noche) {
                  $pago_alojamiento += $precio_noche * (1 - (float)$cabana->attrs->descuento_semana->data[0]/100);
                  continue;
                }
                //CONSULTA DESCUENTOS FESITVOS
                $filter=[];
                $filter[]="items.category_id=:category_id";
                $filter[]="and";
                $filter[]="{fecha} = :fecha ";
                $festivos = Models\Runtime\EavModel::items((object)[
                    "filter"=>$filter,
                    "params"=>[
                        ":category_id"=>41,
                        ":fecha"=> $fecha_liq->format('Y-m-d')
                    ]
                ]);
                switch ($dia_semana) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 7:
                          if (empty($festivos)) {
                            $pago_alojamiento += $precio_noche * (1 - (float)$cabana->attrs->descuento_semana->data[0]/100);
                          }else{
                            $pago_alojamiento += $precio_noche;
                            $segunda_noche = true;
                          }

                      break;

                      case 5:
                      case 6:
                            if ($segunda_noche) {
                              $pago_alojamiento += $precio_noche * (1 - (float)$cabana->attrs->descuento_fin_de_semana->data[0]/100);
                            }else{
                              $pago_alojamiento += $precio_noche;
                              $segunda_noche=true;
                            }
                        break;
                  }
          }

        //FIN PAGO ALOJAMIENTO

        //TOTALES
        $res->total = 0;
        $res->total = $pago_alojamiento + $pago_alimentos + $pago_plan_novios;
        //$res->total =  ($res->total + $pago_alimentos * (integer)$p->ocupantes * $res->noches);
        //FIN TOTALES

        // PERCIOS NOCHES
        if ($res->noches != 0) {
          $res->precio_noche = $res->total / $res->noches;
        }
        //$res->precio_noche = $res->precio_noche * $tarifa_porcentaje;
        //FIN PERCIOS NOCHES

        return $res;
    }
}
