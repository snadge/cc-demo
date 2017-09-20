<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

define("REST_USER", "demouser");
define("REST_PWD", "demopass");

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array(
		'Session',
#		'RequestHandler',
		'Auth',
		'Security'
	);
	
	public function beforeFilter() {
		if(in_array($this->params['controller'],array('test'))){
			// For RESTful web service requests, we check the name of our contoller
			$this->Auth->allow();
			// this line should always be there to ensure that all rest calls are secure
			/* $this->Security->requireSecure(); */
			$this->Security->unlockedActions = array('index', 'test');

			// crude basic authentication - otherwise have to implement custom auth class
			if($_SERVER['PHP_AUTH_USER'] != REST_USER || $_SERVER['PHP_AUTH_PW'] != REST_PWD) {
                            $data = array (
                                'status' => 400,
                                'message' => "You are not authorized to access that location.",
                            );
                            $this->set('data', $data);
                            $this->set('_serialize', 'data');

                            $this->viewClass = 'Json';
                            $this->render();
			} 

			// force json extension
			$this->RequestHandler->ext = 'json';
			
		}else{
			// setup out Auth
			$this->Auth->allow();			
		}
    }
}
