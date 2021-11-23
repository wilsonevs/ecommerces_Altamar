<?php
namespace Cm;

class GoogleVisualization {

    public static function import(){
        $code='
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    	<script type="text/javascript">google.load("visualization", "1", {packages:["corechart"]});</script>
        ';

        return trim($code);
    }

    public static function drawPie($p){


    }

    public static function drawBars($p){
    	return self::drawLinesAndBars("bars",$p);
    }

    public static function drawLines($p){
    	return self::drawLinesAndBars("lines",$p);
    }


    public static function drawLinesAndBars($mode,$p){
    	$p["name"] = isset($p["name"]) ? $p["name"]:uniqid();

        $code="";
        $code.="var data = new google.visualization.DataTable();\n";
        $code.="data.addColumn('string', '{$p["xAxisTitle"]}');\n";


        foreach( $p["series"] as $s){
            $code.="data.addColumn('number', '{$s["title"]}');\n";
        }

        $code.="data.addRows(".count($p["xAxisLabels"]).");\n";

        foreach($p["xAxisLabels"] as $k=>$v){
            $code.="data.setValue({$k}, 0, '{$v}');\n";

            $i=1;
            foreach($p["series"] as $s){
                $code.="data.setValue({$k}, {$i}, {$s["data"][$k]});\n";
                $i++;
            }
        }

        $i=1;
        foreach( $p["series"] as $s){
        	if( isset($s["format"]) ){
        		$code.="var formatter = new google.visualization.NumberFormat({prefix: '$ ', negativeColor: 'red', negativeParens: true,fractionDigits:0});\n";
        		$code.="formatter.format(data, {$i});\n";
        	}
        	$i++;

        }


        if( $mode=="bars"){
        	$code.="var chart = new google.visualization.ColumnChart(document.getElementById('{$p["name"]}'));\n";
        }
        else {
        	$code.="var chart = new google.visualization.LineChart(document.getElementById('{$p["name"]}'));\n";
        }

        $code.="chart.draw(data, {
        width: '100%', height: 400, title: '{$p["title"]}',
        curveType:'function',
        hAxis: {
        	title: '{$p["xAxisTitle"]}',
        	titleTextStyle: {color: 'red'},
        	textStyle: { fontSize:9 }
    	}
    	});\n";

        return self::html($p["name"],$code);
    }

    private static function html($name,$jsCode){
    	ob_start();
    	?>
	    <script type="text/javascript">
    	  google.load("visualization", "1", {packages:["corechart"]});

      	$(document).ready(function(){
          	<?php echo $jsCode;?>
      	});
    	</script>
    	<div id="<?php echo $name;?>" style="border:1px solid gray;"></div>
    	<?php
    	$tmp = ob_get_contents();
    	ob_end_clean();
    	return $tmp;
    }
}


?>
