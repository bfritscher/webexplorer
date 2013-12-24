<style type="text/css">
h1 span{
    font-weight: bold;
    border: 1px solid #003D4C;
    padding: 2px;
}

table tr.warning td{
    background-color: #FF6600;
}

div.ids a{
    display: inline-block;
    margin-left: 2px;
    margin-bottom: 2px;
}

div.ids a.point-1{
    background-color: #FFFFFF;
}
div.ids a.point0{
    background-color: #FF0000;
}
div.ids a.point1{
    background-color: #00FF00;
}
#batchEditDialog{
    display:none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(33, 33, 33, 0.6);
}
#batchEditForm {
    width: 50%;
    margin: 15% auto;
    background-color: white;
    padding: 10px;
    border: 1px solid black;    
}
#batchEditForm .close{
    float: right;
}

</style>
<?php echo $this->Html->link('rendu', array('action' => 'rendu')); ?>
<h1>WebExplorer TP duplicates:
<?php echo $name; ?> -
<?php
foreach(array('html', 'css', 'js') as $t){
    if($t == $type){
        echo "<span>$t</span> ";
    }else{
        echo $this->Html->link($t, array('action' => 'duplicates', $name, $t)) . ' ';
    }
}
?></h1>

<p>Variantes: <?php echo count($tps)?></p>
<table>
<tr>
    <th>Nombre</th>
    <th>Ids</th>
    <th>Action</th>
</tr>
<?php
foreach($tps as $tp):
    $tp = $tp[0];
    $row_class = (($tp['avg'] > 0 and $tp['avg'] < 1) ? 'warning' : '');
    $tp_ids = explode(',', $tp['tp_id']);
    $evaluateurs = explode(',', $tp['evaluateur']);
    $points = explode(',', $tp['point']);
    $students = explode(',', $tp['students']);
?>
<tr class="<?php echo $row_class;?>">
    <td><?php echo $tp['count']?></td>
    <td>
    	<div class="ids">
        <?php 
        for($i=0; $i < count($tp_ids); $i++){
            $id = $tp_ids[$i];
            $point = $points[$i];
            $evaluateur = $name_lookup[$evaluateurs[$i]] . "\n" . $students[$i];
            echo $this->Html->link($id, array('action'=>'eval', $id), array('class' => "point$point", 'title' => $evaluateur));
        }
        ?>
        </div>
    </td>
    <td>
        <a href="#" onclick="$('#WebpageMd5').val('<?php echo $tp['md5']?>');$('#tp_ids').html($(this).parent().parent().find('div.ids').clone());$('#batchEditDialog').show();return false;">Corriger tout</a>
    </td>
</tr>
<?php
endforeach;
?>
</table>
<?php echo $this->Form->create(null, array('url' => array('action'=>'duplicates', $name, $type))); ?>
<?php echo $this->Form->input('text', array('type'=>'textarea')); ?>
<?php echo $this->Form->end('comparer'); ?>

<div id="batchEditDialog">
<?php echo $this->Form->create(null, array('url' => array('action'=>'batch_eval', $name, $type), 'id' => 'batchEditForm')); ?>
<a href="#" class="close" onclick="$('#batchEditDialog').hide();">X</a>
<?php echo $this->Form->input('replace', array('options' =>array('only_null', 'all'))); ?>
<?php echo $this->Form->input('point', array('options' =>array(0,1))); ?>
<?php echo $this->Form->input('comment', array('type'=>'textarea')); ?>
<?php echo $this->Form->input('md5', array('type'=>'hidden')); ?>
<?php echo $this->Form->submit('corriger tout'); ?>
<div id="tp_ids"></div>
<?php echo $this->Form->end(); ?>
</div>