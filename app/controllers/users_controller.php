<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('Auth');
	var $scaffold = 'admin';
	var $uses = array('User');
	
	function beforeFilter() {
		parent::beforeFilter();
        $this->Auth->allow('login', 'register');
		$this->Auth->logoutRedirect = array(Configure::read('Routing.admin') => false, 'controller' => 'sqlexplorer', 'action' => 'index');
	}
	
	function login(){
	}
    
    function register() {
        if (!empty($this->data)) {
            if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password_confirm'])) {
                $this->User->create();
                if($this->User->save($this->data)) {
                    $this->Auth->login($this->data);
                    $this->redirect('/admin/index');
                }
            }
        }
    }
	
	function admin_login(){
		$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin'=>false));
	}
	
	function logout(){
		$this->redirect($this->Auth->logout());
	}
	
	
	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}
	
}
?>
