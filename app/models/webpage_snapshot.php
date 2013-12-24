<?php
class WebpageSnapshot extends AppModel {
	var $name = 'WebpageSnapshot';
	var $order = 'created DESC';
	var $belongsTo = array(
		'Webpage' => array(
			'className' => 'Webpage',
			'foreignKey' => 'webpage_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>