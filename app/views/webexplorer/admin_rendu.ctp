<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
</script>
<style type="text/css">
a{
    margin-right: 5px;
}
tr div{
    display:none;
}
tr:hover div{
    display: block;
}
table{
	margin-bottom: 40px;
}

a:hover{
	color:red;
}

th a {
	display:inline;
	text-decoration: underline;
}

.ids a{
	float:left;
}

tr{
clear:both;
}
</style>
<h1><span>Web</span> Explorer<span> Admin</h1>
<?php echo $this->Html->link('2013', array('action' => 'rendu')); ?>
<?php echo $this->Html->link('2012', array('action' => 'rendu', '2012')); ?>
<?php echo $this->Html->link('2011', array('action' => 'rendu', '2011')); ?>

<?php

$ctx = new StdClass(); 
$ctx->this = $this;
$ctx->tp = "";
$ctx->sum = 0;
$ctx->sum_evaluator = 0;
$ctx->evaluator = "init";
$ctx->chart = "";

function tp_open($ctx){
	echo '<table>';
	echo '<tr><th colspan="2">' . $ctx->tp . ' ' . $ctx->this->Html->link('corriger', array('action'=>'eval', $ctx->tp)) . 
	' ' . $ctx->this->Html->link('result.csv', array('action'=>'result', $ctx->tp)) .
    ' ' . $ctx->this->Html->link('doublons', array('action'=>'duplicates', $ctx->tp)) .
    ' ' . $ctx->this->Html->link('stats_time', array('action'=>'statstime', $ctx->tp)) .
    '</th></tr>';
	$ctx->chart = "(function () { var data = new google.visualization.DataTable(); data.addColumn('string', 'Evaluator'); data.addColumn('number', 'Copies');";
}

function tp_close($ctx){
	echo '<tr><td>Total</td><td>' . $ctx->sum . '</td></tr>';
	echo '</table>';
	echo '<div id="chart_div_'. $ctx->tp .'"></div>';
	$ctx->sum = 0;
	$ctx->evaluator = "init";
	$ctx->chart .= "var chart = new google.visualization.PieChart(document.getElementById('chart_div_" . $ctx->tp . "')); chart.draw(data, {width: 450, height: 250});})();";	
	echo '<script type="text/javascript">' . $ctx->chart . '</script>';
}

function evaluator_open($ctx){
	echo '<tr><th colspan="2">' . $ctx->evaluator . '</th></tr>';
}

function evaluator_close($ctx){		
	echo '<tr class="evaluator-total"><td>Sous-Total</td><td>' . $ctx->sum_evaluator . '</td></tr>';
	$ctx->chart .= "data.addRow(['" . $ctx->evaluator . "', " . $ctx->sum_evaluator . "]);";
	$ctx->sum += $ctx->sum_evaluator;
	$ctx->sum_evaluator = 0;
	
}

foreach($results as $entry){
	if($ctx->tp != $entry['WebpageTp']['name']){
		if($ctx->tp != ""){
			evaluator_close($ctx);
			tp_close($ctx);
		}
		$ctx->tp = $entry['WebpageTp']['name'];
		tp_open($ctx);
	}
	
	if($ctx->evaluator != $entry['Evaluator']['first_name']){
		if($ctx->evaluator != "init"){
			evaluator_close($ctx);
		}
		$ctx->evaluator = is_null($entry['Evaluator']['first_name']) ? 'Non assigné' : $entry['Evaluator']['first_name'];
		evaluator_open($ctx);
	}
?>
<tr>
	<td><?php echo $entry['WebpageTp']['point']?>
	<div class="ids">
        <?php 
        foreach(explode(',', $entry[0]['ids']) as $id){
            echo $this->Html->link($id, array('action'=>'eval', $id));
        }
        ?>
        </div>
	</td>
	<td><?php echo $entry[0]['count'];?></td>
</tr>
<?php
	$ctx->sum_evaluator+=$entry[0]['count'];
}
evaluator_close($ctx);
tp_close($ctx);
?>