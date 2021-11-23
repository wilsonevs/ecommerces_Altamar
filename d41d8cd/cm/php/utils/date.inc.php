<?php
namespace Cm;

class Date {

    public static function months($lang='es'){
            return [
                    "01"=>"Enero",
                    "02"=>"Febrero",
                    "03"=>"Marzo",
                    "04"=>"Abril",
                    "05"=>"Mayo",
                    "06"=>"Junio",
                    "07"=>"Julio",
                    "08"=>"Agosto",
                    "09"=>"Septiembre",
                    "10"=>"Octubre",
                    "11"=>"Noviembre",
                    "12"=>"Diciembre"
            ];
    }

    public static function days($lang='es'){
            return [
                    0=>"Domingo",
                    1=>"Lunes",
                    2=>"Martes",
                    3=>"Miercoles",
                    4=>"Jueves",
                    5=>"Viernes",
                    6=>"Sabado"
            ];
    }

    public static function dmyToYmd($fecha,$separator="/"){
            $tmp = explode($separator,$fecha);
            return "$tmp[2]-{$tmp[1]}-{$tmp[0]}";
    }


    /*
    1: 15 Enero de 2016
    2: Enero 15 de 2016
    3: Miercoles 15 de Enero 2016
    4: 15 Enero
    Ayer
    Hoy
    Mañana
    */


    public static function friendlyDate($date,$format,$options=[]){
		$options = (object) array_merge([
			"nearDays"=>false
		],$options);


		$months = static::months();
		$days = static::days();

		list($y,$m,$d) = explode("-",$date);

		switch($format){

			case 1:
				$res = "{$d} {$months[$m]} de {$y}";
				break;

			case 2:
				$res ="{$months[$m]} {$d} de {$y}";
				break;

			case 3:
				$dow = date("w",mktime(0,0,0,$m,$d,$y) );
				$res="{$days[$dow]} {$d} de {$months[$m]} {$y}";

			default:
				throw new Cm\PublicException("Invalid date format '{$format}'");

		}

		if($options->nearDays){
			$tmp=new DateTime();

			if( $fecha == $tmp->format("Y-m-d") ){
				$res="Hoy";
			}
			elseif( $fecha == ( $tmp->add( new DateInterval("P1D") )->format("Y-m-d") ) ){
				$res="Mañana";
			}
			elseif( $fecha == ( $tmp->sub( new DateInterval("P2D") )->format("Y-m-d") ) ){
				$res="Ayer";
			}
		}


		return $res;
	}

    //Retorna la cantidad de meses que hay entre dos fechas
    public static function retornaMesesEntreFechas($fechaInicio, $fechaFin){
        $diferencia = -1;
        $fecha1 = new \DateTime($fechaInicio);
        $fecha2 = new \DateTime($fechaFin);
        $diff = $fecha1->diff($fecha2);
        $diferencia = ($diff->y * 12) + $diff->m;
        return $diferencia;
    }
}


//DateUtils::ymdToXxxxx("2016-01-15","{d} {mes} de {ano}");





?>
