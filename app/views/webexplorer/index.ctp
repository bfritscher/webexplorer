<h1><span>Web</span> Explorer<span> <?php echo $this->Session->read('Auth.User.full_name')?></span></h1>
<p>
<a href="#" onclick="var name = prompt('Nom de la page?');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'save'));?>/'+name;}">Créer une nouvelle page</a>
-
<?php echo $this->Html->link("Tout télécharger zip", array('action'=>'zipall'));?>
-
<?php echo $this->Html->link("Exemples à copier", array('action'=>'exemples'));?>
-
<?php
if(in_array($this->Session->read('Auth.User.username'), explode(';',SUPER_ADMIN))){
    echo $this->Html->link("Admin", array('admin'=>true,'action'=>'index'));
}
?>
</p>
<?php if(in_array($this->Session->read('Auth.User.username'), explode(';',SUPER_ADMIN))){ ?>
<label>Filtre <input type="text" id="filter"/></label>
<script type="text/javascript" src="/info1ere/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#filter').bind('keyup', function(e){
			var f = $(this).val();
			$('tr.document').each(function(index, tr){
				var $tr = $(tr);
				$tr.toggle($tr.find('td.name').text().indexOf(f) > -1);
			});
			$('tr.header').each(function(index, tr){
				var $tr = $(tr);
				
				$tr.toggle($tr.next().find('td:visible').length > 0);
			});
		});
	});
</script>
<?php } ?>
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
		<tr class="header">
			<td colspan="3"><h3><?php echo $current_group;?></h3></td>
		</tr>
		<?php
	}
?>
<tr class="document">
	<td class="name"><?php echo $webpage['Webpage']['name'];?></td>
	<td><?php echo $this->Html->link("modifier", array('action'=>'edit', $webpage['Webpage']['name']));?>
	<?php echo $this->Html->link("afficher", array('admin'=>false, 'action'=>'view', $webpage['Webpage']['name']));?>
	<a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'rename'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">renommer</a>
    <?php
        if($this->Session->read('User.admin')):
    ?>
    <a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'duplicate'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">duplicate</a>
    <a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('admin'=>true, 'action'=>'duplicate'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">sendToAdmin</a>
    <?php    
        endif;
    ?>
    
    	
    
    <?php echo $this->Html->link("supprimer", array('admin'=>false, 'action'=>'delete', $webpage['Webpage']['name']), null, "Êtes-vous sur de vouloir supprimer la page ".$webpage['Webpage']['name']." ?");?>
    <?php echo $this->Html->link("télécharger zip", array('action'=>'zip', $webpage['Webpage']['name']));?>
	</td>
	<td><?php echo $webpage['Webpage']['modified'];?></td>
</tr>
<?php
}
?>
</table>
<h2>TP rendu</h2>
<table style="clear:both;">
<tr>
	<th>Nom</th>
	<th>Date rendu</th>
	<th>Point*</th>
</tr>
<?php
foreach($webpage_tps as $webpage){

	echo "<tr><td>" . $this->Html->link($webpage['WebpageTp']['name'], array('action'=>'rendu', $webpage['WebpageTp']['name'])) . "</td><td>" .
	$webpage['WebpageSnapshot']['created']. "</td><td>" .
	(is_null($webpage['WebpageTp']['point']) ? 'pas corrigé' : $webpage['WebpageTp']['point']) . "</td><td>" ;
}
?>
</table>
<p>* Seul la note affichée sous moodle compte pour le calcule du point de l'examen.</p>