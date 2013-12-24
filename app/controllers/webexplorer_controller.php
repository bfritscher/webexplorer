<?php
//Configure::write('debug', 2);
class WebexplorerController extends AppController {

	var $tp = array('tp1' => '2014-12-30 17:00');

	var $name = 'Webexplorer';
	var $scaffold = 'admin';
	var $uses = array('Webpage', 'WebpageSnapshot', 'WebpageTp');
	
	function beforeFilter() {
		parent::beforeFilter();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->Session->write('savedPost', $this->data);
            if(isset($this->params['prefix'])){
                $prefix = $this->params['prefix'];
            }else{
                $prefix = '';
            }
			$this->Session->write('savedPostOrigin', $prefix);
            $name = $this->__cleanupName($name);
            $this->Session->write('savedPostName', $name);
        }
        
        $this->Auth->allow('view');
	}
	
	function index(){
		$this->set('webpages', $this->Webpage->find('all', array('conditions'=>array('Webpage.user_id' => $this->Auth->user('matricule')))));
		$this->set('webpage_tps', $this->WebpageTp->find('all',
								array('conditions' => array('WebpageTp.user_id' => $this->Auth->user('matricule')))));

		$this->render('index', 'webpageeditor');
	}
	
	function admin_index(){
		$this->set('webpages', $this->Webpage->find('all', array('conditions'=>array('Webpage.user_id' => 'admin'))));
		$this->render('admin_index',  'webpageeditor');
	}   
	
    function exemples(){
        $this->set('webpages', $this->Webpage->find('all', array('conditions'=>array('Webpage.user_id' => 'public'))));
		$this->layout = 'webpageeditor';
    }
    
	function edit($name=null){
		$this->__common_edit($name, $this->Auth->user('matricule'));
	}
	
	function admin_edit($name=null){
		$this->__common_edit($name, 'admin');
	}
	
	function __cleanupName($name){
		return trim(strtolower($name));
	}
	
	function __common_edit($name, $user){
        $name = $this->__cleanupName($name);
		if ($name) {
			$this->data = $this->Webpage->find('first', array('conditions'=>array('Webpage.user_id'=> $user, 'Webpage.name'=>$name)));
            if(!$this->data){
                $this->Session->setFlash("Page $name non trouvée!");
                $this->set('name', $name);
                $this->render('not_found', 'webpageeditor');
            }else{
				$this->__handleLayoutPreviewMode();
				$this->set('isSaveEnabled', true);
				$this->set('isTpSubmitEnabled', $this->__isTpEnabled($name));
				$this->set('isReviewEnabled', false);
                
                //limit drop down list to same section
                $group = preg_split('/_/', $this->data['Webpage']['name']);
                if(count($group) == 1){
                    $current_group = '';
                }else{
                    $current_group = $group[0];
                }
                $current_group .= '%';
                $webpages = $this->Webpage->find('list', array('fields' => array('Webpage.name', 'Webpage.name'),
																	  'conditions'=>array('Webpage.user_id' => $user,
                                                                      'Webpage.name LIKE' => $current_group
                                                                      )));
                
                $this->set('webpages', $webpages);
                $this->render('edit', 'webpageeditor');                                                                      
            }
		}else{
			$this->redirect(array('action' => 'index'));
		}
	}
    
	function rendu($name){
		$user = $this->Auth->user('matricule');
        $name = $this->__cleanupName($name);
		if ($name) {
			$this->data = $this->WebpageTp->find('first', array('conditions'=>array('WebpageTp.user_id'=> $user, 'WebpageTp.name'=>$name)));
            $this->data['Webpage'] = $this->data['WebpageSnapshot'];
			if(!$this->data){
                $this->Session->setFlash("Page $name non trouvée!");
				$this->redirect(array('action' => 'index'));
            }else{
				$this->__handleLayoutPreviewMode();
				$this->set('isSaveEnabled', false);
				$this->set('isTpSubmitEnabled', false);
				$this->set('isReviewEnabled', false);
                $this->render('edit', 'webpageeditor');                                                                      
            }
		}else{
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function admin_rendu($filter = 'tp'){
        $condition = array('WebpageTp.name LIKE' => "$filter%");
		$results = $this->WebpageTp->find('all', array(
                'conditions' => $condition,
				'fields' => array('WebpageTp.name', 'Evaluator.first_name', 'WebpageTp.point', 'COUNT(*)', 'textcat_all("WebpageTp"."id"' . " || ',') as ids"),
				'group' => 	array('WebpageTp.name', 'Evaluator.first_name', 'WebpageTp.point'),
				'order' => array('WebpageTp.name DESC', 'Evaluator.first_name', 'WebpageTp.point')
			));
		
		$this->set('results', $results);
	}
	
    function admin_duplicates($name, $type = 'html'){
        if(empty($name) or !in_array($type, array('html', 'css', 'js'))){
			$this->redirect(array('action' => 'rendu'));
		}      
        
        $this->set('type', $type);
        $this->set('name', $name);
        
        //lookup names of evaluators
        $result = $this->WebpageTp->query(
            "SELECT DISTINCT u.matricule, u.first_name || ' ' || u.last_name as name 
FROM webpage_tps tp JOIN users u ON tp.evaluator_id = u.matricule
WHERE name = '$name'"
        );
        $name_lookup = array(' '=>'none');
        foreach($result as $entry){
            $name_lookup[$entry[0]['matricule']] = $entry[0]['name'];
        }
        $this->set('name_lookup', $name_lookup);
        
        //get tps
        $query = "SELECT md5_$type AS md5, count(*), AVG(point),
array_to_string(array_agg(COALESCE(point, -1)), ',') point,
array_to_string(array_agg(COALESCE(evaluator_id, ' ')), ',') evaluateur,
array_to_string(array_agg(id), ',') tp_id,
array_to_string(array_agg(first_name || ' ' || last_name),',') students
FROM tps_md5
WHERE name = '$name' ";
        
        if($this->data){
            $query .= "AND md5_$type = '" . md5(preg_replace('/\s/', '', $this->data['Webpage']['text'])) . "' ";
        }
        
        $query .= "GROUP BY md5_$type
ORDER BY count(*) DESC";
        
        $this->set('tps', $this->WebpageTp->query($query));
    }
    
    function admin_statstime($name){
        if(empty($name)){
			$this->redirect(array('action' => 'rendu'));
		}    
        $query = "SELECT to_char(ws.created, 'yyyy-mm-dd HH24:00') as hour, COUNT(*),
array_to_string(array_agg(tp.id), ',') tp_id 
FROM webpage_tps tp JOIN webpage_snapshots ws ON tp.webpage_snapshot_id = ws.id
WHERE tp.name = '$name'
GROUP BY hour
ORDER BY hour";
        $this->set('name', $name);
        $this->set('tps', $this->WebpageTp->query($query));
    }
    
	function admin_result($name){
		if(empty($name)){
			$this->redirect(array('action' => 'rendu'));
		}
		$this->set('tps', $this->WebpageTp->find('all', array(
					'conditions' => array('WebpageTp.name' => $name)
				)
			)
		);
		$this->layout = 'ajax';
		$this->RequestHandler->respondAs('csv', array('index'=>1, 'attachment' => $name . '.csv'));
	}
	
	function admin_eval($name){
		if(empty($name)){
			$this->redirect(array('action' => 'rendu'));
		}
		if(is_numeric($name)){
			//get  by WebpageTp by id
			$tp = $this->WebpageTp->read(null, $name);
			if(!$tp){
				$this->Session->setFlash("TP with id: $name not found!");
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$user = $this->Auth->user('matricule');
			$name = $this->__cleanupName($name);
			$tp = $this->WebpageTp->getNextTpForUser($name, $user);
			//if empty all corrected or assigned
			if(!$tp){
				$this->Session->setFlash("Pas de TP $name, non corrigé ou non assigné.");
				$this->redirect(array('action' => 'rendu'));
			}

		}
		$tp['Webpage'] = $tp['WebpageSnapshot'];
		$this->data = $tp;
		$this->__handleLayoutPreviewMode();
		$this->set('isSaveEnabled', false);
		$this->set('isTpSubmitEnabled', false);
		$this->set('isReviewEnabled', true);
		$this->render('edit', 'webpageeditor');
	}
	
    function admin_batch_eval($name, $type){
        if(empty($name) or !in_array($type, array('html', 'css', 'js'))){
			$this->redirect(array('action' => 'rendu'));
		}  
        $query = "UPDATE webpage_tps tp
SET point = " . $this->data['Webpage']['point'] . ",
comment = '" . pg_escape_string($this->data['Webpage']['comment']) . "',
evaluator_id = '" . $this->Auth->user('matricule') . "'
FROM webpage_snapshots w
WHERE w.id = tp.webpage_snapshot_id
AND md5(regexp_replace($type, '\\\\s', '', 'g'))  = '" . $this->data['Webpage']['md5'] ."'";
        if($this->data['Webpage']['replace'] == '0'){
            $query .= " AND point IS NULL";
        }
        $this->autoRender = false;
        $this->WebpageTp->query($query);
        $this->redirect(array('action' => 'duplicates', $name, $type));
        
    }
    
	function admin_eval_save(){
		$this->autoRender = false;
		$this->data['WebpageTp']['evaluator_id'] = $this->Auth->user('matricule');
		if($this->WebpageTp->save($this->data)) {
			$this->redirect(array('action' => 'eval', $this->data['WebpageTp']['name']));
		}else{
			$this->Session->setFlash("erreur");
			$this->redirect(array('action' => 'rendu'));
		}
	}
	
	function __handleLayoutPreviewMode(){
		if(!$this->Session->check('Preview.mode')){
			$this->Session->write('Preview.mode', 'preview-single');
		}
		if(isset($this->params['named']['layout']) && in_array($this->params['named']['layout'], array('single', 'right', 'bottom'))){
			$this->Session->write('Preview.mode', 'preview-' . $this->params['named']['layout']);
		}
		$this->set('preview_mode', $this->Session->read('Preview.mode'));
	}
	
	function __isTpEnabled($name){
		return array_key_exists($name, $this->tp) && time() <= strtotime($this->tp[$name]);
	}
	
	function check($id){
		$this->autoRender = false;
		$json = array();
		$json['valid'] = $this->__isHtmlAndCssValid($id);
		echo json_encode($json);
	}
	
	function debug_check($id){
	$this->autoRender = false;		
		//check HTML
		$soap = file_get_contents("http://localhost/w3c-validator/check?uri=http://hec.unil.ch/info1ere/webexplorer/view/$id/html;output=soap12");

		$doc = simplexml_load_string($soap);
		$doc->registerXPathNamespace('m', 'http://www.w3.org/2005/10/markup-validator');

		$nodes = $doc->xpath('//m:markupvalidationresponse/m:validity');
		$html_validity = strval($nodes[0]) == "true";
		
		echo "<xmp>$soap</xmp>";
		/*
		$nodes = $doc->xpath('//m:markupvalidationresponse/m:errors/m:errorcount');
		$errorcount = strval($nodes[0]);
		$nodes = $doc->xpath('//m:markupvalidationresponse/m:errors/m:errorlist/m:error');
		foreach ($nodes as $node) {
			$nodes = $node->xpath('m:line'); 
			$line = strval($nodes[0]);
			$nodes = $node->xpath('m:col');
			$col = strval($nodes[0]);
			$nodes = $node->xpath('m:message');
			$message = strval($nodes[0]);
			$nodes = $node->xpath('m:source');
			$source = strval($nodes[0]);
			var_dump($source);
		}
		*/
		//check CSS
		
		$soap = file_get_contents("http://130.223.168.179:8180/css-validator/validator?uri=http://hec.unil.ch/info1ere/webexplorer/view/$id/html&profile=css3&output=soap12");

		$doc = simplexml_load_string($soap);
		$doc->registerXPathNamespace('m', 'http://www.w3.org/2005/07/css-validator');

		$nodes = $doc->xpath('//m:cssvalidationresponse/m:validity');
		$css_validity = strval($nodes[0]) == "true";
		
		
		echo "<xmp>$soap</xmp>";
		/*
		$nodes = $doc->xpath('//m:cssvalidationresponse/m:result/m:errors/m:errorcount');
		$errorcount = strval($nodes[0]);
		$nodes = $doc->xpath('//m:cssvalidationresponse/m:result/m:errors/m:errorlist/m:error');
		foreach ($nodes as $node) {
			$nodes = $node->xpath('m:line'); 
			$line = strval($nodes[0]);
			$nodes = $node->xpath('m:context');
			$context = strval($nodes[0]);
			$nodes = $node->xpath('m:message');
			$message = strval($nodes[0]);
		}
		*/
	}
	
	function __isHtmlAndCssValid($id){
	    return true;
        //CONFIG the right external SERVER and remove the return above
		$this->autoRender = false;		
		//check HTML
		$soap = file_get_contents("http://localhost/w3c-validator/check?uri=http://localhost/info1ere/webexplorer/view/$id/html;output=soap12");

		$doc = simplexml_load_string($soap);
		$doc->registerXPathNamespace('m', 'http://www.w3.org/2005/10/markup-validator');

		$nodes = $doc->xpath('//m:markupvalidationresponse/m:validity');
		$html_validity = strval($nodes[0]) == "true";
		/*
		echo "<xmp>$soap</xmp>";
		$nodes = $doc->xpath('//m:markupvalidationresponse/m:errors/m:errorcount');
		$errorcount = strval($nodes[0]);
		$nodes = $doc->xpath('//m:markupvalidationresponse/m:errors/m:errorlist/m:error');
		foreach ($nodes as $node) {
			$nodes = $node->xpath('m:line'); 
			$line = strval($nodes[0]);
			$nodes = $node->xpath('m:col');
			$col = strval($nodes[0]);
			$nodes = $node->xpath('m:message');
			$message = strval($nodes[0]);
			$nodes = $node->xpath('m:source');
			$source = strval($nodes[0]);
			var_dump($source);
		}
		*/
		//check CSS
		
		$soap = file_get_contents("http://localhost:8180/css-validator/validator?uri=http://localhost/info1ere/webexplorer/view/$id/html&profile=css3&output=soap12");

		$doc = simplexml_load_string($soap);
		$doc->registerXPathNamespace('m', 'http://www.w3.org/2005/07/css-validator');

		$nodes = $doc->xpath('//m:cssvalidationresponse/m:validity');
		$css_validity = strval($nodes[0]) == "true";
		
		/*
		echo "<xmp>$soap</xmp>";
		$nodes = $doc->xpath('//m:cssvalidationresponse/m:result/m:errors/m:errorcount');
		$errorcount = strval($nodes[0]);
		$nodes = $doc->xpath('//m:cssvalidationresponse/m:result/m:errors/m:errorlist/m:error');
		foreach ($nodes as $node) {
			$nodes = $node->xpath('m:line'); 
			$line = strval($nodes[0]);
			$nodes = $node->xpath('m:context');
			$context = strval($nodes[0]);
			$nodes = $node->xpath('m:message');
			$message = strval($nodes[0]);
		}
		*/
		return $html_validity and $css_validity; 
	}
	
	function save($name=null){
		$this->__common_save($name, $this->Auth->user('matricule'));
	}
	
	function admin_save($name=null){
		$this->__common_save($name, 'admin');
	}
    
    function __common_save($name, $user){
        $name = $this->__cleanupName($name);
        if($_SERVER['REQUEST_METHOD'] == 'GET' && $this->Session->check('savedPost') &&  $this->Session->check('savedPostOrigin') && $this->Session->check('savedPostName')){
            if(isset($this->params['prefix'])){
                $prefix = $this->params['prefix'];
            }else{
                $prefix = '';
            }
            if($this->Session->read('savedPostOrigin') == $prefix && $this->Session->read('savedPostName') == $name){
				$this->data = $this->Session->read('savedPost');
			}
			$this->Session->delete('savedPostOrigin');
            $this->Session->delete('savedPostName');
            $this->Session->delete('savedPost');
        }
        
        if ($name && $name !='') {        
            if(!$this->data['Webpage']['id'] ){
                //check if already exists
                $webpage = $this->Webpage->find('first', array('conditions'=>array('Webpage.user_id'=> $user, 'Webpage.name'=>$name)));
                if($webpage){
                    $this->data = $webpage;
                }else{
                    $this->Webpage->create();
                    $this->data['Webpage']['name'] = $name;
                }
            }
            $this->data['Webpage']['user_id'] = $user;
            if ($this->Webpage->save($this->data)) {
                if($this->RequestHandler->isAjax()){
                    $this->autoRender = 'false'; 
                    echo "ok";
                    exit();
                }else{
					$this->Session->setFlash('Modifications enregistrées');
					//do nothing more if content is empty
					
                    if((isset($this->data['Webpage']['html']) && $this->data['Webpage']['html']) || (isset($this->data['Webpage']['css']) && $this->data['Webpage']['css']) || (isset($this->data['Webpage']['js']) && $this->data['Webpage']['js'])){
                        //if tp submit
						$isTpSubmit = isset($this->data['savetp']);
						if($isTpSubmit){
							//check active tp and deadline
							if($this->__isTpEnabled($name)){
								//check if tp is valid
                                /* DISABLE TP VALIDATION */
								if(!$this->__isHtmlAndCssValid($this->Webpage->id)){
									$this->Session->setFlash("Erreur! La syntaxe utilisée dans le TP n'est pas valide.");
									$this->redirect(array('action' => 'edit', $name));	
								}
							} else {
								//error tp not open
								$this->Session->setFlash("Il n'est plus possible de rendre le TP $name!");
								$this->redirect(array('action' => 'edit', $name));
							}
						}			
						//if normal save or tp valid
						//create snapshot
						$this->WebpageSnapshot->create();
						$this->WebpageSnapshot->set('webpage_id', $this->Webpage->id);
						$this->WebpageSnapshot->set('html', $this->data['Webpage']['html']);
						$this->WebpageSnapshot->set('css', $this->data['Webpage']['css']);
						$this->WebpageSnapshot->set('js', $this->data['Webpage']['js']);
						if($isTpSubmit){	
							$this->WebpageSnapshot->set('name', $name);
						}
						if($this->WebpageSnapshot->save()){
							if($isTpSubmit){		
								//check if tp has already been saved
								$tp = $this->WebpageTp->find('first', array('conditions'=>array('WebpageTp.user_id'=> $user, 'WebpageTp.name'=>$name)));
								if($tp){
									//TODO: lock update on some conditions?
								}else{
									$tp = $this->WebpageTp->create();
									$tp['WebpageTp']['name'] = $name;
									$tp['WebpageTp']['user_id'] = $user;
								}
								$tp['WebpageTp']['webpage_snapshot_id'] = $this->WebpageSnapshot->id;
								if($this->WebpageTp->save($tp)){
									$this->Session->setFlash('TP rendu!');
									//TODO redirect to tp viewer?			
									$this->redirect(array('action' => 'index'));										
								}else{
									$this->Session->setFlash("Erreur lors de l'enregistrement du TP.");
								}
							}
						}else{
							$this->Session->setFlash('Error saving snapshot!');
						}
                    }
                }
            } else {
                if($this->RequestHandler->isAjax()){
                    $this->autoRender = 'false'; 
                    echo "error";
                    exit();
                } else {
                    $this->Session->setFlash('Erreur lors de l\'enregistrement.');
                }
            }
            $this->redirect(array('action' => 'edit', $name));
        }else{
            $this->Session->setFlash("Impossible de créer une page avec le nom '$name'");
            $this->redirect(array('action' => 'index'));
        }
    }
	
	function rename($id, $newname){
		$newname = $this->__cleanupName($newname);
		$webpage = $this->Webpage->read(null, $id);
		if($webpage['Webpage']['user_id'] == $this->Auth->user('matricule')){
			$this->Webpage->save(array('Webpage'=>array('name'=> $newname)));
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_rename($id, $newname){
		$newname = $this->__cleanupName($newname);
		$this->Webpage->id = $id;
		$this->Webpage->save(array('Webpage'=>array('name'=> $newname)));
		$this->redirect(array('action' => 'index'));
	}
    
    function duplicate($id, $newname){
        if($this->Session->read('User.admin')){
            $this->__copyPage($id,  $this->Auth->user('matricule'), $newname);
            $this->redirect(array('action' => 'index'));
        }
	}
    
	function admin_duplicate($id, $newname){
        $this->__copyPage($id, 'admin', $newname);
        $this->redirect(array('action' => 'index'));
	}
    
    function admin_sendtopublic($id, $newname){
        $this->__copyPage($id, 'public', $newname);
        $this->redirect(array('action' => 'index'));
    }
    
    function copytolocal($id, $newname){
        //security check if page is really public
        $webpage = $this->Webpage->read('user_id', $id);
        if($webpage && $webpage['Webpage']['user_id'] == 'public'){
            $this->__copyPage($id, $this->Auth->user('matricule'), $newname);
        }
        $this->Session->setFlash("Erreur impossible de copier la page");
        $this->redirect(array('action' => 'index'));
    }

   
    function __copyPage($id, $newuser=null, $newname=null){
        $webpage = $this->Webpage->read(null, $id);
        if($webpage){
            //set defaults
            if(!$newuser){
                $newuser = $webpage['Webpage']['user_id'];
            }
            if(!$newname){
                $newname = $webpage['Webpage']['name'];
            }
            $newname = $this->__cleanupName($newname);
            //check destination
            $new_webpage = $this->Webpage->find('first', array('conditions' => array('user_id' => $newuser,
                                                                                'name' => $newname)));
            if($new_webpage){
                //create snapshot before overriding existing page
                $this->WebpageSnapshot->create();
                $this->WebpageSnapshot->set('webpage_id', $new_webpage['Webpage']['id']);
                $this->WebpageSnapshot->set('html', $new_webpage['Webpage']['html']);
                $this->WebpageSnapshot->set('css', $new_webpage['Webpage']['css']);
                $this->WebpageSnapshot->set('js', $new_webpage['Webpage']['js']);    
                $this->WebpageSnapshot->set('name', 'auto-snapshot before update through copy');
                $this->WebpageSnapshot->save();
                //update
                $webpage['Webpage']['id'] = $new_webpage['Webpage']['id'];
            }else{
                //create new
                $webpage['Webpage']['id'] = null;
            }
            $webpage['Webpage']['name'] = $newname;
            $webpage['Webpage']['user_id'] = $newuser;
            $this->Webpage->save($webpage);
        }
    }
	
	function admin_view($name, $extension=null){
        $name = $this->__cleanupName($name);
		if(!$extension){
			$extension = 'html';
		}
		$webpage = $this->Webpage->find('first', array('conditions'=>array('Webpage.user_id'=>'admin', 'Webpage.name'=>$name)));
		$this->__renderWebpage($webpage, $extension);
	}
    
    function admin_viewtp($tpid){
        $tp = $this->WebpageTp->read(null, $tpid);
        $webpage['Webpage']['html'] = $tp['WebpageSnapshot']['html'];
        $webpage['Webpage']['css'] = $tp['WebpageSnapshot']['css'];
        $webpage['Webpage']['js'] = $tp['WebpageSnapshot']['js'];
        $webpage['User']['full_name'] = $tp['User']['full_name'];
        $this->__renderWebpage($webpage, 'html');
        $this->render('admin_view');
    }
	
	function view($id, $extension=null){
        $id = $this->__cleanupName($id);
        if(!$extension){
			$this->redirect(array('action' => 'view', $id, "html"));
		}
		if(is_numeric($id)){
			$cond = array('Webpage.id'=>$id);
		}else{
			if( $this->Auth->user('matricule')) {
				$cond = array('Webpage.user_id' => $this->Auth->user('matricule'),
						 'Webpage.name' => $id);
			}else{
				die('no webpage found');
			}
		}
		$webpage = $this->Webpage->find('first', array('conditions' => $cond));
		$this->__renderWebpage($webpage, $extension);
		$this->render('admin_view');
	}
	
	function __renderWebpage($webpage, $extension){
		if(!$webpage){
			die("no webpage found");
		}
		switch($extension){
			case 'js':
				$this->RequestHandler->respondAs('js');
				$data = "/* JS file */\n";
				$data .= $webpage['Webpage']['js'];
				break;
		
			case 'css':
				$this->RequestHandler->respondAs('css');
				$data = "/* CSS file */\n";
				$data .= $webpage['Webpage']['css'];
				break;
			case 'html':
			default:
				$data = '';
				$headend = strpos($webpage['Webpage']['html'], '</head');
				if($headend){
					$data .= substr($webpage['Webpage']['html'], 0, $headend);    
					if($webpage['Webpage']['css']){
						$data .= '<style type="text/css">' . "\n<!-- ";
						$data .= $webpage['Webpage']['css'];
						$data .= " -->\n</style>\n";
					}
					if($webpage['Webpage']['js']){
						$data .= '<script type="text/javascript">' . "\n";
						$data .= "//<![CDATA[\n";
						$data .= $webpage['Webpage']['js'];
						$data .= "//]]>\n";
						$data .= "</script>\n";
					}				
					$data .= substr($webpage['Webpage']['html'], $headend);    
				}else{
					$data .= $webpage['Webpage']['html'];    
				}
				$data .= "\n<!-- " . str_replace('--', '-', $webpage['User']['full_name']). " -->";
				break;
		}
		$this->set('data', $data);
		$this->layout = 'ajax';
	}
	
	function snapshots($name=null){
		$this->__common_snapshots($name, $this->Auth->user('matricule'));
	}
	
	function admin_snapshots($name=null){
		$this->__common_snapshots($name, 'admin');
	}
	
	function __common_snapshots($name, $user){
		$name = $this->__cleanupName($name);
		$snapshots = $this->WebpageSnapshot->find('all',
			array('conditions'=>array('Webpage.user_id'=> $user,
									  'Webpage.name'=> $name))
              );
		$this->set('snapshots', $snapshots);
		$this->render('snapshots');
	}
    
    function zip($name){
        $this->__common_zip($name, $this->Auth->user('matricule'));
    }
    
    function admin_zip($name){
		$this->__common_zip($name, 'admin');
	}
    
    function __common_zip($name, $user){
        $name = $this->__cleanupName($name);
		if ($name) {
			$webpage = $this->Webpage->find('first', array('conditions'=>array('Webpage.user_id'=> $user, 'Webpage.name'=>$name)));
            if(!$webpage){
                $this->Session->setFlash("Page $name non trouvée!");
                $this->set('name', $name);
                $this->render('not_found', 'webpageeditor');
            }else{
                $this->autoRender = false;
                $zip = new ZipArchive();
                $filename = $user . "_" . $name .".zip";
                $zip->open($filename, ZIPARCHIVE::OVERWRITE);
                $zip = $this->__addWebpageToZip($webpage, $zip);
          		$zip->close();
                $this->__forceDownload($filename);
                @unlink($filename);
            }
		}else{
			$this->redirect(array('action' => 'index'));
		}
    
    }
    
    function __addWebpageToZip($webpage, $zip){
        $name = $this->__cleanupName($webpage['Webpage']['name']);
        $data = '';
        $headend = strpos($webpage['Webpage']['html'], '</head');
        if($headend){
            $data .= substr($webpage['Webpage']['html'], 0, $headend);    
            
            if($webpage['Webpage']['css']){
                $data .= '<link rel="stylesheet" type="text/css" href="' . $name . '.css" />' . "\n";
                $zip->addFromString ("$name.css", $webpage['Webpage']['css']);
            }
            if($webpage['Webpage']['js']){
                $data .= ' <script language="javascript" type="text/javascript" src="' . $name . '.js"></script>' . "\n";                        
                $zip->addFromString ("$name.js", $webpage['Webpage']['js']);
            }				
            $data .= substr($webpage['Webpage']['html'], $headend);    
        }else{
            $data .= $webpage['Webpage']['html'];    
        }
        $data = str_replace('"/info1ere', '"http://hec.unil.ch/info1ere', $data);
        $zip->addFromString ("$name.html", $data);
        return $zip;
    }
    
    function zipall($name){
        $this->__common_zipall($this->Auth->user('matricule'));
    }
    
    function admin_zipall($name){
		$this->__common_zipall('admin');
	}
    
    function __common_zipall($user){
        $webpages = $this->Webpage->find('all', array('conditions'=>array('Webpage.user_id'=> $user)));
        $this->autoRender = false;
        $zip = new ZipArchive();
        $filename = $user . "_webexplorer.zip";
        $zip->open($filename, ZIPARCHIVE::OVERWRITE);
        foreach($webpages as $webpage){
            $zip = $this->__addWebpageToZip($webpage, $zip);
        }
        $zip->close();
        $this->__forceDownload($filename);
        @unlink($filename);
        
    }
    
    function __forceDownload($archiveName) {
		$headerInfo = '';
		 
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		// Security checks
		if( $archiveName == "" ) {
			echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
			exit;
		} 
		elseif ( ! file_exists( $archiveName ) ) {
			echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
			exit;
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($archiveName));
		readfile("$archiveName");
		
	 }
    
	function restore($snapshot_id, $name){
		$name = $this->__cleanupName($name);
		$snapshot = $this->WebpageSnapshot->read(null, $snapshot_id);
		if($snapshot){
			$this->Webpage->id = $snapshot['Webpage']['id'];
			$this->Webpage->set('html', $snapshot['WebpageSnapshot']['html']);
			$this->Webpage->set('css', $snapshot['WebpageSnapshot']['css']);
			$this->Webpage->set('js', $snapshot['WebpageSnapshot']['js']);
			$this->Webpage->save();
			$this->redirect(array('action'=>'edit', $name));
		}else{
			die('not found');
		}
	}
	
	function admin_restore($snapshot_id, $name){
		$this->restore($snapshot_id, $name);
	}
	
	function delete($arg){
		$user_id = $this->Auth->user('matricule');
		$cond = array('Webpage.user_id'=> $user_id);
		if(is_numeric($arg)){
			$cond['Webpage.id'] = $arg;
		}else{
			$cond['Webpage.name'] = $arg;
		}
		$webpage = $this->Webpage->find('first', array('fields' => array('Webpage.id'),'conditions'=> $cond));
		$id = $webpage['Webpage']['id'];
		if(!$this->Webpage->delete($id)){
			$this->Session->setFlash('Erreur lors de la supression.');
		}
		$this->redirect('/webexplorer');
	}
	
	function admin_delete($id){
		$this->Webpage->delete($id);
		$this->redirect('/admin/webexplorer');
	}
    
    function admin_stats(){
        $this->set('stats', $this->Webpage->query("SELECT name, count(*), textcat_all(id || ',') as ids FROM webpages GROUP BY name ORDER BY count(*) desc"));
    
    }
}