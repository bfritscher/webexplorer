<?php
class User extends AppModel {

	var $name = 'User';
	var $displayField = 'full_name';
	var $primaryKey = 'matricule'; 
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		//sql specific (mysql must enable PIPES_AS_CONCAT or use concat)
		$this->virtualFields['full_name'] = sprintf("%s.first_name || ' ' || %s.last_name", $this->alias, $this->alias);
	}
	

	var $hasMany = array(
    'Webpage' => array(
			'className' => 'Webpage',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'Webpage.name ASC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
    'WebpageTp' => array(
			'className' => 'WebpageTp',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'WebpageTp.name ASC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
}
?>