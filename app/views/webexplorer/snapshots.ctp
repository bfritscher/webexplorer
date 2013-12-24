<ul>
<?php
	foreach($snapshots as $snapshot){
		echo '<li>' . $this->Html->link($snapshot['WebpageSnapshot']['created'] . ' ' . $snapshot['WebpageSnapshot']['name'],
		array('action' => 'restore', $snapshot['WebpageSnapshot']['id'],  $snapshot['Webpage']['name'])) . '</li>';
	
	}
?>
</ul>