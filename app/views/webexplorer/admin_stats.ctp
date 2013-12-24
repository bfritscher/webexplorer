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
</style>
<h1><span>Web</span> Explorer<span> Admin</h1>
<table>
<tr>
<th>name</th>
<th>count</th>
</tr>
<?php
$sum = 0;
foreach($stats as $name){
?>
<tr>
	<td><?php echo $name[0]['name'];?>
        <div>
        <?php 
        foreach(explode(',', $name[0]['ids']) as $id){
            echo $this->Html->link($id, array('admin'=>false, 'action'=>'view', $id));
        }
        ?>
        </div>
    </td>
	<td><?php echo $name[0]['count']; $sum+=$name[0]['count'];?></td>
</tr>
<?php
}
?>
</table>
Total pages: <?php echo $sum;?>