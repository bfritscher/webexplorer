<style type="text/css">
.valid_true, .valid_false{
	width: 10px;
	height: 10px;
}
.valid_true{
	background-color: green;
}
.valid_false{
	background-color: red;
}
</style>
<h1><span>Web</span> Explorer<span> Admin</span></h1>
<p><a href="#" onclick="var name = prompt('Nom de la page?');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'save'));?>/'+name;}">Créer une nouvelle page</a>
- <?php echo $this->Html->link('Statistiques', array('action'=>'stats'));?>
- <?php echo $this->Html->link('Tp rendu', array('action'=>'rendu'));?>
- <?php echo $this->Html->link("Tout télécharger zip", array('action'=>'zipall'));?>
- <?php echo $this->Html->link("Exemples à copier", array('admin'=>false,'action'=>'exemples'));?>
- <?php echo $this->Html->link('Perso', array('admin'=>false,'action'=>'index')); ?>

</p>
<label>Filtre <input type="text" id="filter"/></label>
<table>
<tr>
	<th>Page</th>
	<th>Actions</th>
	<th>Dernière modification</th>
	<th><a href="#" onclick="validate();">valid</a></th>
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
	<?php echo $this->Html->link("afficher", array('action'=>'view', $webpage['Webpage']['name']));?>
	<a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'rename'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">renommer</a>
	<a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'duplicate'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">dupliquer</a>
    <a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('action'=>'sendtopublic'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">sendToPublic</a>
    <a href="#" onclick="var name = prompt('Nom de la page?', '<?php echo $webpage['Webpage']['name'];?>');if(name){location.href = '<?php echo $this->Html->url(array('admin'=>false, 'action'=>'duplicate'));?>/<?php echo $webpage['Webpage']['id']?>/'+name;}">sendToPerso</a>
    <?php echo $this->Html->link("supprimer", array('admin'=>true, 'action'=>'delete', $webpage['Webpage']['id']), null, "Êtes-vous sur de vouloir supprimer la page ".$webpage['Webpage']['name']." ?");?>
    <?php echo $this->Html->link("télécharger zip", array('action'=>'zip', $webpage['Webpage']['name']));?>
	</td>
	<td><?php echo $webpage['Webpage']['modified'];?></td>
	<td id="id_<?php echo $webpage['Webpage']['id']; ?>" class="valid"></td>
</tr>
<?php
}
?>
</table>
<script type="text/javascript">
	function validate(){
		$('.valid').each(function(index, element){
			var id = $(element).attr('id').split('_')[1];
			$.getJSON('<?php echo $this->Html->url(array('admin'=>false, 'action'=>'check'))?>/' + id, function(data){
				$('#id_' + id).empty().append('<div class="valid_' + data.valid + '"></div>');
			});
			
		});
	}
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