<h1><span>Web</span> Explorer<span> Exemples</h1>
<p>
<?php echo $this->Html->link("retour", array('action'=>'index'));?>

</p>
<table>
<tr>
	<th>Page</th>
	<th>Actions</th>
	<th>Dernière modification</th>
</tr>
<?php
$current_group = "";
foreach($webpages as $webpage){
	$group = preg_split('/_/', $webpage['Webpage']['name']);
	if(count($group) == 1){
		$current_group = '';
	}
	if($group[0] !=  $current_group){
		$current_group = $group[0];
		?>
		<tr>
			<td colspan="3"><h3 id="<?php echo $current_group;?>"><?php echo $current_group;?></h3></td>
		</tr>
		<?php
	}
?>


<tr>
	<td><?php echo $webpage['Webpage']['name'];?></td>
	<td>
	<?php echo $this->Html->link("afficher", array('action'=>'view', $webpage['Webpage']['id']));?>
    <a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'copytolocal'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">copier dans mon espace pour modifier</a>
    <?php
        if($this->Session->read('User.admin')){
            echo $this->Html->link("supprimer", array('admin'=>true, 'action'=>'delete', $webpage['Webpage']['id']), null, "Êtes-vous sur de vouloir supprimer la page ".$webpage['Webpage']['name']." ?");
        }
    ?>
	</td>
	<td><?php echo $webpage['Webpage']['modified'];?></td>
</tr>
<?php
}
?>
</table>