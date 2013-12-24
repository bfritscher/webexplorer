<?php
class AppController extends Controller {
	var $components = array('Auth', 'RequestHandler', 'Session');
	
	function beforeFilter(){
		if ( $this->RequestHandler->isAjax() ) {
			Configure::write('debug',0);
		}
		parent::beforeFilter();
		if(isset($this->params['prefix']) && $this->params['prefix']== 'admin'){
			$this->Auth->authorize = 'controller';
			$this->layout = 'admin';
		}
	}
	
	function isAuthorized() {
	    return in_array($this->Session->read('Auth.User.username'), explode(';',SUPER_ADMIN));
    }
}
?>