<?php
class WebpageTp extends AppModel {
	var $name = 'WebpageTp';
	var $order = 'WebpageTp.name ASC';
	var $belongsTo = array(
		'WebpageSnapshot' => array(
			'className' => 'WebpageSnapshot',
			'foreignKey' => 'webpage_snapshot_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Evaluator' => array(
			'className' => 'User',
			'foreignKey' => 'evaluator_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function getNextTpForUser($name, $user){
		//get next non eval for user evaluator
		$tp = $this->find('first', array('conditions'=>array('WebpageTp.evaluator_id' => $user,
																			'WebpageTp.name' => $name,
																			'WebpageTp.point' => null)));
		//if empty assign new one to user
		if(!$tp){
			$dataSource = $this->getDataSource();
			$dataSource->begin($this);
			$tp = $this->find('first', array('conditions'=>array('WebpageTp.evaluator_id' => null,
																				'WebpageTp.name' => $name,
																				'WebpageTp.point' => null)));
			if($tp){
				$this->id = $tp['WebpageTp']['id'];
				$this->saveField('evaluator_id', $user);
			}
			$dataSource->commit($this);
		}
		return $tp;
	}
}
?>