<?php $html->script('jquery.flot.0.7',array('inline' => false)); ?>
<?php $html->script('jquery.flot.resize.min',array('inline' => false)); ?>
<?php $html->script('jquery.flot.navigate.min',array('inline' => false)); ?>
<?php $html->scriptStart(array('inline' => false));?>

var d = [
<?php
foreach($tps as $tp){
    $tp = $tp[0];
    echo '[' . strtotime($tp['hour']) . '000,' . $tp['count'] . '],';
}
?>
];

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#fee',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}

$(document).ready(function() {	
    $.plot($("#chart"), [d], {
        series: {
            lines: { show: false},
            points: { show: true },
            bars: { show: true }
        },
        grid: { hoverable: true, clickable: true},
        xaxis: {
            mode: "time",
            timeformat: "%d %Hh",
            tickSize: [3, "hour"],
            minTickSize: [1, "hour"]
        },
        zoom: {
            interactive: false
        },
        pan: {
            interactive: true
        }
    });
    var previousPoint = null;
    $("#chart").bind("plothover", function (event, pos, item) {
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                
                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];
                
                showTooltip(item.pageX, item.pageY, y);
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    })
    .bind("plotclick", function (event, pos, item) {
        if (item) {
            $('tr').removeClass('selected');
            $('#t_' + item.datapoint[0]).addClass('selected');
            $('html,body').animate({scrollTop: $('#t_' + item.datapoint[0]).offset().top}, 'fast');
        }
    });
    
});




<?php $html->scriptEnd();?>
<style type="text/css">
div.ids a{
    display: inline-block;
    margin-left: 2px;
    margin-bottom: 2px;
}

table tr.selected td{
    background-color: red;
}

</style>
<?php echo $this->Html->link('rendu', array('action' => 'rendu')); ?>
<h1>WebExplorer TP timestats: <?php echo $name; ?></h1>

<div id="chart" style="width:100%;height:400px;"></div>

<table>
<tr>
    <th>Hour</th>
    <th>Count</th>
    <th>tp ids</th>
</tr>
<?php
foreach($tps as $tp):
    $tp = $tp[0];
    $tp_ids = explode(',', $tp['tp_id']);
?>
<tr id="<?php echo "t_".strtotime($tp['hour'])."000"?>">
    <td><?php echo $tp['hour']?></td>
    <td><?php echo $tp['count']?></td>
    <td>
    	<div class="ids">
        <?php 
        for($i=0; $i < count($tp_ids); $i++){
            $id = $tp_ids[$i];
            echo $this->Html->link($id, array('action'=>'eval', $id));
        }
        ?>
        </div>
    </td>
</tr>
<?php
endforeach;
?>
</table>
</div>