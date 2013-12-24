<?php
class Webpage extends AppModel {
	var $name = 'Webpage';
	var $order = 'Webpage.name ASC';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>